<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\EstoqueMovimentacao;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\LoteOcorrencia;
use App\Models\Ordenha;
use App\Models\Plano;
use App\Models\Racao;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Isolamento entre contas em TODOS os endpoints que recebem um id (na URL,
 * no corpo ou num filtro): a conta "outro" nunca lê, altera ou apaga dados
 * da conta demo — nem pela API (token), nem pela web (sessão). No fim,
 * confere no banco que nada da conta demo mudou.
 */
class IsolamentoEntreContasTest extends TestCase
{
    use RefreshDatabase;

    private Cadastro $demo;
    private Cadastro $outro;
    private Fazenda $fazendaDemo;
    private Fazenda $fazendaOutro;
    private Lote $loteDemo;
    private Lote $loteOutro;
    private Racao $racaoDemo;
    private Cocho $cochoDemo;
    private Cocho $cochoOutro;
    private Plano $planoDemo;
    private Ordenha $ordenhaDemo;
    private string $tokenOutro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AgriFarmDemoSeeder::class);

        $this->demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
        $this->outro = Cadastro::where('email', 'outro@agrifarm.com.br')->firstOrFail();
        $this->fazendaDemo = Fazenda::where('nome_fazenda', 'Fazenda Boa Vista')->firstOrFail();
        $this->fazendaOutro = Fazenda::where('nome_fazenda', 'Fazenda do Outro')->firstOrFail();
        $this->loteDemo = Lote::where('nome_lote', 'Leiteiras A')->firstOrFail();
        $this->loteOutro = Lote::where('nome_lote', 'Lote do Outro')->firstOrFail();
        $this->racaoDemo = Racao::where('nome_racao', 'Ração Lactação 22%')->firstOrFail();
        $this->cochoDemo = $this->loteDemo->cochos()->firstOrFail();
        $this->cochoOutro = $this->loteOutro->cochos()->firstOrFail();
        $this->planoDemo = Plano::where('lote_id', $this->loteDemo->id)->firstOrFail();
        $this->ordenhaDemo = Ordenha::where('lote_id', $this->loteDemo->id)->firstOrFail();

        $this->tokenOutro = $this->postJson('/api/auth/login', ['email' => 'outro@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA])->json('token');
    }

    private function api(string $metodo, string $uri, array $dados = [])
    {
        $this->app['auth']->forgetGuards();

        return $this->withHeader('Authorization', 'Bearer ' . $this->tokenOutro)->json($metodo, '/api' . $uri, $dados);
    }

    private function fotografia(): array
    {
        return [
            'fazenda' => $this->fazendaDemo->fresh()->only(['nome_fazenda', 'cadastro_id']),
            'lote' => $this->loteDemo->fresh()->only(['nome_lote', 'quantidade_animais', 'peso_medio', 'racao_id', 'fazenda_id']),
            'racao' => $this->racaoDemo->fresh()->only(['nome_racao', 'estoque', 'preco']),
            'cocho' => $this->cochoDemo->fresh()->only(['capacidade', 'nivel_atual', 'lote_id']),
            'plano' => $this->planoDemo->fresh()?->only(['quantidade_recomendada', 'ativo']),
            'ordenha' => $this->ordenhaDemo->fresh()?->only(['quantidade_litros']),
            'ocorrencias' => LoteOcorrencia::where('lote_id', $this->loteDemo->id)->count(),
            'movimentacoes' => EstoqueMovimentacao::where('racao_id', $this->racaoDemo->id)->count(),
            'ordenhas' => Ordenha::where('lote_id', $this->loteDemo->id)->count(),
        ];
    }

    public function test_api_nao_deixa_outra_conta_tocar_nos_dados_por_id_na_url(): void
    {
        $antes = $this->fotografia();
        $hoje = now()->toDateString();
        $f = $this->fazendaDemo->id;
        $l = $this->loteDemo->id;
        $r = $this->racaoDemo->id;
        $c = $this->cochoDemo->id;
        $o = $this->ordenhaDemo->id;

        $casos = [
            ['GET', "/lotes/{$l}/ocorrencias"],
            ['GET', "/lotes/{$l}/ordenhas"],
            ['GET', "/lotes/{$l}/racoes-compativeis"],
            ['PUT', "/lotes/{$l}", ['nome_lote' => 'Invadido', 'fazenda_id' => $this->fazendaOutro->id, 'tipo_animal' => 'Bovino', 'quantidade_animais' => 1]],
            ['POST', "/lotes/{$l}/doenca", ['doenca' => 'X', 'quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lotes/{$l}/parto", ['quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lotes/{$l}/recuperacao", ['quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lotes/{$l}/eclosao", ['quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lotes/{$l}/postura", ['quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lotes/{$l}/ordenhas", ['data' => $hoje, 'horario' => '06:00', 'turno' => 'Manhã', 'quantidade_litros' => 10]],
            ['PUT', "/lotes/{$l}/ordenhas/{$o}", ['data' => $hoje, 'horario' => '06:00', 'turno' => 'Manhã', 'quantidade_litros' => 1]],
            ['DELETE', "/lotes/{$l}/ordenhas/{$o}"],
            ['POST', "/lotes/{$l}/abastecer-cochos", ['racao_id' => $r, 'quantidade' => 10]],
            ['PUT', "/racoes/{$r}", ['nome_racao' => 'Invadida', 'preco' => 1]],
            ['PUT', "/cochos/{$c}", ['capacidade' => 1]],
            ['POST', "/cochos/{$c}/abastecer", ['racao_id' => $r, 'quantidade' => 10]],
            ['GET', "/cochos/{$c}/status"],
            ['GET', "/cochos/{$c}/iot"],
            ['GET', "/cochos/{$c}/iot/historico"],
            ['PUT', "/planos-alimentares/{$this->planoDemo->id}", ['quantidade_recomendada' => 9]],
            ['POST', "/plano/lotes/{$l}/pesagem", ['peso_medio' => 1]],
            ['POST', "/plano/lotes/{$l}/vincular-racao", ['racao_id' => $r]],
        ];

        foreach ($casos as $caso) {
            [$metodo, $uri] = $caso;
            $resposta = $this->api($metodo, $uri, $caso[2] ?? []);
            $this->assertContains($resposta->status(), [403, 404], "{$metodo} {$uri} deveria ser recusado para outra conta, veio {$resposta->status()}: " . mb_substr($resposta->getContent(), 0, 300));
        }

        $this->assertEquals($antes, $this->fotografia(), 'Algum endpoint alterou dados da conta demo.');
    }

    public function test_api_nao_vaza_dados_por_filtro_de_fazenda_ou_lote(): void
    {
        $f = $this->fazendaDemo->id;
        $l = $this->loteDemo->id;

        foreach ([
            "/dashboard?fazenda_id={$f}",
            "/plano?fazenda_id={$f}",
            "/ordenha-leite?fazenda_id={$f}",
            "/relatorios?fazenda_id={$f}",
            "/relatorios/resumo?fazenda_id={$f}",
            "/relatorios/consumo?fazenda_id={$f}",
            "/relatorios/estoque?fazenda_id={$f}",
            "/relatorios/lotes?fazenda_id={$f}",
            "/relatorios/cochos?fazenda_id={$f}",
            "/relatorios/producao?fazenda_id={$f}",
            "/relatorios/consumo?lote_id={$l}",
            "/lotes?fazenda_id={$f}",
            "/cochos?fazenda_id={$f}",
            "/cochos?lote_id={$l}",
            "/racoes?fazenda_id={$f}",
            "/estoque?fazenda_id={$f}",
            "/estoque/movimentacoes?fazenda_id={$f}",
            "/estoque/movimentacoes?lote_id={$l}",
            "/planos-alimentares?fazenda_id={$f}",
            "/planos-alimentares?lote_id={$l}",
            "/alertas/resumo?fazenda_id={$f}",
        ] as $uri) {
            $resposta = $this->api('GET', $uri);
            $corpo = $resposta->getContent();
            if (in_array($resposta->status(), [403, 404, 422], true)) {
                continue;
            }
            $this->assertSame(200, $resposta->status(), "GET {$uri}: {$resposta->status()} " . mb_substr($corpo, 0, 300));
            foreach (['Leiteiras A', 'Engorda B', 'Ração Lactação 22%', 'Fazenda Boa Vista'] as $nomeDemo) {
                $this->assertStringNotContainsString($nomeDemo, $corpo, "GET {$uri} vazou \"{$nomeDemo}\" da conta demo.");
            }
        }
    }

    public function test_api_recusa_ids_alheios_no_corpo_da_requisicao(): void
    {
        $antes = $this->fotografia();
        $hoje = now()->toDateString();
        $racaoDemo = $this->racaoDemo->id;

        $casos = [
            // Lote próprio, ração da outra conta.
            ['POST', "/lotes/{$this->loteOutro->id}/abastecer-cochos", ['racao_id' => $racaoDemo, 'quantidade' => 10], 'racao_id'],
            ['POST', "/cochos/{$this->cochoOutro->id}/abastecer", ['racao_id' => $racaoDemo, 'quantidade' => 10], 'racao_id'],
            ['POST', "/plano/lotes/{$this->loteOutro->id}/vincular-racao", ['racao_id' => $racaoDemo], 'racao_id'],
            ['POST', '/planos-alimentares', ['lote_id' => $this->loteOutro->id, 'racao_id' => $racaoDemo, 'quantidade_recomendada' => 1, 'frequencia_diaria' => 1, 'data_inicio' => $hoje], 'racao_id'],
            ['POST', '/planos-alimentares', ['lote_id' => $this->loteDemo->id, 'racao_id' => $racaoDemo, 'quantidade_recomendada' => 1, 'frequencia_diaria' => 1, 'data_inicio' => $hoje], 'lote_id'],
            ['POST', '/estoque/saida', ['racao_id' => $racaoDemo, 'quantidade' => 1], 'racao_id'],
            ['POST', '/racoes', ['nome_racao' => 'Intrusa', 'fazenda_id' => $this->fazendaDemo->id, 'tipo_racao' => 'Bovino', 'preco' => 1, 'estoque' => 1], 'fazenda_id'],
            ['PUT', "/lotes/{$this->loteOutro->id}", ['nome_lote' => 'Lote do Outro', 'fazenda_id' => $this->fazendaDemo->id, 'tipo_animal' => 'Bovino', 'quantidade_animais' => 30], 'fazenda_id'],
            ['PUT', "/lotes/{$this->loteOutro->id}/ordenhas/{$this->ordenhaDemo->id}", ['data' => $hoje, 'horario' => '06:00', 'turno' => 'Manhã', 'quantidade_litros' => 1], null],
            ['DELETE', "/lotes/{$this->loteOutro->id}/ordenhas/{$this->ordenhaDemo->id}", [], null],
        ];

        foreach ($casos as [$metodo, $uri, $dados, $campo]) {
            $resposta = $this->api($metodo, $uri, $dados);
            $this->assertContains($resposta->status(), [403, 404, 422], "{$metodo} {$uri} aceitou id alheio ({$resposta->status()}): " . mb_substr($resposta->getContent(), 0, 300));
        }

        $this->assertSame(0, Racao::where('nome_racao', 'Intrusa')->count());
        $this->assertEquals($antes, $this->fotografia(), 'Algum endpoint alterou dados da conta demo usando um id no corpo.');
    }

    public function test_pagina_publica_nao_mostra_lotes_de_nenhuma_conta(): void
    {
        // Visitante: só totais por espécie (somando todas as contas).
        $visitante = $this->getJson('/animais-por-lote')->assertOk();
        foreach (['Leiteiras A', 'Engorda B', 'Lote do Outro', 'Holandês', 'Nelore'] as $privado) {
            $this->assertStringNotContainsString($privado, $visitante->getContent());
        }
        $this->assertContains('Bovino', collect($visitante->json('lotes'))->pluck('nome')->all());
        $this->assertSame((int) Lote::ativos()->sum('quantidade_animais'), $visitante->json('total'));
        $this->get('/')->assertOk()->assertDontSee('Leiteiras A');

        // Logado: os próprios lotes, nunca os da outra conta.
        $logado = $this->withSession(['usuario_id' => $this->outro->id])->getJson('/animais-por-lote')->assertOk();
        $this->assertStringContainsString('Lote do Outro', $logado->getContent());
        $this->assertStringNotContainsString('Leiteiras A', $logado->getContent());
    }

    public function test_web_nao_deixa_outra_conta_tocar_nos_dados(): void
    {
        $antes = $this->fotografia();
        $hoje = now()->toDateString();
        $l = $this->loteDemo->id;
        $r = $this->racaoDemo->id;
        $c = $this->cochoDemo->id;
        $o = $this->ordenhaDemo->id;
        $sessao = ['usuario_id' => $this->outro->id, 'usuario_nome' => $this->outro->responsavel, 'usuario_email' => $this->outro->email];

        $casos = [
            ['GET', "/fazenda/atualizar/{$this->fazendaDemo->id}"],
            ['GET', "/fazenda/{$this->fazendaDemo->id}"],
            ['PUT', "/fazenda/update/{$this->fazendaDemo->id}", ['nome_fazenda' => 'Invadida']],
            ['DELETE', "/fazenda/delete/{$this->fazendaDemo->id}"],
            ['GET', "/lote/atualizar/{$l}"],
            ['PUT', "/lote/update/{$l}", ['nome_lote' => 'Invadido']],
            ['DELETE', "/lote/delete/{$l}"],
            ['POST', "/lote/{$l}/morte", ['quantidade' => 1, 'causa' => 'X', 'data_ocorrencia' => $hoje]],
            ['POST', "/lote/{$l}/doenca", ['quantidade' => 1, 'doenca' => 'X', 'data_ocorrencia' => $hoje]],
            ['POST', "/lote/{$l}/parto", ['quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lote/{$l}/recuperacao", ['quantidade' => 1, 'data_ocorrencia' => $hoje]],
            ['POST', "/lote/{$l}/ordenha", ['data' => $hoje, 'horario' => '06:00', 'turno' => 'Manhã', 'quantidade_litros' => 10]],
            ['GET', "/lote/{$l}/ordenha/historico"],
            ['PUT', "/lote/{$l}/ordenha/{$o}", ['data' => $hoje, 'horario' => '06:00', 'turno' => 'Manhã', 'quantidade_litros' => 1]],
            ['DELETE', "/lote/{$l}/ordenha/{$o}"],
            ['POST', "/lote/{$l}/cochos/abastecer", ['racao_id' => $r, 'quantidade' => 10]],
            ['GET', "/racao/atualizar/{$r}"],
            ['GET', "/racao/{$r}"],
            ['PUT', "/racao/update/{$r}", ['nome_racao' => 'Invadida']],
            ['DELETE', "/racao/delete/{$r}"],
            ['GET', "/cocho/atualizar/{$c}"],
            ['PUT', "/cocho/update/{$c}", ['capacidade' => 1]],
            ['POST', "/cocho/{$c}/abastecer", ['racao_id' => $r, 'quantidade' => 10]],
            ['GET', "/cocho/{$c}/detalhes"],
            ['GET', "/cocho/{$c}/iot"],
            ['GET', "/cocho/{$c}/iot/historico"],
            ['POST', "/plano/lotes/{$l}/pesagem", ['peso_medio' => 1]],
            ['POST', "/plano/lotes/{$l}/vincular-racao", ['racao_id' => $r]],
            // Ração da demo no cocho/lote da conta "outro".
            ['POST', "/cocho/{$this->cochoOutro->id}/abastecer", ['racao_id' => $r, 'quantidade' => 10]],
            ['POST', "/lote/{$this->loteOutro->id}/cochos/abastecer", ['racao_id' => $r, 'quantidade' => 10]],
            ['PUT', "/lote/{$this->loteOutro->id}/ordenha/{$o}", ['data' => $hoje, 'horario' => '06:00', 'turno' => 'Manhã', 'quantidade_litros' => 1]],
            ['DELETE', "/lote/{$this->loteOutro->id}/ordenha/{$o}"],
        ];

        foreach ($casos as $caso) {
            [$metodo, $uri] = $caso;
            $resposta = $this->withSession($sessao)->call($metodo, $uri, $caso[2] ?? [], [], [], ['HTTP_ACCEPT' => 'application/json']);
            $status = $resposta->getStatusCode();
            $this->assertTrue(in_array($status, [302, 403, 404, 422], true), "{$metodo} {$uri} (web) respondeu {$status}: " . mb_substr($resposta->getContent(), 0, 300));
            if ($status === 200) {
                $this->assertStringNotContainsString('Leiteiras A', $resposta->getContent());
            }
        }

        // Telas com filtro de fazenda alheia não mostram nada da demo.
        foreach (['/dashboard', '/lotes', '/racao/listar', '/estoque/listar', '/plano/listar', '/cocho/listar', '/ordenha-leite', '/relatorios', '/alertas/resumo', '/ordenha-leite/historico', '/plano/lotes-consumo', '/plano/conversoes-alimentares', '/relatorios/ranking'] as $tela) {
            foreach (['fazenda_id', 'fazenda'] as $param) {
                $resposta = $this->withSession($sessao)->get($tela . '?' . $param . '=' . $this->fazendaDemo->id);
                if ($resposta->getStatusCode() !== 200) {
                    continue;
                }
                foreach (['Leiteiras A', 'Engorda B', 'Ração Lactação 22%'] as $nome) {
                    $this->assertStringNotContainsString($nome, $resposta->getContent(), "{$tela}?{$param}= vazou \"{$nome}\" da conta demo.");
                }
            }
        }

        $this->assertEquals($antes, $this->fotografia(), 'Alguma rota web alterou dados da conta demo.');
    }
}
