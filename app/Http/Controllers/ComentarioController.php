<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    /**
     * Página pública de avaliações (acessada pela landing page). Mostra o
     * feedback de todos os visitantes/usuários, não só do usuário logado.
     */
    public function listar()
    {
        $comentarios = Comentario::with('cadastro')
            ->orderByDesc('created_at')
            ->get();

        $totalComentarios = $comentarios->count();

        $mediaNotaBruta = $comentarios->whereNotNull('nota')->avg('nota');
        $notaMedia = $mediaNotaBruta !== null ? round($mediaNotaBruta, 1) : 0;
        $satisfacao = $totalComentarios > 0
            ? (int) round($notaMedia / 5 * 100)
            : 0;

        $inicioMesAtual = Carbon::now()->startOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $fimMesAnterior = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $comentariosMesAtual = $comentarios->filter(fn ($c) => $c->created_at->gte($inicioMesAtual));
        $comentariosMesAnterior = $comentarios->filter(
            fn ($c) => $c->created_at->between($inicioMesAnterior, $fimMesAnterior)
        );

        $totalComentariosMes = $comentariosMesAtual->count();

        $notaMediaMesAnterior = $comentariosMesAnterior->whereNotNull('nota')->avg('nota');
        $satisfacaoMesAnterior = $notaMediaMesAnterior ? ($notaMediaMesAnterior / 5 * 100) : null;

        $satisfacaoTendencia = $this->calcularTendencia($satisfacao, $satisfacaoMesAnterior);
        $comentariosTendencia = $this->calcularTendencia($totalComentariosMes, $comentariosMesAnterior->count());

        // Distribuição das notas (1 a 5 estrelas) + % positivo/neutro/negativo
        $comentariosComNota = $comentarios->whereNotNull('nota');
        $totalComNota = $comentariosComNota->count();

        $distribuicaoNotas = [];
        for ($i = 5; $i >= 1; $i--) {
            $qtd = $comentariosComNota->where('nota', $i)->count();
            $distribuicaoNotas[$i] = [
                'quantidade' => $qtd,
                'percentual' => $totalComNota > 0 ? round($qtd / $totalComNota * 100) : 0,
            ];
        }

        $qtdPositivas = $comentariosComNota->filter(fn ($c) => $c->nota >= 4)->count();
        $qtdNeutras = $comentariosComNota->filter(fn ($c) => $c->nota == 3)->count();
        $qtdNegativas = $comentariosComNota->filter(fn ($c) => $c->nota <= 2)->count();

        $percentPositivo = $totalComNota > 0 ? round($qtdPositivas / $totalComNota * 100) : 0;
        $percentNeutro = $totalComNota > 0 ? round($qtdNeutras / $totalComNota * 100) : 0;
        $percentNegativo = $totalComNota > 0 ? round($qtdNegativas / $totalComNota * 100) : 0;

        // Evolução da nota média nos últimos 6 meses
        $evolucaoMensal = [];
        for ($i = 5; $i >= 0; $i--) {
            $referencia = Carbon::now()->subMonthsNoOverflow($i);
            $doMes = $comentarios->filter(
                fn ($c) => $c->created_at->isSameMonth($referencia) && $c->created_at->isSameYear($referencia)
            )->whereNotNull('nota');

            $evolucaoMensal[] = [
                'mes' => ucfirst($referencia->locale('pt_BR')->translatedFormat('M/y')),
                'media' => $doMes->count() > 0 ? round($doMes->avg('nota'), 1) : null,
            ];
        }

        // Médias por categoria ("O que você achou?")
        $categoriasMedias = [];
        foreach (Comentario::CATEGORIAS as $coluna => $rotulo) {
            $avaliadas = $comentarios->whereNotNull($coluna);
            $categoriasMedias[$coluna] = [
                'rotulo' => $rotulo,
                'media' => $avaliadas->count() > 0 ? round($avaliadas->avg($coluna), 1) : null,
                'quantidade' => $avaliadas->count(),
            ];
        }

        $nomeVisitante = session('usuario_id')
            ? \App\Models\Cadastro::find(session('usuario_id'))?->responsavel
            : null;

        return view('avaliacoes', compact(
            'comentarios',
            'totalComentarios',
            'totalComentariosMes',
            'notaMedia',
            'satisfacao',
            'satisfacaoTendencia',
            'comentariosTendencia',
            'distribuicaoNotas',
            'totalComNota',
            'percentPositivo',
            'percentNeutro',
            'percentNegativo',
            'evolucaoMensal',
            'categoriasMedias',
            'nomeVisitante'
        ));
    }

    /**
     * Variação percentual entre o valor atual e o do mês anterior.
     * Retorna null quando não há base de comparação (mês anterior sem dados).
     */
    private function calcularTendencia(float $atual, ?float $anterior): ?array
    {
        if ($anterior === null || $anterior == 0) {
            return null;
        }

        $variacao = round((($atual - $anterior) / $anterior) * 100);

        return [
            'direcao' => $variacao > 0 ? 'up' : ($variacao < 0 ? 'down' : 'igual'),
            'valor' => abs($variacao),
        ];
    }

    public function salvar(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['nullable', 'string', 'max:150'],
            'mensagem' => ['required', 'string', 'max:2000'],
            'nota' => ['required', 'integer', 'between:1,5'],
            'nota_facilidade' => ['nullable', 'integer', 'between:1,5'],
            'nota_visual' => ['nullable', 'integer', 'between:1,5'],
            'nota_desempenho' => ['nullable', 'integer', 'between:1,5'],
            'nota_organizacao' => ['nullable', 'integer', 'between:1,5'],
            'nota_funcionalidades' => ['nullable', 'integer', 'between:1,5'],
            'nota_acessibilidade' => ['nullable', 'integer', 'between:1,5'],
        ], [
            'mensagem.required' => 'Escreva um comentário antes de enviar.',
            'mensagem.max' => 'O comentário pode ter no máximo 2000 caracteres.',
            'nota.required' => 'Selecione uma nota de 1 a 5 estrelas.',
            'nota.between' => 'A nota precisa ser de 1 a 5 estrelas.',
        ]);

        Comentario::create($dados + ['cadastro_id' => session('usuario_id')]);

        return redirect()->route('comentario.listar')->with('sucesso', 'Comentário enviado com sucesso! Obrigado pela sua avaliação.');
    }
}
