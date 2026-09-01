<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\Fazenda;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CochoController extends Controller
{
    /**
     * Cocho pertence a um Lote, que pertence a uma Fazenda, que pertence a
     * um Cadastro (usuário). Sem esse encadeamento, `Cocho::all()` (como
     * era antes) mostrava cochos, lotes e fazendas de QUALQUER usuário do
     * sistema pra qualquer pessoa logada — e o formulário de cadastro/edição
     * aceitava um lote_id de outra conta sem checar nada.
     */
    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function lotesDoUsuario()
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    private function cochosDoUsuario()
    {
        return Cocho::whereIn('lote_id', $this->lotesDoUsuario()->pluck('id'));
    }

    private function exigirLogin()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return null;
    }

    private function regraLoteDoUsuario(): array
    {
        return [
            'required',
            Rule::exists('lotes', 'id')->whereIn('fazenda_id', $this->fazendaIdsDoUsuario()),
        ];
    }

    // LISTAR (por lote, com filtros)
    public function listar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        return $this->renderizarListagem($request);
    }

    // TELA CADASTRO
    public function cadastrar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        return $this->renderizarListagem($request);
    }

    private function renderizarListagem(Request $request)
    {
        $filtros = $request->only(['fazenda_id', 'lote_id', 'status']);

        $cochos = $this->cochosDoUsuario()
            ->with('lote.fazenda')
            ->when(!empty($filtros['lote_id']), fn ($q) => $q->where('lote_id', $filtros['lote_id']))
            ->when(!empty($filtros['fazenda_id']), fn ($q) => $q->whereHas('lote', fn ($l) => $l->where('fazenda_id', $filtros['fazenda_id'])))
            ->get()
            ->map(function ($cocho) {
                $cocho->status_abastecimento = $this->statusAbastecimento($cocho);
                return $cocho;
            });

        if (!empty($filtros['status'])) {
            $cochos = $cochos->where('status_abastecimento', $filtros['status'])->values();
        }

        $lotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        $filtrosAplicados = $this->descreverFiltros($filtros, $fazendas, $lotes);

        return view('cadastrococho', compact('cochos', 'lotes', 'fazendas', 'cadastroLogado', 'filtros', 'filtrosAplicados'));
    }

    /**
     * Traduz os filtros ativos em rótulos legíveis para os "chips" do modal
     * de Filtros Avançados — mesmo padrão do RelatorioController/
     * RacaoController/EstoqueController/LoteController.
     */
    private function descreverFiltros(array $filtros, $fazendas, $lotes): array
    {
        $itens = [];

        if (!empty($filtros['fazenda_id'])) {
            $nome = optional($fazendas->firstWhere('id', (int) $filtros['fazenda_id']))->nome_fazenda;
            $itens[] = ['chave' => 'fazenda_id', 'label' => 'Fazenda: ' . ($nome ?? '—')];
        }

        if (!empty($filtros['lote_id'])) {
            $nome = optional($lotes->firstWhere('id', (int) $filtros['lote_id']))->nome_lote;
            $itens[] = ['chave' => 'lote_id', 'label' => 'Lote: ' . ($nome ?? '—')];
        }

        if (!empty($filtros['status'])) {
            $itens[] = ['chave' => 'status', 'label' => 'Status: ' . $filtros['status']];
        }

        return $itens;
    }

    /** Mesmos limiares já usados nos alertas do Dashboard (alertasCochos):
     *  sem ração é crítico, abaixo de 20% da capacidade é baixo. */
    private function statusAbastecimento(Cocho $cocho): string
    {
        if ($cocho->capacidade <= 0) {
            return 'Indefinido';
        }

        if ($cocho->nivel_atual <= 0) {
            return 'Vazio';
        }

        $percentual = ($cocho->nivel_atual / $cocho->capacidade) * 100;

        return $percentual < 20 ? 'Baixo' : 'OK';
    }

    // SALVAR
    public function add(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $dados = $request->validate([
            'lote_id' => $this->regraLoteDoUsuario(),
            'nome_cocho' => 'required|max:255',
            'capacidade' => 'required|numeric|gt:0',
            'nivel_atual' => 'required|numeric|min:0|lte:capacidade',
            'sensor' => 'required',
        ], [
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'capacidade.gt' => 'A capacidade deve ser maior que zero.',
            'nivel_atual.min' => 'O nível atual não pode ser negativo.',
            'nivel_atual.lte' => 'O nível atual não pode ser maior que a capacidade do cocho.',
        ]);

        Cocho::create($dados);

        return redirect()
            ->route('cocho.listar')
            ->with('success', 'Cocho cadastrado com sucesso!');
    }

    // EDITAR
    // A edição acontece via modal na própria tela de listagem.
    public function atualizar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $this->cochosDoUsuario()->findOrFail($id);

        return redirect()->route('cocho.listar');
    }

    // ATUALIZAR
    public function update(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $cocho = $this->cochosDoUsuario()->findOrFail($id);

        $dados = $request->validate([
            'lote_id' => $this->regraLoteDoUsuario(),
            'nome_cocho' => 'required|max:255',
            'capacidade' => 'required|numeric|gt:0',
            'nivel_atual' => 'required|numeric|min:0|lte:capacidade',
            'sensor' => 'required',
        ], [
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'capacidade.gt' => 'A capacidade deve ser maior que zero.',
            'nivel_atual.min' => 'O nível atual não pode ser negativo.',
            'nivel_atual.lte' => 'O nível atual não pode ser maior que a capacidade do cocho.',
        ]);

        $cocho->update($dados);

        return redirect()
            ->route('cocho.listar')
            ->with('success', 'Cocho atualizado com sucesso!');
    }

    // DELETAR
    public function deletar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $cocho = $this->cochosDoUsuario()->findOrFail($id);
        $cocho->delete();

        return redirect()
            ->route('cocho.listar')
            ->with('success', 'Cocho deletado com sucesso!');
    }

    // DETALHES
    public function detalhes($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $this->cochosDoUsuario()->with('lote')->findOrFail($id);

        return redirect()->route('cocho.listar');
    }
}
