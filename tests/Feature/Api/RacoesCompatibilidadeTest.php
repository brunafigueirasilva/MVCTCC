<?php

namespace Tests\Feature\Api;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use App\Support\CatalogoRacoes;
use App\Support\CompatibilidadeRacao;
use App\Support\LoteRegras;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tela "Tipos de Rações" do app: filtros pela API, nível de estoque, campos
 * nutricionais e — principalmente — a regra de compatibilidade ração × lote
 * aplicada no servidor (lista de opções E validação ao salvar).
 */
class RacoesCompatibilidadeTest extends TestCase
{
    use RefreshDatabase;

    private Fazenda $fazenda;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AgriFarmDemoSeeder::class);

        $demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
        $this->fazenda = $demo->fazendas()->where('nome_fazenda', 'Fazenda Boa Vista')->firstOrFail();
        $this->token = $this->postJson('/api/auth/login', ['email' => 'demo@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA])
            ->assertOk()->json('token');
    }

    private function api(string $metodo, string $uri, array $dados = [])
    {
        $this->app['auth']->forgetGuards();

        // GET: filtros vão na query string (json() mandaria no corpo).
        if ($metodo === 'GET' && $dados) {
            $uri .= '?' . http_build_query($dados);
            $dados = [];
        }

        return $this->withHeader('Authorization', 'Bearer ' . $this->token)->json($metodo, '/api' . $uri, $dados);
    }

    private function lote(string $nome, string $tipo, string $sexo, string $finalidade): Lote
    {
        return Lote::create([
            'fazenda_id' => $this->fazenda->id,
            'nome_lote' => $nome,
            'tipo_animal' => $tipo,
            'sexo' => $sexo,
            'finalidade' => $finalidade,
            'idade_media' => '1-2 anos',
            'quantidade_animais' => 10,
            'peso_medio' => 100,
            'status' => 'Ativo',
        ]);
    }

    private function racao(string $nome, string $tipo, ?string $sexo, ?string $finalidade, array $extra = []): Racao
    {
        return Racao::create($extra + [
            'fazenda_id' => $this->fazenda->id,
            'nome_racao' => $nome,
            'tipo' => $tipo,
            'sexo' => $sexo,
            'finalidade' => $finalidade,
            'unidade' => 'kg',
            'estoque' => 500,
            'estoque_total' => 1000,
            'consumo' => 10,
            'valor_kg' => 2,
        ]);
    }

    private function nomesCompativeis(Lote $lote): array
    {
        return collect($this->api('GET', "/lotes/{$lote->id}/racoes-compativeis")->assertOk()->json('data'))
            ->pluck('nome_racao')->all();
    }

    public function test_lote_suino_so_ve_racoes_suinas(): void
    {
        $lote = $this->lote('Suínos T', 'Suíno', 'Misto', 'Engorda');
        $this->racao('Ração Suína T', 'Suíno', 'Misto', 'Engorda');
        $this->racao('Ração Suíno Crescimento T', 'Suíno', null, null);
        $this->racao('Ração Bovina T', 'Bovino', 'Macho', 'Corte');
        $this->racao('Ração Equina T', 'Equino', null, null);

        $nomes = $this->nomesCompativeis($lote);

        $this->assertContains('Ração Suína T', $nomes);
        $this->assertContains('Ração Suíno Crescimento T', $nomes);
        $this->assertNotContains('Ração Bovina T', $nomes);
        $this->assertNotContains('Ração Equina T', $nomes);
    }

    public function test_bovino_femea_leite_prioriza_leite_e_exclui_finalidade_incompativel(): void
    {
        $lote = $this->lote('Vacas Leite T', 'Bovino', 'Fêmea', 'Leite');
        $this->racao('Bovino Geral T', 'Bovino', null, null);
        $this->racao('Ração Leite Bovino T', 'Bovino', 'Fêmea', 'Leite');
        $this->racao('Bovino Corte T', 'Bovino', 'Macho', 'Corte');
        $this->racao('Bovino Corte Misto T', 'Bovino', 'Misto', 'Corte');
        $this->racao('Suína T', 'Suíno', 'Fêmea', null);

        $resposta = collect($this->api('GET', "/lotes/{$lote->id}/racoes-compativeis")->assertOk()->json('data'));
        $nomes = $resposta->pluck('nome_racao')->all();

        // As exatas (Fêmea + Leite) vêm antes das de uso geral.
        $prioridades = $resposta->pluck('prioritaria')->all();
        $this->assertSame($prioridades, collect($prioridades)->sortDesc()->values()->all());
        $this->assertTrue($resposta->firstWhere('nome_racao', 'Ração Leite Bovino T')['prioritaria']);
        $this->assertFalse($resposta->firstWhere('nome_racao', 'Bovino Geral T')['prioritaria']);
        $this->assertContains('Bovino Geral T', $nomes);
        $this->assertNotContains('Bovino Corte T', $nomes);
        $this->assertNotContains('Bovino Corte Misto T', $nomes);
        $this->assertNotContains('Suína T', $nomes);
    }

    public function test_racao_inativa_nao_e_oferecida(): void
    {
        $lote = $this->lote('Equinos T', 'Equino', 'Misto', 'Trabalho');
        $this->racao('Equina Ativa T', 'Equino', null, null);
        $this->racao('Equina Inativa T', 'Equino', null, null, ['status' => 'inativa']);

        $nomes = $this->nomesCompativeis($lote);

        $this->assertContains('Equina Ativa T', $nomes);
        $this->assertNotContains('Equina Inativa T', $nomes);
    }

    public function test_vincular_racao_incompativel_e_recusado_no_servidor(): void
    {
        $lote = $this->lote('Corte T', 'Bovino', 'Macho', 'Corte');
        $suina = $this->racao('Suína Errada T', 'Suíno', null, null);

        $this->api('POST', "/plano/lotes/{$lote->id}/vincular-racao", ['racao_id' => $suina->id])
            ->assertStatus(422)
            ->assertJsonFragment(['racao_id' => [CompatibilidadeRacao::MENSAGEM]]);

        $this->assertNull($lote->fresh()->racao_id);
    }

    public function test_plano_alimentar_com_racao_incompativel_e_recusado(): void
    {
        $lote = $this->lote('Aves T', 'Avicultura', 'Fêmea', 'Ovos');
        $bovina = $this->racao('Bovina p/ Aves T', 'Bovino', null, null);

        $this->api('POST', '/planos-alimentares', [
            'lote_id' => $lote->id,
            'racao_id' => $bovina->id,
            'quantidade_recomendada' => 1.5,
            'data_inicio' => now()->toDateString(),
        ])->assertStatus(422)->assertJsonFragment(['racao_id' => [CompatibilidadeRacao::MENSAGEM]]);
    }

    public function test_lote_equino_b1_so_ve_racao_equina(): void
    {
        // Mesmo cenário do print do "Plano de Lote B1".
        $lote = $this->lote('Lote B1 T', 'Equino', 'Macho', 'Esporte');
        $this->racao('Pig Premium T', 'Suíno', 'Macho', 'Corte');
        $this->racao('Avicultura T', 'Avicultura', 'Fêmea', 'Ovos');
        $this->racao('Bovino Premium T', 'Bovino', 'Macho', 'Corte');
        $this->racao('Leite Bovino T', 'Bovino', 'Fêmea', 'Leite');
        $this->racao('Magnus Cow T', 'Equino', 'Macho', 'Esporte');

        $nomes = collect($this->nomesCompativeis($lote))->filter(fn ($n) => str_ends_with($n, ' T'))->values()->all();

        $this->assertSame(['Magnus Cow T'], $nomes);
    }

    public function test_lote_misto_so_aceita_racao_misto_ou_geral(): void
    {
        $lote = $this->lote('Misto Repro T', 'Bovino', 'Misto', 'Reprodução');
        $this->racao('Repro Misto T', 'Bovino', 'Misto', 'Reprodução');
        $this->racao('Repro Geral T', 'Bovino', null, 'Reprodução');
        $this->racao('Repro Fêmea T', 'Bovino', 'Fêmea', 'Reprodução');
        $this->racao('Repro Macho T', 'Bovino', 'Macho', 'Reprodução');

        $nomes = $this->nomesCompativeis($lote);

        $this->assertContains('Repro Misto T', $nomes);
        $this->assertContains('Repro Geral T', $nomes);
        $this->assertNotContains('Repro Fêmea T', $nomes);
        $this->assertNotContains('Repro Macho T', $nomes);

        // E a ração Misto continua servindo a um lote só de fêmeas.
        $femeas = $this->lote('Fêmeas Repro T', 'Bovino', 'Fêmea', 'Reprodução');
        $this->assertContains('Repro Misto T', $this->nomesCompativeis($femeas));
        $this->assertSame('Repro Fêmea T', $this->nomesCompativeis($femeas)[0], 'A de Fêmea vem primeiro.');
    }

    public function test_abastecer_cocho_com_racao_incompativel_e_recusado(): void
    {
        $lote = $this->lote('Bovinos Cocho T', 'Bovino', 'Fêmea', 'Leite');
        $cocho = Cocho::create([
            'lote_id' => $lote->id,
            'numero' => 1,
            'nome_cocho' => 'Cocho 1 T',
            'capacidade' => 100,
            'nivel_atual' => 0,
        ]);
        $suina = $this->racao('Suína p/ Cocho T', 'Suíno', null, null);
        $leite = $this->racao('Leite p/ Cocho T', 'Bovino', 'Fêmea', 'Leite');

        $this->api('POST', "/cochos/{$cocho->id}/abastecer", ['racao_id' => $suina->id, 'quantidade' => 10])
            ->assertStatus(422)
            ->assertJsonFragment(['racao_id' => [CompatibilidadeRacao::MENSAGEM]]);
        $this->api('POST', "/lotes/{$lote->id}/abastecer-cochos", ['racao_id' => $suina->id, 'quantidade_total' => 10])
            ->assertStatus(422)
            ->assertJsonFragment(['racao_id' => [CompatibilidadeRacao::MENSAGEM]]);

        $this->assertEquals(0, $cocho->fresh()->nivel_atual);
        $this->assertEquals(500, $suina->fresh()->estoque, 'Nada sai do estoque da ração recusada.');

        $this->api('POST', "/cochos/{$cocho->id}/abastecer", ['racao_id' => $leite->id, 'quantidade' => 10])
            ->assertSuccessful();
        $this->assertEquals(10, $cocho->fresh()->nivel_atual);
    }

    public function test_todo_modelo_do_catalogo_respeita_as_regras_do_lote(): void
    {
        foreach (CatalogoRacoes::MODELOS as $m) {
            $this->assertContains($m['tipo'], LoteRegras::tiposAnimais(), $m['chave']);
            $this->assertTrue($m['sexo'] === null || in_array($m['sexo'], LoteRegras::SEXOS, true), $m['chave']);
            $this->assertTrue($m['idade'] === null || in_array($m['idade'], LoteRegras::IDADES_MEDIAS, true), $m['chave']);
            if ($m['finalidade'] !== null) {
                $this->assertContains($m['finalidade'], LoteRegras::finalidadesPermitidas($m['tipo'], $m['sexo']), $m['chave']);
            }
        }
        $this->assertSame(count(CatalogoRacoes::MODELOS), collect(CatalogoRacoes::MODELOS)->pluck('chave')->unique()->count());
        $this->assertEqualsCanonicalizing(LoteRegras::tiposAnimais(), collect(CatalogoRacoes::MODELOS)->pluck('tipo')->unique()->all(), 'Toda espécie tem ao menos um tipo de ração.');
    }

    public function test_catalogo_lista_modelos_com_lotes_compativeis(): void
    {
        $leite = $this->lote('Vacas Catálogo T', 'Bovino', 'Fêmea', 'Leite');
        $this->lote('Bois Catálogo T', 'Bovino', 'Macho', 'Corte');

        $catalogo = collect($this->api('GET', '/racoes/catalogo', ['fazenda_id' => $this->fazenda->id])->assertOk()->json('data'))->keyBy('chave');

        $lotesLeite = collect($catalogo['bovino-leite']['lotes'])->pluck('nome_lote');
        $this->assertContains('Vacas Catálogo T', $lotesLeite);
        $this->assertNotContains('Bois Catálogo T', $lotesLeite, 'Ração de leite (Fêmea) não serve a lote de machos de corte.');
        $this->assertNotContains('Vacas Catálogo T', collect($catalogo['suino-terminacao']['lotes'])->pluck('nome_lote'));
        $this->assertSame('fa-cow', $catalogo['bovino-leite']['icone']);

        // "Adicionar": POST /racoes normal com o perfil do modelo.
        $modelo = CatalogoRacoes::buscar('bovino-leite');
        $this->api('POST', '/racoes', CatalogoRacoes::camposRacao($modelo) + [
            'fazenda_id' => $this->fazenda->id,
            'nome_racao' => 'Leite do Catálogo T',
            'estoque' => 300, 'estoque_total' => 1000, 'consumo' => 20, 'valor_kg' => 1.9,
        ])->assertSuccessful();

        $racao = Racao::where('nome_racao', 'Leite do Catálogo T')->firstOrFail();
        $this->assertSame(['Bovino', 'Fêmea', 'Leite'], [$racao->tipo, $racao->sexo, $racao->finalidade]);
        $this->assertEquals(22, $racao->proteina_percentual);
        $this->assertContains('Leite do Catálogo T', $this->nomesCompativeis($leite));

        $catalogo = collect($this->api('GET', '/racoes/catalogo', ['fazenda_id' => $this->fazenda->id])->json('data'))->keyBy('chave');
        $this->assertGreaterThanOrEqual(1, $catalogo['bovino-leite']['ja_cadastradas']);
    }

    public function test_perfil_impossivel_e_recusado_ao_salvar(): void
    {
        $base = [
            'fazenda_id' => $this->fazenda->id,
            'nome_racao' => 'Impossível T', 'estoque' => 10, 'estoque_total' => 100, 'consumo' => 1,
            'valor_kg' => 1,
        ];

        $this->api('POST', '/racoes', $base + ['tipo' => 'Bovino', 'sexo' => 'Macho', 'finalidade' => 'Leite'])
            ->assertStatus(422)->assertJsonValidationErrors('finalidade');
        $this->api('POST', '/racoes', $base + ['tipo' => 'Suíno', 'finalidade' => 'Ovos'])
            ->assertStatus(422)->assertJsonValidationErrors('finalidade');
        $this->assertNull(Racao::where('nome_racao', 'Impossível T')->first());
    }

    public function test_cadastro_sem_lote_com_nutrientes_e_validacao(): void
    {
        $dados = [
            'fazenda_id' => $this->fazenda->id,
            'nome_racao' => 'Ração Nova App T',
            'tipo' => 'Bovino',
            'sexo' => 'Fêmea',
            'finalidade' => 'Leite',
            'idade' => '1-2 anos',
            'estoque' => 200,
            'estoque_total' => 1000,
            'consumo' => 8,
            'valor_kg' => 1.75,
            'proteina_percentual' => 18.5,
            'energia_mcal_kg' => 2.8,
            'fibra_percentual' => 12,
            'minerais' => 'Cálcio, fósforo',
            'nutrientes' => 'Vitamina A',
            'estoque_minimo' => 150,
            'status' => 'ativa',
            'observacoes' => 'Ofertar em 2 tratos.',
        ];

        $this->api('POST', '/racoes', $dados)->assertSuccessful();

        $racao = Racao::where('nome_racao', 'Ração Nova App T')->firstOrFail();
        $this->assertSame('Bovino', $racao->tipo);
        $this->assertSame('Leite', $racao->finalidade);
        $this->assertEquals(18.5, $racao->proteina_percentual);
        $this->assertSame('Cálcio, fósforo', $racao->minerais);
        $this->assertEquals(200, $racao->estoque, 'O estoque inicial entra como movimentação de entrada.');

        // Campos obrigatórios vazios não salvam.
        $this->api('POST', '/racoes', ['fazenda_id' => $this->fazenda->id, 'nome_racao' => ''])
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['nome_racao', 'tipo', 'valor_kg']]);
    }

    public function test_ajuste_do_plano_nao_apaga_campos_nutricionais(): void
    {
        $lote = $this->lote('Leite Plano T', 'Bovino', 'Fêmea', 'Leite');
        $racao = $this->racao('Com Nutrientes T', 'Bovino', 'Fêmea', 'Leite', [
            'proteina_percentual' => 20,
            'observacoes' => 'Guardar em local seco.',
        ]);

        // Mesmo corpo que o Plano Alimentar do app envia (sem os campos novos).
        $this->api('PUT', "/racoes/{$racao->id}", [
            'fazenda_id' => $this->fazenda->id,
            'nome_racao' => $racao->nome_racao,
            'lote_id' => $lote->id,
            'estoque' => 500,
            'estoque_total' => 1000,
            'consumo' => 12,
            'valor_kg' => 2,
        ])->assertSuccessful();

        $racao->refresh();
        $this->assertEquals(12, $racao->consumo);
        $this->assertEquals(20, $racao->proteina_percentual);
        $this->assertSame('Guardar em local seco.', $racao->observacoes);
    }

    public function test_filtros_e_nivel_de_estoque_vem_da_api(): void
    {
        $this->racao('Filtro Adequada T', 'Ovino', null, null, ['estoque' => 900, 'consumo' => 10]);
        $this->racao('Filtro Baixa T', 'Ovino', null, null, ['estoque' => 80, 'consumo' => 10]);   // 8 dias
        $this->racao('Filtro Critica T', 'Ovino', null, null, ['estoque' => 20, 'consumo' => 10]); // 2 dias
        $this->racao('Filtro Minimo T', 'Ovino', null, null, ['estoque' => 400, 'consumo' => 1, 'estoque_minimo' => 500]);

        $todas = collect($this->api('GET', '/racoes', ['tipo_animal' => 'Ovino', 'busca' => 'Filtro'])->assertOk()->json('data'))
            ->keyBy('nome_racao');

        $this->assertSame('adequado', $todas['Filtro Adequada T']['nivel_estoque']);
        $this->assertSame('baixo', $todas['Filtro Baixa T']['nivel_estoque']);
        $this->assertSame('critico', $todas['Filtro Critica T']['nivel_estoque']);
        $this->assertSame('baixo', $todas['Filtro Minimo T']['nivel_estoque'], 'Abaixo do estoque mínimo cadastrado.');
        $this->assertSame('Fazenda Boa Vista', $todas['Filtro Adequada T']['nome_fazenda']);

        $criticas = collect($this->api('GET', '/racoes', ['busca' => 'Filtro', 'estoque' => 'critico'])->json('data'))->pluck('nome_racao');
        $this->assertSame(['Filtro Critica T'], $criticas->all());
    }
}
