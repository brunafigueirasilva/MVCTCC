<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\MovimentacaoEstoqueRequest;
use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Racao;
use App\Services\EstoqueService;
use App\Support\LoteRegras;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    /**
     * Ração pertence a uma Fazenda, que pertence a um Cadastro (usuário) —
     * mesmo encadeamento usado em Cocho/Ração. Sem isso, `Racao::all()`
     * mostrava rações de QUALQUER usuário do sistema.
     */
    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function racoesDoUsuario()
    {
        return Racao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).

    /**
     * ENTRADA (compra/reposição) e SAÍDA (uso, perda, venda) de ração pela
     * tela de Estoque — mesma validação (MovimentacaoEstoqueRequest) e mesmo
     * serviço (EstoqueService) da API: o histórico registra exatamente o que
     * aconteceu, em vez de um "ajuste" de saldo. Saída nunca deixa negativo.
     */
    public function entrada(MovimentacaoEstoqueRequest $request, EstoqueService $estoque)
    {
        return $this->movimentar($request, $estoque, 'entrada');
    }

    public function saida(MovimentacaoEstoqueRequest $request, EstoqueService $estoque)
    {
        return $this->movimentar($request, $estoque, 'saida');
    }

    private function movimentar(MovimentacaoEstoqueRequest $request, EstoqueService $estoque, string $tipo)
    {
        $racao = $this->racoesDoUsuario()->findOrFail($request->validated('racao_id'));
        $contexto = array_filter([
            'cadastro_id' => $request->cadastroId(),
            'lote_id' => $request->validated('lote_id'),
            'observacao' => $request->validated('observacao'),
            'data_movimentacao' => $request->validated('data_movimentacao'),
            'origem' => 'manual',
        ], fn ($v) => $v !== null);

        $movimentacao = $tipo === 'entrada'
            ? $estoque->entrada($racao, (float) $request->validated('quantidade'), $contexto)
            : $estoque->saida($racao, (float) $request->validated('quantidade'), $contexto);

        $acao = $tipo === 'entrada' ? 'Entrada' : 'Saída';

        return redirect()->route('estoque.listar')->with('success',
            "{$acao} de " . number_format($movimentacao->quantidade, 2, ',', '.') . " kg registrada em \"{$racao->nome_racao}\". Estoque atual: " . number_format($movimentacao->saldo_posterior, 2, ',', '.') . ' kg.'
        );
    }

    /**
     * Tela de Estoque: só exibe, direto da Ração (estoque, valor/kg,
     * status), o que já é gerenciado na tela de Rações — não
     * existe mais um registro de "movimentação" próprio e independente
     * (o antigo model Estoque, com sua própria quantidade/capacidade,
     * duplicava e podia divergir do estoque real da ração).
     * Editar o estoque de uma ração acontece na própria tela de Rações.
     */
    public function listar(Request $request)
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        $racaos = $this->racoesDoUsuario()
            ->with('fazenda')
            ->when($request->filled('fazenda'), fn ($q) => $q->where('fazenda_id', $request->fazenda))
            ->when($request->filled('tipo_animal') && $request->tipo_animal !== 'todos', fn ($q) => $q->where('tipo', $request->tipo_animal))
            ->when($request->filled('sexo') && $request->sexo !== 'todos', fn ($q) => $q->where('sexo', $request->sexo))
            ->when($request->filled('finalidade') && $request->finalidade !== 'todos', fn ($q) => $q->where('finalidade', $request->finalidade))
            ->get();

        $filtrosAplicados = $this->descreverFiltros($request, $fazendas);

        // Listas usadas nos <select> de Tipo de Animal/Sexo/Finalidade do
        // painel "Filtros Avançados" — mesma fonte única (LoteRegras) usada
        // no cadastro de Lote/Ração, pra nunca ficar dessincronizada.
        $tiposAnimais = LoteRegras::tiposAnimais();
        $sexos = LoteRegras::SEXOS;
        $finalidades = LoteRegras::todasFinalidades();

        return view('cadastroestoque', compact(
            'racaos',
            'fazendas',
            'cadastroLogado',
            'filtrosAplicados',
            'tiposAnimais',
            'sexos',
            'finalidades'
        ));
    }

    /**
     * Traduz os filtros da URL em rótulos legíveis para os "chips" de
     * filtro ativo — mesma ideia do RelatorioController/RacaoController.
     */
    private function descreverFiltros(Request $request, $fazendas): array
    {
        $itens = [];

        if ($request->filled('fazenda')) {
            $nome = optional($fazendas->firstWhere('id', (int) $request->fazenda))->nome_fazenda;
            $itens[] = ['chave' => 'fazenda', 'label' => 'Fazenda: ' . ($nome ?? '—')];
        }

        if ($request->filled('tipo_animal') && $request->tipo_animal !== 'todos') {
            $itens[] = ['chave' => 'tipo_animal', 'label' => 'Animal: ' . $request->tipo_animal];
        }

        if ($request->filled('sexo') && $request->sexo !== 'todos') {
            $itens[] = ['chave' => 'sexo', 'label' => 'Sexo: ' . $request->sexo];
        }

        if ($request->filled('finalidade') && $request->finalidade !== 'todos') {
            $itens[] = ['chave' => 'finalidade', 'label' => 'Finalidade: ' . $request->finalidade];
        }

        return $itens;
    }
}
