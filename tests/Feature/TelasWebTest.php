<?php

namespace Tests\Feature;

use App\Models\Cadastro;
use App\Models\Lote;
use App\Models\Racao;
use Database\Seeders\AgriFarmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * As telas web continuam funcionando com a estrutura revisada (decimais no
 * estoque, histórico de movimentações, capacidade padrão de cocho...).
 */
class TelasWebTest extends TestCase
{
    use RefreshDatabase;

    private Cadastro $demo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AgriFarmDemoSeeder::class);
        $this->demo = Cadastro::where('email', 'demo@agrifarm.com.br')->firstOrFail();
    }

    private function logado()
    {
        return $this->withSession([
            'usuario_id' => $this->demo->id,
            'usuario_nome' => $this->demo->responsavel,
            'usuario_email' => $this->demo->email,
        ]);
    }

    /** @return array<string, array{0: string}> */
    public static function telas(): array
    {
        return [
            'dashboard' => ['/dashboard'],
            'perfil' => ['/perfil'],
            'fazendas' => ['/fazenda/listar'],
            'lotes' => ['/lotes'],
            'racoes' => ['/racao/listar'],
            'estoque' => ['/estoque/listar'],
            'plano' => ['/plano/listar'],
            'cochos' => ['/cocho/listar'],
            'ordenha' => ['/ordenha-leite'],
            'relatorios' => ['/relatorios'],
        ];
    }

    #[DataProvider('telas')]
    public function test_tela_abre_sem_fazenda_e_com_fazenda_selecionada(string $uri): void
    {
        $fazendaId = $this->demo->fazendas()->value('id');

        $this->logado()->get($uri)->assertOk();
        $this->logado()->get($uri . '?fazenda_id=' . $fazendaId)->assertOk();
    }

    public function test_tipos_de_racao_em_cards_e_adicionar_pelo_card(): void
    {
        $fazenda = $this->demo->fazendas()->firstOrFail();

        $this->logado()->get(route('racao.listar'))
            ->assertOk()
            ->assertSee('Tipos de ração')
            ->assertSee('Ração Bovino Leite')
            ->assertSee('Ração Aves de Postura')
            ->assertSee('abrirModalCatalogo(\'bovino-leite\')', false);

        // Mesmo POST do card: sem lote, com o perfil do tipo.
        $this->logado()->post(route('racao.add'), [
            'origem' => 'catalogo',
            'modelo' => 'aves-postura',
            'tipo' => 'Avicultura', 'sexo' => 'Fêmea', 'finalidade' => 'Ovos', 'idade' => '',
            'proteina_percentual' => 16, 'energia_mcal_kg' => 2.75, 'fibra_percentual' => 4,
            'fazenda_id' => $fazenda->id,
            'nome_racao' => 'Postura do Card',
            'estoque' => 80, 'estoque_total' => 400, 'consumo' => 6, 'valor_kg' => 1.6,
        ])->assertRedirect(route('racao.listar'));

        $racao = Racao::where('nome_racao', 'Postura do Card')->firstOrFail();
        $this->assertSame(['Avicultura', 'Fêmea', 'Ovos'], [$racao->tipo, $racao->sexo, $racao->finalidade]);
        $this->assertEquals(80, $racao->estoque);

        // Perfil adulterado (macho + ovos): recusado e o modal do card reabre.
        $this->logado()->from(route('racao.listar'))->post(route('racao.add'), [
            'origem' => 'catalogo', 'modelo' => 'aves-postura',
            'tipo' => 'Avicultura', 'sexo' => 'Macho', 'finalidade' => 'Ovos',
            'fazenda_id' => $fazenda->id, 'nome_racao' => 'Adulterada',
            'estoque' => 1, 'estoque_total' => 10, 'consumo' => 1, 'valor_kg' => 1,
        ])->assertSessionHasErrors('finalidade');
        $this->assertNull(Racao::where('nome_racao', 'Adulterada')->first());
    }

    public function test_cadastro_e_edicao_de_racao_pela_tela_registram_estoque(): void
    {
        $lote = Lote::where('nome_lote', 'Engorda B')->firstOrFail();
        $dados = [
            'fazenda_id' => $lote->fazenda_id,
            'lote_id' => $lote->id,
            'nome_racao' => 'Ração Tela',
            'estoque' => 150.5,
            'estoque_total' => 500,
            'consumo' => 20,
            'valor_kg' => 2.4,
        ];

        $this->logado()->post('/racao/add', $dados)->assertRedirect(route('racao.listar'))->assertSessionHasNoErrors();
        $racao = Racao::where('nome_racao', 'Ração Tela')->firstOrFail();
        $this->assertEquals(150.5, $racao->estoque);
        $this->assertDatabaseHas('estoque_movimentacoes', ['racao_id' => $racao->id, 'tipo' => 'entrada', 'origem' => 'cadastro_racao']);

        $this->logado()->put("/racao/update/{$racao->id}", ['estoque' => 100] + $dados)->assertSessionHasNoErrors();
        $this->assertEquals(100, $racao->fresh()->estoque);
        $this->assertDatabaseHas('estoque_movimentacoes', ['racao_id' => $racao->id, 'tipo' => 'ajuste', 'saldo_anterior' => 150.5, 'saldo_posterior' => 100]);
    }

    public function test_abastecer_cochos_do_lote_pela_tela(): void
    {
        $lote = Lote::where('nome_lote', 'Suínos C')->with('cochos')->firstOrFail(); // 2 cochos vazios de 100 kg
        $racao = Racao::where('nome_racao', 'Ração Suínos Terminação')->firstOrFail(); // 560 kg
        $saldo = $racao->estoque;

        $this->logado()->post("/lote/{$lote->id}/cochos/abastecer", ['racao_id' => $racao->id, 'quantidade_total' => 150])
            ->assertSessionHasNoErrors();

        $this->assertEquals($saldo - 150, $racao->fresh()->estoque);
        $this->assertEquals(150, $lote->cochos()->sum('nivel_atual'));
        $this->assertSame(2, $racao->movimentacoes()->where('origem', 'abastecimento_cocho')->count());
    }
}
