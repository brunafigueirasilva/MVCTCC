<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use App\Models\EstoqueMovimentacao;
use App\Models\Lote;
use App\Models\Racao;
use Carbon\Carbon;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Nada some em silêncio: ração com histórico não é excluída (é inativada)
 * e cocho com ração dentro não é removido ao diminuir a quantidade.
 */
class IntegridadeDadosTest extends TestCase
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

    public function test_racao_com_historico_nao_e_excluida_e_o_historico_fica(): void
    {
        $racao = Racao::where('nome_racao', 'Ração Engorda Plus')->firstOrFail();
        $movimentacoes = EstoqueMovimentacao::where('racao_id', $racao->id)->count();

        $this->web()->delete("/racao/delete/{$racao->id}")->assertSessionHasErrors('racao');

        $this->assertNotNull($racao->fresh());
        $this->assertSame($movimentacoes, EstoqueMovimentacao::where('racao_id', $racao->id)->count());
    }

    public function test_racao_cadastrada_por_engano_pode_ser_excluida(): void
    {
        $fazendaId = Lote::where('nome_lote', 'Engorda B')->value('fazenda_id');
        $racao = Racao::create(['fazenda_id' => $fazendaId, 'nome_racao' => 'Teste Engano', 'tipo' => 'Bovino', 'estoque' => 0, 'consumo' => 10, 'unidade' => 'kg']);

        $this->web()->delete("/racao/delete/{$racao->id}")->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertNull($racao->fresh());
    }

    public function test_nao_remove_cocho_com_racao_ao_diminuir_a_quantidade(): void
    {
        $lote = Lote::where('nome_lote', 'Engorda B')->firstOrFail();   // 4 cochos com 70 kg cada (seeder)
        $antes = $lote->cochos()->count();

        $this->web()->put("/lote/update/{$lote->id}", [
            'nome_lote' => $lote->nome_lote, 'fazenda_id' => $lote->fazenda_id, 'tipo_animal' => $lote->tipo_animal,
            'finalidade' => $lote->finalidade, 'sexo' => $lote->sexo, 'idade_media' => $lote->idade_media,
            'quantidade_animais' => $lote->quantidade_animais, 'peso_medio' => $lote->peso_medio, 'status' => 'Ativo',
            'raca' => $lote->raca, 'quantidade_cochos' => $antes - 1, 'cocho_capacidade' => 100,
        ])->assertSessionHasErrors('quantidade_cochos');

        $this->assertSame($antes, $lote->cochos()->count());
        $this->assertSame($antes, $lote->fresh()->quantidade_cochos);
    }
}
