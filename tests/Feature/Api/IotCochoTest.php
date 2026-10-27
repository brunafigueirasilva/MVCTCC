<?php

namespace Tests\Feature\Api;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\LeituraIot;
use App\Models\Lote;
use App\Models\Racao;
use App\Models\Sensor;
use Carbon\Carbon;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integração IoT ponta a ponta: sensor → POST /api/iot/leitura → banco →
 * GET /api/cochos/{id}/iot(/historico) e tela web de detalhes.
 */
class IotCochoTest extends TestCase
{
    use RefreshDatabase;

    private const CHAVE = 'chave-de-teste-iot';

    private Cadastro $demo;
    private Cocho $cocho;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-06 12:00:00'));
        config(['iot.chave' => self::CHAVE, 'iot.minutos_offline' => 15]);

        $this->seed(AgriFarmDemoSeeder::class);
        $this->demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
        // Suínos C: cochos de 100 kg, vazios.
        $this->cocho = Lote::where('nome_lote', 'Suínos C')->firstOrFail()->cochos()->orderBy('numero')->firstOrFail();
        $this->token = $this->postJson('/api/auth/login', ['email' => 'demo@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA])->json('token');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function api(string $metodo, string $uri, array $dados = [], ?string $token = null)
    {
        $this->app['auth']->forgetGuards();

        return $this->withHeader('Authorization', 'Bearer ' . ($token ?? $this->token))->json($metodo, '/api' . $uri, $dados);
    }

    private function enviar(array $dados, ?string $chave = self::CHAVE)
    {
        return $this->withHeaders($chave === null ? [] : ['X-IoT-Key' => $chave])->postJson('/api/iot/leitura', $dados);
    }

    private function cadastrarSensores(?Cocho $cocho = null, array $extra = []): void
    {
        $cocho ??= $this->cocho;
        $this->api('PUT', "/cochos/{$cocho->id}", [
            'capacidade' => 100,
            'nivel_atual' => 0,
            'sensor_distancia' => 'DIST-001',
            'sensor_balanca' => 'BAL-001',
            'distancia_vazio_cm' => 50,
            'distancia_cheio_cm' => 0,
        ] + $extra)->assertOk();
    }

    public function test_rota_aberta_aceita_dispositivo_sem_chave(): void
    {
        // Ambiente escolar: a rota não exige X-IoT-Key (nem IOT_API_KEY).
        $this->cadastrarSensores();
        config(['iot.chave' => null]);

        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 10], null)->assertCreated();
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'NAO-EXISTE', 'peso_kg' => 10], null)
            ->assertStatus(422)->assertJsonValidationErrors('sensor_id'); // dados continuam validados
    }

    public function test_cocho_sem_sensor_mostra_zeros_e_sem_leitura(): void
    {
        $this->api('GET', "/cochos/{$this->cocho->id}/iot")->assertOk()
            ->assertJsonPath('data.capacidade_total', 100)
            ->assertJsonPath('data.quantidade_atual', 0)
            ->assertJsonPath('data.nivel_percentual', 0)
            ->assertJsonPath('data.distancia_cm', 0)
            ->assertJsonPath('data.peso_kg', 0)
            ->assertJsonPath('data.status_sensor', 'sem_leitura')
            ->assertJsonPath('data.ultima_atualizacao', null)
            ->assertJsonPath('data.alertas.1.tipo', 'sem_sensor');

        $this->api('GET', "/cochos/{$this->cocho->id}/iot/historico")->assertOk()
            ->assertJsonPath('data.totais.leituras', 0)
            ->assertJsonPath('data.pontos.0.quantidade_kg', null);
    }

    public function test_armazem_e_cocho_separados_e_cocho_em_zero_ate_a_primeira_leitura(): void
    {
        $lote = $this->cocho->lote()->with('racao')->first();
        $estoqueFazenda = (float) Racao::where('fazenda_id', $lote->fazenda_id)->sum('estoque');
        $this->assertGreaterThan(0, $estoqueFazenda);

        // Abastecido pelo estoque, mas sem leitura de sensor: o cocho (sensor) fica em 0.
        $racao = Racao::where('nome_racao', 'Ração Suínos Terminação')->firstOrFail();
        $this->api('POST', "/cochos/{$this->cocho->id}/abastecer", ['racao_id' => $racao->id, 'quantidade' => 40])->assertOk();

        $this->api('GET', "/cochos/{$this->cocho->id}/iot")->assertOk()
            ->assertJsonPath('data.cocho_sensor.quantidade_kg', 0)
            ->assertJsonPath('data.cocho_sensor.percentual', 0)
            ->assertJsonPath('data.cocho_sensor.tem_leitura', false)
            ->assertJsonPath('data.quantidade_atual', 40)               // registrado pelo abastecimento
            // JSON não distingue 5250 de 5250.0 — compara o número, não o tipo
            // (antes este teste falhava só por isso: "5250 is identical to 5250.0").
            ->assertJsonPath('data.armazem.quantidade_total_kg', fn ($kg) => abs($kg - ($estoqueFazenda - 40)) < 0.01)
            ->assertJsonPath('data.armazem.racao_lote.nome_racao', $lote->racao?->nome_racao);

        // Primeira leitura da balança: o cocho passa a mostrar o valor medido.
        $this->cadastrarSensores(null, ['nivel_atual' => 40]);
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 37.5])->assertCreated();

        $this->api('GET', "/cochos/{$this->cocho->id}/iot")
            ->assertJsonPath('data.cocho_sensor.quantidade_kg', 37.5)
            ->assertJsonPath('data.cocho_sensor.percentual', 37.5)
            ->assertJsonPath('data.cocho_sensor.tem_leitura', true)
            ->assertJsonPath('data.quantidade_atual', 37.5);
    }

    public function test_leitura_da_balanca_atualiza_cocho_historico_e_api(): void
    {
        $this->cadastrarSensores();

        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'tipo_sensor' => 'balanca', 'peso_kg' => 63.4])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.cocho.quantidade_atual', 63.4)
            ->assertJsonPath('data.leitura.variacao_kg', 63.4);

        $this->assertEquals(63.4, $this->cocho->fresh()->nivel_atual);
        $this->assertSame(1, LeituraIot::count());

        $this->api('GET', "/cochos/{$this->cocho->id}/iot")->assertOk()
            ->assertJsonPath('data.quantidade_atual', 63.4)
            ->assertJsonPath('data.nivel_percentual', 63.4)
            ->assertJsonPath('data.peso_kg', 63.4)
            ->assertJsonPath('data.status_sensor', 'online')
            ->assertJsonPath('data.ultima_atualizacao', '2026-10-06 12:00:00');

        // A listagem de cochos usada pelo app traz o resumo IoT.
        $this->api('GET', "/cochos/{$this->cocho->id}")->assertOk()
            ->assertJsonPath('data.sensor_balanca', 'BAL-001')
            ->assertJsonPath('data.status_sensor', 'online');
    }

    public function test_controlador_esp32_envia_peso_e_presenca_de_animal(): void
    {
        $this->cadastrarSensores();

        // Pacote do firmware iot/agrifarm_cocho.ino: balança + HC-SR04.
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'tipo_sensor' => 'balanca', 'peso_kg' => 4.25, 'presenca_animal' => true, 'distancia_presenca_cm' => 37.2])
            ->assertCreated()
            ->assertJsonPath('data.leitura.presenca_animal', true)
            ->assertJsonPath('data.leitura.distancia_presenca_cm', 37.2)
            ->assertJsonPath('data.cocho.quantidade_atual', 4.25);

        $this->assertDatabaseHas('leituras_iot', ['cocho_id' => $this->cocho->id, 'presenca_animal' => true, 'distancia_presenca_cm' => 37.2, 'distancia_cm' => null]);

        Carbon::setTestNow(Carbon::parse('2026-10-06 12:00:10'));
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 4.0, 'presenca_animal' => false])->assertCreated();

        $this->api('GET', "/cochos/{$this->cocho->id}/iot")->assertOk()
            ->assertJsonPath('data.presenca_animal', false)
            ->assertJsonPath('data.presenca_data_hora', '2026-10-06 12:00:10');

        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 4.0, 'presenca_animal' => 'talvez'])
            ->assertUnprocessable()->assertJsonValidationErrors('presenca_animal');
    }

    public function test_sensor_de_distancia_calcula_nivel_fisico_e_balanca_continua_referencia(): void
    {
        $this->cadastrarSensores();
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 63.4])->assertCreated();
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'DIST-001', 'tipo_sensor' => 'distancia', 'distancia_cm' => 18.5])
            ->assertCreated()
            ->assertJsonPath('data.leitura.nivel_percentual', 63);

        $this->assertEquals(63.4, $this->cocho->fresh()->nivel_atual);
        $this->api('GET', "/cochos/{$this->cocho->id}/iot")
            ->assertJsonPath('data.distancia_cm', 18.5)
            ->assertJsonPath('data.nivel_sensor_percentual', 63);
    }

    public function test_sem_balanca_a_distancia_estima_a_quantidade(): void
    {
        $this->api('PUT', "/cochos/{$this->cocho->id}", [
            'capacidade' => 100, 'nivel_atual' => 0,
            'sensor_distancia' => 'DIST-001', 'sensor_balanca' => '',
            'distancia_vazio_cm' => 50, 'distancia_cheio_cm' => 10,
        ])->assertOk();

        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'DIST-001', 'distancia_cm' => 30])->assertCreated();

        $this->assertEquals(50.0, $this->cocho->fresh()->nivel_atual);
    }

    public function test_reenvio_da_mesma_leitura_nao_duplica(): void
    {
        $this->cadastrarSensores();
        $pacote = ['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 40, 'data_hora' => '2026-10-06 11:30:00'];

        $this->enviar($pacote)->assertCreated();
        $this->enviar($pacote)->assertOk()->assertJsonPath('message', 'Leitura já registrada anteriormente.');

        $this->assertSame(1, LeituraIot::count());
    }

    public function test_validacoes_recusam_dados_invalidos_e_marcam_sensor_com_erro(): void
    {
        $this->cadastrarSensores();
        $outroCocho = Lote::where('nome_lote', 'Suínos C')->firstOrFail()->cochos()->orderBy('numero')->skip(1)->firstOrFail();

        $this->enviar(['sensor_id' => 'BAL-001', 'peso_kg' => 10])->assertStatus(422)->assertJsonValidationErrors('cocho_id');
        $this->enviar(['cocho_id' => 999999, 'sensor_id' => 'BAL-001', 'peso_kg' => 10])->assertStatus(422)->assertJsonValidationErrors('cocho_id');
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'NAO-EXISTE', 'peso_kg' => 10])->assertStatus(422)->assertJsonValidationErrors('sensor_id');
        $this->enviar(['cocho_id' => $outroCocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 10])->assertStatus(422)->assertJsonValidationErrors('sensor_id');
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'tipo_sensor' => 'distancia', 'distancia_cm' => 10])->assertStatus(422)->assertJsonValidationErrors('tipo_sensor');
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001'])->assertStatus(422)->assertJsonValidationErrors('peso_kg');
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 150])->assertStatus(422)->assertJsonValidationErrors('peso_kg');
        $this->assertSame(0, LeituraIot::count());

        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => -5])->assertStatus(422)->assertJsonValidationErrors('peso_kg');
        $this->assertSame(Sensor::STATUS_ERRO, Sensor::where('codigo', 'BAL-001')->first()->status());
        $this->api('GET', "/cochos/{$this->cocho->id}/iot")->assertJsonPath('data.status_sensor', 'erro');

        // Código de sensor já usado em outro cocho não pode ser cadastrado.
        $this->api('PUT', "/cochos/{$outroCocho->id}", ['capacidade' => 100, 'nivel_atual' => 0, 'sensor_balanca' => 'BAL-001'])
            ->assertStatus(422)->assertJsonValidationErrors('sensor_balanca');
    }

    public function test_sensor_fica_offline_sem_leitura_recente(): void
    {
        $this->cadastrarSensores();
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 50])->assertCreated();

        Carbon::setTestNow(now()->addMinutes(20));
        $this->api('GET', "/cochos/{$this->cocho->id}/iot")->assertJsonPath('data.status_sensor', 'offline');
    }

    public function test_historico_e_indicadores_de_consumo_e_reposicao(): void
    {
        $this->cadastrarSensores();
        foreach (['04:10' => 80, '05:10' => 70, '06:10' => 60] as $hora => $peso) {
            $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => $peso, 'data_hora' => "2026-10-06 {$hora}:00"])->assertCreated();
        }

        // Abastecimento pelo estoque (saída da ração + nível do cocho).
        $racao = Racao::where('nome_racao', 'like', '%Suín%')->firstOrFail();
        $this->api('POST', "/cochos/{$this->cocho->id}/abastecer", ['racao_id' => $racao->id, 'quantidade' => 30])->assertOk();
        $this->assertEquals(90.0, $this->cocho->fresh()->nivel_atual);

        // Próxima leitura parte do nível já abastecido: 90 → 85 = 5 kg de consumo.
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 85])->assertCreated()
            ->assertJsonPath('data.leitura.variacao_kg', -5);

        $historico = $this->api('GET', "/cochos/{$this->cocho->id}/iot/historico?periodo=hoje")->assertOk()->json('data');
        // Hoje: de 5 em 5 minutos, 00:00..12:00 = 145 pontos.
        $this->assertSame(145, count($historico['pontos']));
        $this->assertEquals(25, $historico['totais']['consumo_kg']);            // 10 + 10 + 5
        $this->assertEquals(110, $historico['totais']['reposicao_kg']);         // 80 (primeira leitura) + 30 abastecido
        $this->assertSame('04:10', $historico['pontos'][50]['rotulo']);
        $this->assertEquals(80, $historico['pontos'][50]['quantidade_kg']);     // leitura das 04:10
        $this->assertEquals(70, $historico['pontos'][62]['quantidade_kg']);     // 05:10
        $this->assertEquals(60, $historico['pontos'][74]['quantidade_kg']);     // 06:10
        $this->assertEquals(60, $historico['pontos'][75]['quantidade_kg']);     // repete o último valor conhecido
        $this->assertNull($historico['pontos'][49]['quantidade_kg']);           // antes da 1ª leitura: sem dado
        $this->assertEquals(10, $historico['pontos'][62]['consumo_kg']);        // consumo no intervalo certo
        $this->assertCount(4, $historico['leituras']);

        $resumo = $this->api('GET', "/cochos/{$this->cocho->id}/iot")->json('data');
        $this->assertEquals(25, $resumo['indicadores']['consumo_hoje_kg']);
        $this->assertSame('abastecimento', $resumo['indicadores']['ultima_reposicao']['origem']);
        $this->assertNotNull($resumo['indicadores']['consumo_medio_diario_kg']);
        $this->assertNotNull($resumo['indicadores']['previsao_esvaziamento']);

        $this->api('GET', "/cochos/{$this->cocho->id}/iot/historico?periodo=30d")->assertOk()->assertJsonCount(30, 'data.pontos');
        $this->api('GET', "/cochos/{$this->cocho->id}/iot/historico?periodo=ano")->assertStatus(422);
    }

    public function test_outro_usuario_nao_ve_o_cocho(): void
    {
        $tokenOutro = $this->postJson('/api/auth/login', ['email' => 'outro@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA])->json('token');

        $this->api('GET', "/cochos/{$this->cocho->id}/iot", [], $tokenOutro)->assertNotFound();
        $this->api('GET', "/cochos/{$this->cocho->id}/iot/historico", [], $tokenOutro)->assertNotFound();
    }

    public function test_tela_web_de_detalhes_e_json_pela_sessao(): void
    {
        $this->cadastrarSensores();
        $this->enviar(['cocho_id' => $this->cocho->id, 'sensor_id' => 'BAL-001', 'peso_kg' => 42])->assertCreated();

        $sessao = $this->withSession(['usuario_id' => $this->demo->id, 'usuario_nome' => $this->demo->responsavel, 'usuario_email' => $this->demo->email]);

        $sessao->get("/cocho/{$this->cocho->id}/detalhes")->assertOk()->assertSee($this->cocho->nome_cocho)->assertDontSee('42,00');
        $sessao->getJson("/cocho/{$this->cocho->id}/iot")->assertOk()->assertJsonPath('data.quantidade_atual', 42);
        $sessao->getJson("/cocho/{$this->cocho->id}/iot/historico?periodo=7d")->assertOk()->assertJsonCount(27, 'data.pontos');
        $sessao->get('/cocho/listar')->assertOk()->assertSee('Detalhes');
    }
}
