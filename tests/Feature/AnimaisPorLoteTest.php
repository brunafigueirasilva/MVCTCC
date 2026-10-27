<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Lote;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Gráfico "Animais por Lote": GET /api/lotes/animais-por-lote (app) e
 * /animais-por-lote (página web) com os MESMOS dados do banco, só da
 * fazenda selecionada, refletindo na hora as mudanças nos lotes.
 */
class AnimaisPorLoteTest extends TestCase
{
    use RefreshDatabase;

    private Cadastro $demo;
    private Fazenda $principal;
    private Fazenda $sitio;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AgriFarmDemoSeeder::class);
        $this->demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
        $this->principal = Fazenda::where('nome_fazenda', 'Fazenda Boa Vista')->firstOrFail();
        $this->sitio = Fazenda::where('nome_fazenda', 'Sítio Santa Luzia')->firstOrFail();
        $this->token = $this->postJson('/api/auth/login', ['email' => 'demo@agrifarm.com.br', 'password' => AgriFarmDemoSeeder::SENHA])->json('token');
    }

    private function api(string $uri, ?string $token = null)
    {
        $this->app['auth']->forgetGuards();

        return $this->withHeader('Authorization', 'Bearer ' . ($token ?? $this->token))->getJson('/api' . $uri);
    }

    private function sessao(): array
    {
        return ['usuario_id' => $this->demo->id, 'usuario_nome' => $this->demo->responsavel, 'usuario_email' => $this->demo->email];
    }

    public function test_api_devolve_nome_quantidade_total_e_percentual_da_fazenda(): void
    {
        $resposta = $this->api("/lotes/animais-por-lote?fazenda_id={$this->principal->id}")->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.fazenda.id', $this->principal->id)
            ->assertJsonStructure(['data' => ['total', 'lotes' => [['lote_id', 'nome_lote', 'tipo_animal', 'quantidade_animais', 'percentual']]]]);

        $esperado = Lote::ativos()->where('fazenda_id', $this->principal->id)->where('quantidade_animais', '>', 0)->get();
        $this->assertSame((int) $esperado->sum('quantidade_animais'), $resposta->json('data.total'));
        $this->assertSame($esperado->count(), count($resposta->json('data.lotes')));
        $this->assertSame('Poedeiras D', $resposta->json('data.lotes.0.nome_lote')); // maior primeiro
        $this->assertEqualsWithDelta(100, collect($resposta->json('data.lotes'))->sum('percentual'), 0.5);

        // Só a fazenda escolhida.
        $this->assertNotContains('Lote do Outro', collect($resposta->json('data.lotes'))->pluck('nome_lote'));
        $this->api("/lotes/animais-por-lote?fazenda_id={$this->sitio->id}")->assertOk()
            ->assertJsonPath('data.total', 0)->assertJsonPath('data.lotes', []);

        // Sem fazenda: vazio (mesma regra do dashboard). Fazenda alheia: 404.
        $this->api('/lotes/animais-por-lote')->assertOk()->assertJsonPath('data.fazenda', null)->assertJsonPath('data.total', 0);
        $outro = Fazenda::where('nome_fazenda', 'Fazenda do Outro')->firstOrFail();
        $this->api("/lotes/animais-por-lote?fazenda_id={$outro->id}")->assertNotFound();
    }

    public function test_mudancas_nos_lotes_aparecem_na_proxima_consulta(): void
    {
        $uri = "/lotes/animais-por-lote?fazenda_id={$this->principal->id}";
        $antes = $this->api($uri)->json('data.total');
        $leite = Lote::where('nome_lote', 'Leiteiras A')->firstOrFail();

        // Óbito pela API: o total cai 1.
        $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/lotes/{$leite->id}/morte", ['causa' => 'Acidente', 'quantidade' => 1, 'data_ocorrencia' => now()->toDateString()])->assertOk();
        $this->assertSame($antes - 1, $this->api($uri)->json('data.total'));

        // Lote encerrado sai do gráfico.
        $leite->update(['status' => 'Encerrado']);
        $nomes = collect($this->api($uri)->json('data.lotes'))->pluck('nome_lote');
        $this->assertNotContains('Leiteiras A', $nomes);
    }

    public function test_web_usa_a_mesma_fazenda_selecionada_e_os_mesmos_numeros(): void
    {
        // Duas fazendas e nenhuma escolhida: vazio, pedindo a seleção.
        $this->withSession($this->sessao())->getJson('/animais-por-lote')->assertOk()
            ->assertJsonPath('total', 0)->assertJsonPath('fazenda', null);

        // Escolheu a fazenda no Dashboard: a página inicial segue a mesma.
        $this->withSession($this->sessao())->get("/dashboard?fazenda_id={$this->principal->id}")->assertOk();
        $web = $this->getJson('/animais-por-lote')->assertOk();
        $api = $this->api("/lotes/animais-por-lote?fazenda_id={$this->principal->id}");
        $this->assertSame($api->json('data.total'), $web->json('total'));
        // O navegador não manda o token do app: volta a ser só a sessão.
        $this->flushHeaders();
        $this->app['auth']->forgetGuards();

        // ?fazenda_id= troca na hora.
        $this->getJson("/animais-por-lote?fazenda_id={$this->sitio->id}")->assertOk()->assertJsonPath('total', 0);
        $this->get('/')->assertOk()->assertSee('loteVazio', false);
    }
}
