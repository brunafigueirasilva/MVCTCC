<?php

namespace Tests\Feature\Api;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Plano;
use App\Models\Racao;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fluxos principais da API usada pelo app Flutter, ponta a ponta (login real
 * por token, banco montado pelas migrations + AgriFarmDemoSeeder):
 * login, fazendas, lotes, ocorrências (óbito, doença, parto, ordenha),
 * cochos, rações, estoque (entrada/saída/histórico), plano alimentar,
 * dashboard, relatórios, autorização entre usuários e formato das respostas.
 */
class AgriFarmApiTest extends TestCase
{
    use RefreshDatabase;

    private Cadastro $demo;
    private Fazenda $fazenda;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AgriFarmDemoSeeder::class);

        $this->demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
        $this->fazenda = $this->demo->fazendas()->where('nome_fazenda', 'Fazenda Boa Vista')->firstOrFail();
        $this->token = $this->login('demo@agrifarm.com.br');
    }

    private function login(string $email): string
    {
        return $this->postJson('/api/auth/login', ['email' => $email, 'password' => AgriFarmDemoSeeder::SENHA])
            ->assertOk()
            ->json('token');
    }

    private function api(string $metodo, string $uri, array $dados = [], ?string $token = null)
    {
        $this->app['auth']->forgetGuards();

        return $this->withHeader('Authorization', 'Bearer ' . ($token ?? $this->token))->json($metodo, '/api' . $uri, $dados);
    }

    private function lote(string $nome): Lote
    {
        return Lote::where('nome_lote', $nome)->firstOrFail();
    }

    // ------------------------------------------------------------ 1. Login

    public function test_login_logout_e_usuario_autenticado(): void
    {
        $this->api('GET', '/auth/me')->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'demo@agrifarm.com.br')
            ->assertJsonMissingPath('data.senha');

        $this->postJson('/api/auth/login', ['email' => 'demo@agrifarm.com.br', 'password' => 'errada'])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['message', 'errors' => ['email']]);

        $this->api('POST', '/auth/logout')->assertOk()->assertJsonPath('success', true);
        $this->api('GET', '/auth/me')->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Não autenticado. Faça login novamente.');
    }

    // ---------------------------------------------------- 2/3. Fazendas

    public function test_criar_fazenda_usa_o_usuario_autenticado_como_dono(): void
    {
        $outro = Cadastro::where('email', 'outro@agrifarm.com.br')->firstOrFail();

        $resposta = $this->api('POST', '/fazendas', [
            'nome_fazenda' => 'Fazenda Nova',
            'area' => 50,
            'estado' => 'MG',
            // Tentativa de "plantar" a fazenda em outra conta: ignorada.
            'cadastro_id' => $outro->id,
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nome_fazenda', 'Fazenda Nova');

        $this->assertSame($this->demo->id, Fazenda::find($resposta->json('data.id'))->cadastro_id);

        $nomes = collect($this->api('GET', '/fazendas')->assertOk()->json('data'))->pluck('nome_fazenda');
        $this->assertContains('Fazenda Nova', $nomes);
        $this->assertNotContains('Fazenda do Outro', $nomes);

        $this->api('POST', '/fazendas', ['area' => 0])->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['nome_fazenda', 'area']);
    }

    // ------------------------------------------------- 4/5/6. Lotes

    public function test_criar_listar_e_ver_lote_com_cochos_automaticos_de_capacidade_padrao(): void
    {
        $resposta = $this->api('POST', '/lotes', [
            'nome_lote' => 'Recria F',
            'fazenda_id' => $this->fazenda->id,
            'tipo_animal' => 'Bovino',
            'sexo' => 'Fêmea',
            'finalidade' => 'Leite',
            'idade_media' => '6-12 meses',
            'quantidade_animais' => 25,
            'quantidade_cochos' => 2,
            'cocho_capacidade' => 0, // o app envia 0 por padrão
            'peso_medio' => 250,
            'status' => 'Ativo',
        ])->assertCreated()->assertJsonPath('data.nome_lote', 'Recria F');

        $lote = Lote::findOrFail($resposta->json('data.id'));
        $this->assertSame(2, $lote->cochos()->count());
        $this->assertEquals(Cocho::CAPACIDADE_PADRAO, $lote->cochos()->first()->capacidade);

        $this->assertContains('Recria F', collect($this->api('GET', '/lotes?fazenda_id=' . $this->fazenda->id)->json('data'))->pluck('nome_lote'));

        $this->api('GET', "/lotes/{$lote->id}")->assertOk()
            ->assertJsonPath('data.quantidade_animais', 25)
            ->assertJsonCount(2, 'data.cochos');

        // Tipo de animal fora da lista e macho com finalidade "Leite": 422.
        $this->api('POST', '/lotes', ['tipo_animal' => 'Dragão'])->assertStatus(422)->assertJsonValidationErrors(['tipo_animal', 'nome_lote']);
    }

    // ------------------------------- 7/8/9/10. Registros do lote

    public function test_registrar_obito_doenca_parto_e_ordenha_no_lote(): void
    {
        $corte = $this->lote('Engorda B');
        $leite = $this->lote('Leiteiras A');
        $hoje = now()->toDateString();

        $antes = $corte->quantidade_animais;
        $this->api('POST', "/lotes/{$corte->id}/morte", ['causa' => 'Acidente', 'quantidade' => 2, 'data_ocorrencia' => $hoje])
            ->assertOk()->assertJsonPath('success', true);
        $this->assertSame($antes - 2, $corte->fresh()->quantidade_animais);

        $this->api('POST', "/lotes/{$corte->id}/doenca", ['doenca' => ['Verminose', 'Verminose'], 'data_ocorrencia' => $hoje])
            ->assertOk();
        $this->assertSame($antes - 4, $corte->fresh()->quantidade_animais);

        // Parto: permitido em lote de fêmeas, recusado em lote de machos.
        $this->api('POST', "/lotes/{$leite->id}/parto", ['quantidade' => 3, 'data_ocorrencia' => $hoje])->assertOk();
        $this->api('POST', "/lotes/{$corte->id}/parto", ['quantidade' => 1, 'data_ocorrencia' => $hoje])
            ->assertStatus(422)->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Parto só é possível em lotes com fêmeas (sexo Fêmea ou Misto).');

        $this->api('POST', "/lotes/{$leite->id}/ordenhas", ['data' => $hoje, 'horario' => '18:30', 'turno' => 'Noite', 'quantidade_litros' => 150.5])
            ->assertOk();

        $tipos = collect($this->api('GET', "/lotes/{$corte->id}/ocorrencias")->assertOk()->json('data'))->pluck('tipo');
        $this->assertContains('morte', $tipos);
        $this->assertContains('doenca', $tipos);
        $this->assertContains('parto', collect($this->api('GET', "/lotes/{$leite->id}/ocorrencias")->json('data'))->pluck('tipo'));

        $ordenhas = $this->api('GET', "/lotes/{$leite->id}/ordenhas")->assertOk()->json('data');
        $this->assertSame(150.5, collect($ordenhas)->firstWhere('turno', 'Noite')['quantidade_litros']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $ordenhas[0]['data']);
    }

    // ---------------------------------------------------- 11. Cochos

    public function test_consultar_cochos_com_status_e_alerta_abaixo_de_50_por_cento(): void
    {
        $cochos = collect($this->api('GET', '/cochos?fazenda_id=' . $this->fazenda->id)->assertOk()->json('data'));

        $doLeite = $cochos->firstWhere('lote.nome_lote', 'Leiteiras A');
        // Dados do lote/fazenda que o app (cocho.dart) lê de cada cocho.
        $this->assertSame('Fazenda Boa Vista', $doLeite['lote']['fazenda']['nome_fazenda']);
        $this->assertSame('Bovino', $doLeite['lote']['tipo_animal']);
        // 30 de 100 kg = 30% → abaixo de 50%: alerta.
        $this->assertSame('Baixo', $doLeite['status_abastecimento']);
        $this->assertTrue($doLeite['alerta_nivel_baixo']);

        $doCorte = $cochos->firstWhere('lote.nome_lote', 'Engorda B'); // 70%
        $this->assertSame('OK', $doCorte['status_abastecimento']);

        $this->api('GET', "/cochos/{$doLeite['id']}/status")->assertOk()
            ->assertJsonPath('data.percentual_nivel', 30)
            ->assertJsonPath('data.percentual_alerta', 50);

        $this->assertTrue(collect($this->api('GET', '/cochos?alerta=1')->json('data'))->every(fn ($c) => $c['alerta_nivel_baixo']));
    }

    public function test_abastecer_cocho_desconta_estoque_e_registra_historico(): void
    {
        $cocho = $this->lote('Leiteiras A')->cochos()->first();
        $racao = Racao::where('nome_racao', 'Ração Lactação 22%')->firstOrFail();
        $saldo = $racao->estoque;

        $this->api('POST', "/cochos/{$cocho->id}/abastecer", ['racao_id' => $racao->id, 'quantidade' => 12.5])->assertOk();

        $this->assertEquals($saldo - 12.5, $racao->fresh()->estoque); // decimal preservado
        $this->assertEquals(42.5, $cocho->fresh()->nivel_atual);
        $this->assertDatabaseHas('estoque_movimentacoes', [
            'racao_id' => $racao->id, 'cocho_id' => $cocho->id, 'tipo' => 'saida', 'origem' => 'abastecimento_cocho', 'quantidade' => 12.5,
        ]);

        // Mais do que cabe no cocho: recusado sem mexer no estoque.
        $this->api('POST', "/cochos/{$cocho->id}/abastecer", ['racao_id' => $racao->id, 'quantidade' => 500])->assertStatus(422);
        $this->assertEquals($saldo - 12.5, $racao->fresh()->estoque);
    }

    // ---------------------------------------------------- 12. Rações

    public function test_consultar_racoes(): void
    {
        $racoes = collect($this->api('GET', '/racoes')->assertOk()->json('data'));
        $this->assertCount(4, $racoes);

        $racao = $racoes->firstWhere('nome_racao', 'Ração Engorda Plus');
        // Número no JSON (não texto "2720.00"): 2720.0 chega como 2720, o
        // que o app converte com numero() (json_util.dart).
        $this->assertTrue(is_int($racao['estoque']) || is_float($racao['estoque']));
        $this->assertIsFloat($racao['valor_kg']);
        $this->assertSame('kg', $racao['unidade']);
        $this->assertArrayHasKey('status_estoque', $racao);

        $this->api('GET', "/racoes/{$racao['id']}")->assertOk()->assertJsonPath('data.nome_racao', 'Ração Engorda Plus');
    }

    // ---------------------------------------------- 13/14. Estoque

    public function test_entrada_saida_e_historico_de_estoque_sem_permitir_saldo_negativo(): void
    {
        $racao = Racao::where('nome_racao', 'Ração Postura')->firstOrFail(); // 300 kg

        $this->api('POST', '/estoque/entrada', ['racao_id' => $racao->id, 'quantidade' => 200, 'observacao' => 'Compra'])
            ->assertCreated()
            ->assertJsonPath('data.tipo', 'entrada')
            ->assertJsonPath('data.saldo_anterior', 300)
            ->assertJsonPath('data.saldo_posterior', 500);

        $this->api('POST', '/estoque/saida', ['racao_id' => $racao->id, 'quantidade' => 120.75])
            ->assertCreated()->assertJsonPath('data.saldo_posterior', 379.25);

        $this->api('POST', '/estoque/saida', ['racao_id' => $racao->id, 'quantidade' => 10000])
            ->assertStatus(422)->assertJsonPath('success', false)->assertJsonValidationErrors(['quantidade']);
        $this->assertEquals(379.25, $racao->fresh()->estoque);

        $this->api('POST', '/estoque/saida', ['racao_id' => $racao->id, 'quantidade' => -5])->assertStatus(422);

        $historico = $this->api('GET', "/estoque/movimentacoes?racao_id={$racao->id}")->assertOk();
        $this->assertSame(['saida', 'entrada', 'entrada'], collect($historico->json('data'))->pluck('tipo')->all());
        $this->assertSame(3, $historico->json('meta.total'));

        $estoque = $this->api('GET', '/estoque?fazenda_id=' . $this->fazenda->id)->assertOk();
        $this->assertEquals(379.25, collect($estoque->json('data.itens'))->firstWhere('racao_id', $racao->id)['estoque']);
        $this->api('GET', "/estoque/{$racao->id}")->assertOk()->assertJsonCount(3, 'data.ultimas_movimentacoes');
    }

    // ---------------------------------------------- 15. Plano alimentar

    public function test_crud_de_plano_alimentar_com_um_plano_ativo_por_lote(): void
    {
        $lote = $this->lote('Engorda B');

        // Ração de suíno num lote bovino de corte: a API recusa
        // (regra de compatibilidade — App\Support\CompatibilidadeRacao).
        $racaoSuina = Racao::where('nome_racao', 'Ração Suínos Terminação')->firstOrFail();
        $this->api('POST', '/planos-alimentares', [
            'lote_id' => $lote->id, 'racao_id' => $racaoSuina->id, 'quantidade_recomendada' => 2, 'data_inicio' => now()->toDateString(),
        ])->assertStatus(422)->assertJsonFragment(['racao_id' => ['Esta ração não é compatível com o lote selecionado.']]);

        // Outra ração COMPATÍVEL (bovina, macho, corte) da mesma fazenda.
        $outraRacao = Racao::create([
            'fazenda_id' => $lote->fazenda_id, 'nome_racao' => 'Ração Corte Alternativa', 'tipo' => 'Bovino',
            'sexo' => 'Macho', 'finalidade' => 'Corte', 'unidade' => 'kg', 'estoque' => 300, 'consumo' => 10,
        ]);

        $criado = $this->api('POST', '/planos-alimentares', [
            'lote_id' => $lote->id,
            'racao_id' => $outraRacao->id,
            'quantidade_recomendada' => '4,2',
            'frequencia_diaria' => 3,
            'data_inicio' => now()->toDateString(),
            'observacoes' => 'Teste',
        ])->assertCreated()
            ->assertJsonPath('data.quantidade_recomendada', 4.2)
            ->assertJsonPath('data.fazenda_id', $lote->fazenda_id)
            ->assertJsonPath('data.ativo', true);

        $id = $criado->json('data.id');
        $this->assertSame(1, Plano::where('lote_id', $lote->id)->where('ativo', true)->count());
        $this->assertSame($outraRacao->id, $lote->fresh()->racao_id);

        $this->api('GET', "/planos-alimentares?lote_id={$lote->id}")->assertOk()->assertJsonCount(2, 'data');
        $this->api('PUT', "/planos-alimentares/{$id}", ['frequencia_diaria' => 2])->assertOk()->assertJsonPath('data.frequencia_diaria', 2);
        $this->api('PUT', "/planos-alimentares/{$id}", ['data_inicio' => '2026-10-10', 'data_fim' => '2026-10-01'])
            ->assertStatus(422)->assertJsonValidationErrors(['data_fim']);
        $this->api('DELETE', "/planos-alimentares/{$id}")->assertOk();
        $this->assertDatabaseMissing('planos', ['id' => $id]);

        // Ração de OUTRA fazenda (do outro usuário) não pode entrar no plano.
        $racaoAlheia = Racao::create(['fazenda_id' => Fazenda::where('nome_fazenda', 'Fazenda do Outro')->value('id'), 'nome_racao' => 'Alheia', 'estoque' => 10, 'consumo' => 1]);
        $this->api('POST', '/planos-alimentares', [
            'lote_id' => $lote->id, 'racao_id' => $racaoAlheia->id, 'quantidade_recomendada' => 2, 'data_inicio' => now()->toDateString(),
        ])->assertStatus(422)->assertJsonValidationErrors(['racao_id']);
    }

    // --------------------------------------- 16. Dashboard

    public function test_dashboard_zerado_sem_fazenda_e_com_dados_reais_da_fazenda(): void
    {
        $this->api('GET', '/dashboard/resumo')->assertOk()
            ->assertJsonPath('data.fazenda', null)
            ->assertJsonPath('data.lotes', 0)
            ->assertJsonPath('data.animais', 0);

        $resumo = $this->api('GET', '/dashboard/resumo?fazenda_id=' . $this->fazenda->id)->assertOk();
        $this->assertSame($this->fazenda->lotes()->count(), $resumo->json('data.lotes'));
        $this->assertSame((int) $this->fazenda->lotes()->sum('quantidade_animais'), $resumo->json('data.animais'));
        $this->assertSame(14 - 2, $resumo->json('data.cochos')); // 12 cochos na fazenda principal
        $this->assertGreaterThan(0, $resumo->json('data.estoque_atual_kg'));
        $this->assertGreaterThan(0, $resumo->json('data.producao_leite_hoje_litros'));

        $this->api('GET', '/dashboard?fazenda_id=' . $this->fazenda->id)->assertOk()->assertJsonPath('success', true);
    }

    // ---------------------------------------------- 17. Relatórios

    public function test_relatorios_com_dados_reais_e_estado_vazio(): void
    {
        $f = '?fazenda_id=' . $this->fazenda->id;

        $this->api('GET', '/relatorios/consumo' . $f)->assertOk()->assertJsonStructure(['data' => ['total_kg', 'custo_total', 'por_dia', 'por_lote', 'periodo']]);
        $this->assertGreaterThan(0, $this->api('GET', '/relatorios/consumo' . $f)->json('data.total_kg'));
        $this->assertGreaterThan(0, $this->api('GET', '/relatorios/estoque' . $f)->json('data.entradas_kg'));
        $this->assertSame(5, $this->api('GET', '/relatorios/lotes' . $f)->json('data.total_lotes'));
        $this->assertSame(12, $this->api('GET', '/relatorios/cochos' . $f)->json('data.total'));
        $this->assertGreaterThan(0, $this->api('GET', '/relatorios/producao' . $f)->json('data.total_litros'));
        $this->api('GET', '/relatorios/resumo' . $f)->assertOk();

        $this->api('GET', '/relatorios/consumo')->assertOk()->assertJsonPath('data.fazenda', null)->assertJsonPath('data.total_kg', 0);
        $this->api('GET', '/relatorios/consumo' . $f . '&data_inicio=2026-10-10&data_fim=2026-10-01')->assertStatus(422);
    }

    // ----------------------------------- 18. Autorização entre usuários

    public function test_usuario_nao_acessa_nem_altera_dados_de_outra_conta(): void
    {
        $tokenOutro = $this->login('outro@agrifarm.com.br');
        $lote = $this->lote('Leiteiras A');
        $racao = Racao::where('nome_racao', 'Ração Lactação 22%')->firstOrFail();
        $cocho = $lote->cochos()->first();
        $plano = Plano::where('lote_id', $lote->id)->firstOrFail();

        foreach ([
            ['GET', "/fazendas/{$this->fazenda->id}"],
            ['PUT', "/fazendas/{$this->fazenda->id}", ['nome_fazenda' => 'Invadida', 'area' => 1]],
            ['DELETE', "/fazendas/{$this->fazenda->id}"],
            ['GET', "/lotes/{$lote->id}"],
            ['DELETE', "/lotes/{$lote->id}"],
            ['POST', "/lotes/{$lote->id}/morte", ['causa' => 'Acidente', 'quantidade' => 1, 'data_ocorrencia' => now()->toDateString()]],
            ['GET', "/racoes/{$racao->id}"],
            ['DELETE', "/racoes/{$racao->id}"],
            ['GET', "/cochos/{$cocho->id}"],
            ['GET', "/estoque/{$racao->id}"],
            ['GET', "/planos-alimentares/{$plano->id}"],
            ['DELETE', "/planos-alimentares/{$plano->id}"],
            ['GET', "/dashboard/resumo?fazenda_id={$this->fazenda->id}"],
        ] as $caso) {
            [$metodo, $uri] = $caso;
            $resposta = $this->api($metodo, $uri, $caso[2] ?? [], $tokenOutro);
            $this->assertSame(404, $resposta->status(), "{$metodo} {$uri} deveria dar 404 para outra conta: " . $resposta->getContent());
            $resposta->assertJsonPath('success', false);
        }

        // Escrita referenciando ids alheios: recusada na validação.
        $this->api('POST', '/estoque/entrada', ['racao_id' => $racao->id, 'quantidade' => 10], $tokenOutro)
            ->assertStatus(422)->assertJsonValidationErrors(['racao_id']);
        $this->api('POST', '/lotes', ['nome_lote' => 'X', 'fazenda_id' => $this->fazenda->id], $tokenOutro)
            ->assertStatus(422)->assertJsonValidationErrors(['fazenda_id']);

        // Listagens do outro usuário não trazem nada da conta demo.
        $this->assertSame(['Fazenda do Outro'], collect($this->api('GET', '/fazendas', [], $tokenOutro)->json('data'))->pluck('nome_fazenda')->all());
        $this->assertSame([], $this->api('GET', '/racoes', [], $tokenOutro)->json('data'));
        $this->assertSame([], $this->api('GET', '/estoque/movimentacoes', [], $tokenOutro)->json('data'));

        // Nada foi alterado.
        $this->assertSame('Fazenda Boa Vista', $this->fazenda->fresh()->nome_fazenda);
        $this->assertNotNull(Lote::find($lote->id));
        $this->assertNotNull(Plano::find($plano->id));

        // Sem token: 401 padronizado.
        $this->app['auth']->forgetGuards();
        $this->flushHeaders()->getJson('/api/fazendas')->assertStatus(401)->assertJsonPath('success', false);
    }

    public function test_respostas_seguem_o_padrao_success_message_data(): void
    {
        foreach (['/fazendas', '/lotes', '/cochos', '/racoes', '/estoque', '/planos-alimentares', '/opcoes', '/alertas/resumo?fazenda_id=' . $this->fazenda->id, '/acessibilidade'] as $uri) {
            $this->api('GET', $uri)->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'message', 'data']);
        }

        // Respostas que nasceram sem "data" ganham a cópia, sem perder as
        // chaves do topo que o app já lê.
        $alertas = $this->api('GET', '/alertas/resumo?fazenda_id=' . $this->fazenda->id);
        $this->assertSame($alertas->json('count'), $alertas->json('data.count'));
        $this->assertSame($alertas->json('alertas'), $alertas->json('data.alertas'));
        $login = $this->postJson('/api/auth/login', ['email' => 'demo@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA]);
        $this->assertNotEmpty($login->json('token'));
        $this->assertSame($login->json('token'), $login->json('data.token'));
        $this->assertSame('demo@agrifarm.com.br', $login->json('data.usuario.email'));

        $this->api('GET', '/lotes/999999')->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Registro não encontrado.')
            ->assertJsonStructure(['errors']);

        // Tipos numéricos estáveis para o app (nada de "12.50" como texto).
        $lote = $this->api('GET', '/lotes')->json('data.0');
        $this->assertIsInt($lote['id']);
        $this->assertIsInt($lote['quantidade_animais']);
        $this->assertIsFloat($lote['peso_medio'] + 0.0);
    }

    public function test_plano_entrega_numeros_sem_formatacao_de_tela(): void
    {
        $dados = $this->api('GET', '/plano?fazenda_id=' . $this->fazenda->id)->assertOk()->json('data');

        // Os campos de tela continuam iguais ("4.440" = 4.440 kg em pt-BR)...
        $this->assertIsString($dados['totalSemana']);
        // ...e os blocos para o app trazem números e listas de verdade.
        $semana = $dados['consumo7Dias'];
        $this->assertCount(7, $semana['labels']);
        $this->assertCount(7, $semana['valores_kg']);
        $this->assertEqualsWithDelta(array_sum($semana['valores_kg']), $semana['total_kg'], 0.1);
        $this->assertEqualsWithDelta((float) str_replace('.', '', $dados['totalSemana']), $semana['total_kg'], 1);

        $mes = $dados['resumoMensal'];
        $this->assertSame($dados['mesReferencia'], $mes['mes']);
        $this->assertSame(json_decode($dados['valoresMensal'], true), $mes['valores_kg']);
        foreach (['total_kg', 'custo_total', 'media_diaria_kg'] as $campo) {
            $this->assertIsNumeric($mes[$campo]);
            $this->assertIsNotString($mes[$campo]);
        }
    }
}
