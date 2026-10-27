<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\CochoConsumo;
use App\Models\EstoqueMovimentacao;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fluxo completo de ponta a ponta, numa conta NOVA criada pela API (o mesmo
 * caminho do app Flutter), conferindo que API, telas web e banco mostram os
 * MESMOS números em cada etapa:
 *
 * cadastro/login → fazendas A e B → lote (cochos automáticos) → ração →
 * entrada no estoque → abastecer cocho → consumo → dashboard → relatórios
 * (com filtros) → telas web → banco → exclusões protegidas → logout.
 */
class FluxoCompletoIntegradoTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    private function api(string $metodo, string $uri, array $dados = [], ?string $token = null)
    {
        $this->app['auth']->forgetGuards();

        return $this->withHeader('Authorization', 'Bearer ' . ($token ?? $this->token))->json($metodo, '/api' . $uri, $dados);
    }

    public function test_fluxo_completo_com_os_mesmos_dados_na_api_na_web_e_no_banco(): void
    {
        // ---------------------------------------------- 1. Cadastro e login
        $this->postJson('/api/auth/register', [
            'nome' => 'Produtora Teste', 'email' => 'fluxo@agrifarm.com.br', 'telefone' => '19999990000',
            'password' => 'Senha@123', 'password_confirmation' => 'Senha@123',
        ])->assertCreated();

        $this->token = $this->postJson('/api/auth/login', ['email' => 'fluxo@agrifarm.com.br', 'password' => 'Senha@123'])
            ->assertOk()->json('token');
        $usuario = Cadastro::where('email', 'fluxo@agrifarm.com.br')->firstOrFail();
        $this->api('GET', '/auth/me')->assertOk()->assertJsonPath('data.email', 'fluxo@agrifarm.com.br')->assertJsonMissingPath('data.senha');

        // ---------------------------------------------- 2. Fazendas A e B
        $fazendaA = $this->api('POST', '/fazendas', ['nome_fazenda' => 'Fazenda A', 'area' => 100, 'estado' => 'SP'])->assertCreated()->json('data.id');
        $fazendaB = $this->api('POST', '/fazendas', ['nome_fazenda' => 'Fazenda B', 'area' => 50, 'estado' => 'MG'])->assertCreated()->json('data.id');
        $this->assertSame($usuario->id, Fazenda::find($fazendaA)->cadastro_id);

        // ---------------------------------------------- 3. Lote + cochos automáticos
        $entrada = now()->subDays(10)->toDateString();
        $loteId = $this->api('POST', '/lotes', [
            'nome_lote' => "Leiteiras D'Água", 'fazenda_id' => $fazendaA, 'tipo_animal' => 'Bovino', 'sexo' => 'Fêmea',
            'finalidade' => 'Leite', 'idade_media' => '2-3 anos', 'quantidade_animais' => 10, 'quantidade_cochos' => 2,
            'cocho_capacidade' => 100, 'peso_medio' => 450.5, 'data_entrada' => $entrada, 'status' => 'Ativo',
        ])->assertCreated()->json('data.id');

        $this->api('GET', "/lotes/{$loteId}")->assertOk()
            ->assertJsonPath('data.data_entrada', $entrada)
            ->assertJsonPath('data.quantidade_animais', 10)
            ->assertJsonCount(2, 'data.cochos');
        [$cocho1, $cocho2] = Lote::find($loteId)->cochos()->orderBy('numero')->get()->all();

        // Ocorrência antes da entrada do lote: recusada.
        $this->api('POST', "/lotes/{$loteId}/parto", ['quantidade' => 1, 'data_ocorrencia' => now()->subDays(20)->toDateString()])
            ->assertStatus(422)->assertJsonValidationErrors('data_ocorrencia');
        $this->api('POST', "/lotes/{$loteId}/parto", ['quantidade' => 2, 'data_ocorrencia' => now()->toDateString()])->assertOk();
        $this->assertSame(12, Lote::find($loteId)->quantidade_animais);

        // Localização do cocho.
        $this->api('PUT', "/cochos/{$cocho1->id}", ['capacidade' => 100, 'nivel_atual' => 0, 'localizacao' => 'Piquete 3'])->assertOk();
        $this->api('GET', "/cochos/{$cocho1->id}")->assertOk()->assertJsonPath('data.localizacao', 'Piquete 3');

        // ---------------------------------------------- 4. Ração (com fornecedor)
        $this->api('POST', '/racoes', [
            'fazenda_id' => $fazendaA, 'lote_id' => $loteId, 'nome_racao' => 'Lactação Teste', 'estoque' => 100,
            'estoque_total' => 2000, 'consumo' => 12, 'valor_kg' => 0, 'fornecedor' => 'Coop. Sul',
        ])->assertStatus(422)->assertJsonValidationErrors('valor_kg'); // preço zero recusado

        $this->api('POST', '/racoes', [
            'fazenda_id' => $fazendaA, 'lote_id' => $loteId, 'nome_racao' => 'Lactação Teste', 'estoque' => 100,
            'estoque_total' => 2000, 'consumo' => 12, 'valor_kg' => 2.5, 'fornecedor' => 'Coop. Sul',
        ])->assertCreated();
        $racao = Racao::where('nome_racao', 'Lactação Teste')->firstOrFail();
        $this->api('GET', "/racoes/{$racao->id}")->assertOk()->assertJsonPath('data.fornecedor', 'Coop. Sul')->assertJsonPath('data.estoque', 100);

        // ---------------------------------------------- 5. Entrada no estoque
        $this->api('POST', '/estoque/entrada', ['racao_id' => $racao->id, 'quantidade' => 500])->assertCreated()->assertJsonPath('data.saldo_posterior', 600);
        $this->api('POST', '/estoque/saida', ['racao_id' => $racao->id, 'quantidade' => 5000])->assertStatus(422); // nunca negativo
        $this->api('POST', '/estoque/entrada', ['racao_id' => $racao->id, 'quantidade' => -1])->assertStatus(422);

        // ---------------------------------------------- 6. Enviar ração para o cocho
        $this->api('POST', "/cochos/{$cocho1->id}/abastecer", ['racao_id' => $racao->id, 'quantidade' => 60])->assertOk();
        $this->assertEquals(540, $racao->fresh()->estoque);    // armazém diminui
        $this->assertEquals(60, $cocho1->fresh()->nivel_atual); // cocho aumenta

        // A edição não "cria" ração no cocho: só Abastecer coloca ração.
        $this->api('PUT', "/cochos/{$cocho1->id}", ['capacidade' => 100, 'nivel_atual' => 90])->assertStatus(422)->assertJsonValidationErrors('nivel_atual');
        $this->assertEquals(60, $cocho1->fresh()->nivel_atual);

        // ---------------------------------------------- 7. Consumo
        $this->api('POST', "/cochos/{$cocho1->id}/consumo", ['quantidade' => 25, 'observacao' => 'Trato da manhã'])->assertOk()
            ->assertJsonPath('data.nivel_atual', 35);
        $this->api('POST', "/cochos/{$cocho1->id}/consumo", ['quantidade' => 100])->assertStatus(422); // mais do que há no cocho
        $this->api('POST', "/cochos/{$cocho1->id}/consumo", ['quantidade' => -3])->assertStatus(422);
        // Baixar o nível pela edição = consumo registrado (com histórico).
        $this->api('PUT', "/cochos/{$cocho1->id}", ['capacidade' => 100, 'nivel_atual' => 30])->assertOk();

        $this->assertEquals(30, $cocho1->fresh()->nivel_atual);
        $this->assertEquals(540, $racao->fresh()->estoque); // consumo não mexe no armazém
        $this->assertSame([25.0, 5.0], CochoConsumo::where('cocho_id', $cocho1->id)->orderBy('id')->pluck('quantidade')->all());
        $this->assertSame([CochoConsumo::ORIGEM_MANUAL, CochoConsumo::ORIGEM_AJUSTE_NIVEL], CochoConsumo::where('cocho_id', $cocho1->id)->orderBy('id')->pluck('origem')->all());

        // ---------------------------------------------- 8. Dashboard (A e B)
        $this->api('GET', "/dashboard/resumo?fazenda_id={$fazendaA}")->assertOk()
            ->assertJsonPath('data.lotes', 1)
            ->assertJsonPath('data.animais', 12)
            ->assertJsonPath('data.cochos', 2)
            ->assertJsonPath('data.estoque_atual_kg', 540)
            ->assertJsonPath('data.racao_nos_cochos_kg', 30);

        $this->api('GET', "/dashboard/resumo?fazenda_id={$fazendaB}")->assertOk()
            ->assertJsonPath('data.lotes', 0)->assertJsonPath('data.animais', 0)->assertJsonPath('data.cochos', 0)
            ->assertJsonPath('data.estoque_atual_kg', 0)->assertJsonPath('data.racao_nos_cochos_kg', 0);

        // ---------------------------------------------- 9. Relatórios + filtros
        $estoque = $this->api('GET', "/relatorios/estoque?fazenda_id={$fazendaA}")->assertOk();
        $this->assertEquals(540, $estoque->json('data.estoque_total_kg'));
        $this->assertEquals(600, $estoque->json('data.entradas_kg'));
        $this->assertEquals(60, $estoque->json('data.saidas_kg'));

        $this->assertEquals(30, $this->api('GET', "/relatorios/consumo?fazenda_id={$fazendaA}")->json('data.consumo_registrado_kg'));
        $this->assertSame(1, $this->api('GET', "/relatorios/lotes?fazenda_id={$fazendaA}&lote_id={$loteId}")->json('data.total_lotes'));
        $this->assertSame(0, $this->api('GET', "/relatorios/lotes?fazenda_id={$fazendaA}&tipo_animal=Suíno")->json('data.total_lotes'));
        $this->assertSame(0, $this->api('GET', "/relatorios/lotes?fazenda_id={$fazendaA}&status=Encerrado")->json('data.total_lotes'));
        $this->assertSame(2, $this->api('GET', "/relatorios/cochos?fazenda_id={$fazendaA}&status=Ativo")->json('data.total'));
        $this->assertSame(0, $this->api('GET', "/relatorios/cochos?fazenda_id={$fazendaA}&tipo_animal=Equino")->json('data.total'));
        $this->assertSame(0, $this->api('GET', "/relatorios/lotes?fazenda_id={$fazendaB}")->json('data.total_lotes'));
        $this->api('GET', "/relatorios/lotes?fazenda_id={$fazendaA}&status=Vendido")->assertStatus(422);
        $this->api('GET', "/relatorios/lotes?fazenda_id={$fazendaA}&data_inicio=2026-10-10&data_fim=2026-10-01")->assertStatus(422);

        // ---------------------------------------------- 10. As mesmas informações na web
        $sessao = ['usuario_id' => $usuario->id, 'usuario_nome' => $usuario->responsavel, 'usuario_email' => $usuario->email];

        $this->withSession($sessao)->get("/dashboard?fazenda_id={$fazendaA}")->assertOk()
            ->assertViewHas('estoqueAtual', 540.0)
            ->assertViewHas('racaoNosCochos', 30.0)
            ->assertViewHas('totalAnimais', 12)
            ->assertSee('Estoque no Armazém');
        $this->withSession($sessao)->get("/dashboard?fazenda_id={$fazendaB}")->assertOk()
            ->assertViewHas('estoqueAtual', 0.0)->assertViewHas('totalAnimais', 0);
        $this->withSession($sessao)->get("/cocho/listar?fazenda_id={$fazendaA}")->assertOk()
            ->assertSee('Piquete 3')->assertSee('Registrar Consumo');
        $this->withSession($sessao)->get("/estoque/listar?fazenda_id={$fazendaA}")->assertOk()->assertSee('Lactação Teste');
        $this->withSession($sessao)->get("/relatorios?fazenda_id={$fazendaA}")->assertOk();
        $this->withSession($sessao)->get("/relatorios?fazenda_id={$fazendaA}&data_inicio=nao-e-data")->assertSessionHasErrors('data_inicio');
        // Nome com apóstrofo não quebra o botão Editar (era '{{ }}' dentro do onclick).
        $this->withSession($sessao)->get("/lotes?fazenda_id={$fazendaA}")->assertOk()
            ->assertSee('Leiteiras D\u0027', false)
            ->assertDontSee("'Leiteiras D&#039;Água'", false);

        // Entrada e saída pela tela de Estoque (mesmas regras da API).
        $this->withSession($sessao)->post('/estoque/entrada', ['racao_id' => $racao->id, 'quantidade' => 40, 'observacao' => 'NF 77'])
            ->assertRedirect('/estoque/listar')->assertSessionHas('success');
        $this->withSession($sessao)->post('/estoque/saida', ['racao_id' => $racao->id, 'quantidade' => 10])
            ->assertRedirect('/estoque/listar');
        $this->withSession($sessao)->post('/estoque/saida', ['racao_id' => $racao->id, 'quantidade' => 99999])
            ->assertSessionHasErrors('quantidade');
        $this->assertEquals(570, $racao->fresh()->estoque);
        $this->assertDatabaseHas('estoque_movimentacoes', ['racao_id' => $racao->id, 'tipo' => 'entrada', 'quantidade' => 40, 'observacao' => 'NF 77', 'cadastro_id' => $usuario->id]);
        $this->api('GET', "/estoque/{$racao->id}")->assertOk()->assertJsonPath('data.estoque', 570); // o app vê o mesmo saldo

        // ---------------------------------------------- 11. Banco: tudo bate
        $racao->refresh();
        $entradas = EstoqueMovimentacao::where('racao_id', $racao->id)->where('tipo', 'entrada')->sum('quantidade');
        $saidas = EstoqueMovimentacao::where('racao_id', $racao->id)->where('tipo', 'saida')->sum('quantidade');
        $this->assertEquals($racao->estoque, $entradas - $saidas);
        $this->assertEquals(30, Cocho::find($cocho1->id)->nivel_atual);
        $this->assertEquals(0, Cocho::find($cocho2->id)->nivel_atual);
        $this->assertDatabaseHas('estoque_movimentacoes', ['racao_id' => $racao->id, 'cocho_id' => $cocho1->id, 'lote_id' => $loteId, 'fazenda_id' => $fazendaA, 'cadastro_id' => $usuario->id, 'origem' => 'abastecimento_cocho', 'quantidade' => 60]);
        $this->assertDatabaseHas('cocho_consumos', ['cocho_id' => $cocho1->id, 'lote_id' => $loteId, 'fazenda_id' => $fazendaA, 'racao_id' => $racao->id, 'cadastro_id' => $usuario->id, 'quantidade' => 25, 'nivel_anterior' => 60, 'nivel_posterior' => 35]);

        // ---------------------------------------------- 12. Exclusões protegidas
        $this->api('PUT', "/lotes/{$loteId}", [
            'nome_lote' => "Leiteiras D'Água", 'fazenda_id' => $fazendaB, 'tipo_animal' => 'Bovino', 'sexo' => 'Fêmea', 'finalidade' => 'Leite',
            'idade_media' => '2-3 anos', 'quantidade_animais' => 12, 'quantidade_cochos' => 2, 'cocho_capacidade' => 100, 'peso_medio' => 450.5,
        ])->assertStatus(422)->assertJsonValidationErrors('fazenda_id'); // não leva o histórico para a Fazenda B
        $this->api('DELETE', "/lotes/{$loteId}")->assertStatus(422);
        $this->api('DELETE', "/fazendas/{$fazendaA}")->assertStatus(422);
        $this->assertNotNull(Lote::find($loteId));
        $this->assertNotNull(Fazenda::find($fazendaA));
        $this->api('DELETE', "/fazendas/{$fazendaB}")->assertOk(); // vazia: pode
        $this->assertNull(Fazenda::find($fazendaB));

        // ---------------------------------------------- 13. Logout revoga o token
        $this->api('POST', '/auth/logout')->assertOk();
        $this->api('GET', '/auth/me')->assertStatus(401);
    }
}
