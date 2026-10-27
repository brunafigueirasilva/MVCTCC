<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use App\Models\Lote;
use App\Models\Plano;
use App\Models\Racao;
use Carbon\Carbon;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Web e app com a MESMA regra de plano alimentar: o vínculo de ração é o
 * plano ativo do lote (kg por animal por dia), o consumo do lote acompanha a
 * quantidade de animais e a mesma ração pode alimentar vários lotes.
 */
class PlanoAlimentarUnificadoTest extends TestCase
{
    use RefreshDatabase;

    private Cadastro $demo;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-10 12:00:00'));
        $this->seed(AgriFarmDemoSeeder::class);
        $this->demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function web()
    {
        return $this->withSession(['usuario_id' => $this->demo->id, 'usuario_nome' => 'Demo']);
    }

    private function vincular(Lote $lote, ?int $racaoId, ?float $porAnimal = null)
    {
        return $this->web()->post("/plano/lotes/{$lote->id}/vincular-racao", array_filter([
            'racao_id' => $racaoId ?? '',
            'quantidade_por_animal' => $porAnimal,
        ], fn ($v) => $v !== null));
    }

    public function test_vincular_pela_web_cria_o_plano_ativo_por_animal_igual_a_api(): void
    {
        $lote = Lote::where('nome_lote', 'Engorda B')->firstOrFail();

        $this->vincular($lote, $lote->racao_id, 4.0)->assertSessionHasNoErrors()->assertSessionHas('success');

        $ativos = Plano::where('lote_id', $lote->id)->where('ativo', true)->get();
        $this->assertCount(1, $ativos);
        $this->assertEqualsWithDelta(4.0, $ativos->first()->quantidade_recomendada, 0.001);

        // A API (app) enxerga o mesmo plano e o mesmo consumo do lote.
        $token = $this->postJson('/api/auth/login', ['email' => 'demo@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA])->json('token');
        $resposta = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/planos-alimentares?lote_id={$lote->id}&ativo=1")
            ->assertOk();
        // JSON não distingue 236 de 236.0 — compara o número, não o tipo.
        $this->assertEqualsWithDelta(4.0 * $lote->quantidade_animais, $resposta->json('data.0.consumo_diario_previsto_kg'), 0.01);
    }

    public function test_consumo_planejado_acompanha_a_quantidade_de_animais(): void
    {
        $lote = Lote::where('nome_lote', 'Engorda B')->firstOrFail();
        $this->vincular($lote, $lote->racao_id, 4.0);

        $this->web()->get('/plano/listar?fazenda_id=' . $lote->fazenda_id)->assertOk()
            ->assertSee(number_format(4.0 * $lote->quantidade_animais, 1, ',', '.') . ' kg', false);

        // Nasceram 10 animais: o plano do lote cresce junto, sem editar nada.
        $lote->update(['quantidade_animais' => $lote->quantidade_animais + 10]);
        $this->web()->get('/plano/listar?fazenda_id=' . $lote->fazenda_id)->assertOk()
            ->assertSee(number_format(4.0 * $lote->quantidade_animais, 1, ',', '.') . ' kg', false);
    }

    public function test_a_mesma_racao_pode_alimentar_dois_lotes_e_o_estoque_soma_os_dois(): void
    {
        $leiteiras = Lote::where('nome_lote', 'Leiteiras A')->firstOrFail();
        // Lote novo, compatível com a ração das Leiteiras (mesma espécie/sexo/finalidade).
        $novilhas = Lote::create([
            'fazenda_id' => $leiteiras->fazenda_id, 'nome_lote' => 'Novilhas Teste', 'tipo_animal' => 'Bovino',
            'finalidade' => 'Leite', 'sexo' => 'Fêmea', 'idade_media' => '1-2 anos', 'quantidade_animais' => 20,
            'peso_medio' => 300, 'status' => 'Ativo',
        ]);
        $racao = Racao::findOrFail($leiteiras->racao_id);

        $this->vincular($leiteiras, $racao->id, 3.0)->assertSessionHasNoErrors();
        $this->vincular($novilhas, $racao->id, 2.0)->assertSessionHasNoErrors();

        $this->assertSame($racao->id, $novilhas->fresh()->racao_id);

        $consumoTotal = 3.0 * $leiteiras->quantidade_animais + 2.0 * 20;
        $racao = $racao->fresh();
        $this->assertEqualsWithDelta($consumoTotal, $racao->consumoDiarioReferencia(), 0.01);
        $this->assertSame((int) floor($racao->estoque / $consumoTotal), $racao->diasRestantesEstoque());
    }

    public function test_desvincular_encerra_o_plano_ativo(): void
    {
        $lote = Lote::where('nome_lote', 'Suínos C')->firstOrFail();

        $this->vincular($lote, null)->assertSessionHas('success');

        $this->assertNull($lote->fresh()->racao_id);
        $this->assertSame(0, Plano::where('lote_id', $lote->id)->where('ativo', true)->count());
        $this->assertNotNull(Plano::where('lote_id', $lote->id)->latest('id')->first()->data_fim);
    }

    public function test_racao_incompativel_continua_recusada(): void
    {
        $lote = Lote::where('nome_lote', 'Engorda B')->firstOrFail();          // bovino macho de corte
        $racaoAves = Racao::where('nome_racao', 'Ração Postura')->firstOrFail();

        $this->vincular($lote, $racaoAves->id, 1.0)->assertSessionHasErrors('racao_id');
        $this->assertNotSame($racaoAves->id, $lote->fresh()->racao_id);
    }
}
