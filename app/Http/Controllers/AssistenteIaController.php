<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssistenteIaController extends Controller
{
    // Base de conhecimento do assistente — resume as telas e regras reais
    // do AgriFarm pra ele responder com o vocabulário e o funcionamento
    // certos do sistema, em vez de "chutar" genericamente.
    private const SYSTEM_PROMPT = <<<'PROMPT'
        Você é o Assistente do AgriFarm, um painel web de gestão de fazendas
        de pecuária. Responda em português do Brasil, de forma curta, direta
        e cordial (poucos parágrafos, sem enrolação).

        Você só ajuda com dúvidas sobre o próprio sistema AgriFarm — como
        usar as telas, o que cada número/gráfico significa, como cadastrar
        algo. Se perguntarem algo sem relação com o sistema, diga
        educadamente que só pode ajudar com o AgriFarm.

        O que você sabe sobre o sistema:

        - É um painel 100% web, sem sensor ou hardware: tudo é cadastro
          manual pelo navegador (computador, tablet ou celular).
        - Uma conta pode ter várias Fazendas, cada uma com seus próprios
          Lotes, Cochos, Rações e Estoque — os dados de uma fazenda nunca
          aparecem para outra conta.
        - Lote: um grupo de animais (tipo, sexo, finalidade — corte, leite,
          reprodução etc. — quantidade e peso médio).
        - Ração: cadastrada com fazenda, tipo de animal, sexo, finalidade,
          consumo diário (kg/dia), valor por kg e fornecedor. Só pode ser
          vinculada a um lote da MESMA fazenda, tipo e sexo (uma ração para
          "Equino Macho" nunca vincula a um lote "Bovino Fêmea").
        - Estoque: mostra o estoque de cada ração (quantidade, status
          OK/baixo/esgotado) direto da tela de Rações — pra repor ou
          ajustar, a edição acontece na própria tela de Rações.
        - Cocho: o comedouro físico de um lote, com nível de ração.
        - Plano Alimentar: tela central de consumo diário. Não precisa
          registrar alimentação manualmente — assim que um lote tem uma
          ração vinculada, o consumo diário dela é dividido automaticamente
          em manhã/tarde/noite e aparece na tabela "Consumo por Lote". O
          vínculo lote-ração é manual (o usuário escolhe qual ração vincular
          na tela, um vínculo por vez, e só rações ainda não vinculadas a
          outro lote aparecem como opção) — antes de vincular, o lote
          aparece como "Sem ração vinculada". A tela também mostra o
          gráfico dos últimos 7 dias, o resumo mensal, a distribuição de
          ração por tipo e a conversão alimentar (kg de ração / kg de peso
          vivo) por lote.
        - Dashboard: gráficos de consumo dos últimos 7 dias e resumo mensal
          por fazenda, além de alertas automáticos (estoque baixo/esgotado,
          cocho com problema, lote sem cocho ou sem alimentação no dia).
        - Relatórios: filtra por fazenda, lote, categoria de animal, tipo de
          ração e período; mostra gráfico de eficiência por lote; exporta em
          PDF.
        - Perfil: dados da conta (nome, e-mail, senha, foto) — alterações
          sensíveis (senha, e-mail) disparam um código de confirmação por
          e-mail.
        - Não exige treinamento nem conhecimento técnico prévio para usar.
        PROMPT;

    // gemini-2.5-flash foi descontinuado pelo Google para chaves novas (a
    // própria API retorna 404 e indica gemini-3.6-flash como substituto),
    // mas esse modelo "cheio" tem cota gratuita mínima (só 20 pedidos por
    // DIA — não por minuto, confirmado batendo em 429 de novo 26 minutos
    // depois de esgotar a cota). "flash-lite" é a variante pensada
    // justamente pra uso gratuito/alto volume, com cota diária bem maior.
    private const MODELO = 'gemini-3.1-flash-lite';
    private const MAX_TURNOS_HISTORICO = 6;
    private const URL_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';

    // ENVIAR PERGUNTA: recebe a mensagem do widget de chat flutuante e
    // responde chamando a API do Google Gemini. O histórico enviado pelo
    // front-end é só o desta conversa (mantido em memória no navegador) —
    // nada é salvo no banco.
    public function perguntar(Request $request)
    {
        $dados = $request->validate([
            'mensagem' => 'required|string|max:2000',
            'historico' => 'nullable|array|max:' . (self::MAX_TURNOS_HISTORICO * 2),
            'historico.*.papel' => 'required_with:historico|in:usuario,assistente',
            'historico.*.texto' => 'required_with:historico|string|max:2000',
        ]);

        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            Log::warning('Assistente IA: GEMINI_API_KEY não configurada.');

            return response()->json([
                'erro' => 'O assistente ainda não foi configurado — falta a chave da API. Avise um administrador do sistema.',
            ], 503);
        }

        // Gemini usa "model" (não "assistant") para o papel da IA no
        // histórico da conversa.
        $conteudos = collect($dados['historico'] ?? [])
            ->map(fn ($turno) => [
                'role' => $turno['papel'] === 'usuario' ? 'user' : 'model',
                'parts' => [['text' => $turno['texto']]],
            ])
            ->push(['role' => 'user', 'parts' => [['text' => $dados['mensagem']]]])
            ->values()
            ->all();

        try {
            // gemini-3.6-flash "pensa" antes de responder (gasta tokens de
            // raciocínio mesmo em perguntas simples) e, sob demanda alta do
            // lado do Google, isso pode passar de 30s — 30s estava cortando
            // respostas que teriam completado normalmente logo em seguida.
            $resposta = Http::withHeaders(['x-goog-api-key' => $apiKey])
                ->timeout(58)
                ->post(self::URL_BASE . self::MODELO . ':generateContent', [
                    'system_instruction' => ['parts' => [['text' => self::SYSTEM_PROMPT]]],
                    'contents' => $conteudos,
                    'generationConfig' => ['maxOutputTokens' => 1024],
                ]);

            if (!$resposta->successful()) {
                Log::error('Assistente IA: erro da API Gemini', [
                    'status' => $resposta->status(),
                    'mensagem' => $resposta->json('error.message'),
                ]);

                return response()->json([
                    'erro' => 'O assistente está indisponível no momento. Tente novamente em instantes.',
                ], 502);
            }

            $texto = collect($resposta->json('candidates.0.content.parts', []))
                ->pluck('text')
                ->implode('');

            if ($texto === '') {
                $texto = 'Não consegui gerar uma resposta agora. Pode reformular a pergunta?';
            }

            return response()->json(['resposta' => $texto]);
        } catch (\Throwable $e) {
            Log::error('Assistente IA: falha inesperada', ['mensagem' => $e->getMessage()]);

            return response()->json([
                'erro' => 'Algo deu errado ao falar com o assistente. Tente novamente.',
            ], 500);
        }
    }
}
