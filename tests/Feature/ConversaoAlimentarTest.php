<?php

namespace Tests\Feature;

use App\Http\Controllers\PlanoController;
use App\Models\Cadastro;
use App\Models\DesempenhoLote;
use App\Models\LeituraIot;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use App\Models\Sensor;
use App\Services\EstoqueService;
use Carbon\Carbon;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Conversão alimentar = ração consumida ÷ peso ganho entre duas pesagens,
 * com o consumo vindo da fonte mais confiável (sensor → ração colocada nos
 * cochos → plano) e o custo pelo preço cadastrado da ração.
 */
class ConversaoAlimentarTest extends TestCase
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

    private function pesar(Lote $lote, string $data, float $peso)
    {
        return $this->withSession(['usuario_id' => $this->demo->id, 'usuario_nome' => 'Demo'])
            ->post("/plano/lotes/{$lote->id}/pesagem", ['peso_medio_kg' => $peso, 'data' => $data]);
    }

    private function lote(string $nome): Lote
    {
        return Lote::where('nome_lote', $nome)->firstOrFail();
    }

    private function ultimaPesagem(Lote $lote): DesempenhoLote
    {
        return DesempenhoLote::where('lote_id', $lote->id)->orderByDesc('data_registro')->firstOrFail();
    }

    public function test_usa_a_racao_colocada_nos_cochos_sem_contar_o_dia_da_pesagem_anterior(): void
    {
        $lote = $this->lote('Engorda B');
        $this->pesar($lote, '2026-10-05', 400)->assertRedirect();

        // Abastecimento NO DIA da 1ª pesagem: pertence ao período anterior.
        app(EstoqueService::class)->abastecerCochos($lote->racao, [$lote->cochos()->first()->id => 10], [
            'cadastro_id' => $this->demo->id, 'data_movimentacao' => Carbon::parse('2026-10-05 15:00'),
        ]);

        $this->pesar($lote, '2026-10-10', 410)->assertRedirect()->assertSessionHas('success');

        $p = $this->ultimaPesagem($lote);
        $animais = $lote->fresh()->quantidade_animais;
        // Seeder: 4 cochos × 70 kg em 10/10 (dentro do período).
        $this->assertSame(DesempenhoLote::FONTE_FORNECIDO, $p->fonte_consumo);
        $this->assertEqualsWithDelta(280, $p->consumo_kg, 0.01);
        $this->assertEqualsWithDelta(280 * 1.95, $p->custo_rs, 0.01);           // preço cadastrado, não R$ 3,50
        $this->assertEqualsWithDelta(10 * $animais, $p->ganho_peso_kg, 0.01);
        $this->assertEqualsWithDelta(round(280 / (10 * $animais), 2), $p->eficiencia_alimentar, 0.001);
        $this->assertFalse($p->conversaoEhReal());
    }

    public function test_sem_abastecimento_usa_o_plano_como_estimativa(): void
    {
        $lote = $this->lote('Suínos C');   // saída manual do seeder não é abastecimento de cocho
        $this->pesar($lote, '2026-10-06', 85);
        $this->pesar($lote, '2026-10-10', 90);

        $esperado = LoteConsumoSnapshot::where('lote_id', $lote->id)
            ->whereDate('data', '>', '2026-10-06')->whereDate('data', '<=', '2026-10-10')->sum('consumo_kg');

        $p = $this->ultimaPesagem($lote);
        $this->assertSame(DesempenhoLote::FONTE_PLANO, $p->fonte_consumo);
        $this->assertEqualsWithDelta($esperado, $p->consumo_kg, 0.01);
        $this->assertEqualsWithDelta($esperado * 2.10, $p->custo_rs, 0.01);
    }

    public function test_com_sensores_em_todos_os_cochos_o_consumo_e_medido(): void
    {
        $lote = $this->lote('Poedeiras D');
        foreach ($lote->cochos as $i => $cocho) {
            $sensor = Sensor::create(['cocho_id' => $cocho->id, 'codigo' => "BAL-T{$i}", 'tipo' => Sensor::TIPO_BALANCA, 'ativo' => true]);
            foreach ([['2026-10-05 08:00', 50, null], ['2026-10-07 08:00', 40, -10], ['2026-10-09 08:00', 30, -10]] as [$quando, $kg, $variacao]) {
                LeituraIot::create(['cocho_id' => $cocho->id, 'sensor_id' => $sensor->id, 'tipo_sensor' => Sensor::TIPO_BALANCA,
                    'peso_kg' => $kg, 'quantidade_kg' => $kg, 'variacao_kg' => $variacao, 'data_hora' => $quando]);
            }
        }

        $this->pesar($lote, '2026-10-05', 1.70);
        $this->pesar($lote, '2026-10-10', 1.80);

        $p = $this->ultimaPesagem($lote);
        $kg = 20 * $lote->cochos->count();
        $this->assertSame(DesempenhoLote::FONTE_SENSOR, $p->fonte_consumo);
        $this->assertEqualsWithDelta($kg, $p->consumo_kg, 0.01);
        $this->assertEqualsWithDelta($kg * 2.60, $p->custo_rs, 0.01);
        $this->assertTrue($p->conversaoEhReal());
    }

    public function test_sem_nenhum_consumo_registrado_nao_inventa_conversao(): void
    {
        $lote = $this->lote('Equinos E');   // sem ração vinculada, sem abastecimento
        $this->pesar($lote, '2026-10-05', 450);
        $this->pesar($lote, '2026-10-10', 455)->assertSessionHas('success', fn ($m) => str_contains($m, 'abasteça os cochos'));

        $p = $this->ultimaPesagem($lote);
        $this->assertNull($p->fonte_consumo);
        $this->assertEquals(0, $p->consumo_kg);
        $this->assertEquals(0, $p->eficiencia_alimentar);
    }

    public function test_lote_sem_racao_vinculada_grava_consumo_zero_no_historico(): void
    {
        app(PlanoController::class)->capturarSnapshotsGlobal();

        $semVinculo = $this->lote('Equinos E');
        $snapshot = LoteConsumoSnapshot::where('lote_id', $semVinculo->id)->whereDate('data', '2026-10-10')->firstOrFail();
        $this->assertEquals(0, $snapshot->consumo_kg);
        $this->assertNull($snapshot->racao_id);

        $vinculado = $this->lote('Engorda B');
        $this->assertGreaterThan(0, LoteConsumoSnapshot::where('lote_id', $vinculado->id)->whereDate('data', '2026-10-10')->value('consumo_kg'));
    }
}
