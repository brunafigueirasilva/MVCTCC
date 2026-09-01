<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use App\Support\LoteRegras;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RacaoController extends Controller
{
    // Cada ração é cadastrada para um tipo de animal (mesma lista usada nos
    // filtros do Relatório) — lista fixa porque o campo é um <select>, não
    // texto livre, então não há risco de grafias divergentes no banco.
    private const TIPOS_ANIMAIS = ['Bovino', 'Ovino', 'Caprino', 'Suíno', 'Avicultura', 'Equino'];

    // Sexo, idade e finalidade da ração usam o mesmo vocabulário do
    // cadastro de Lote (LoteRegras) — evita ter listas divergentes pro
    // mesmo conceito. No formulário, esses três campos são preenchidos
    // automaticamente a partir de um lote escolhido (em vez de digitados
    // à mão), pra garantir que a ração sempre reflita um lote real.

    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function lotesDoUsuario()
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    /** Rações não guardam lote_id — só uma "foto" do lote no momento do
     *  cadastro (fazenda_id, tipo, sexo, idade, finalidade) — então o
     *  vínculo com o usuário logado precisa passar pela fazenda mesmo. */
    private function racoesDoUsuario()
    {
        return Racao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    /** Regra de validação do lote_id — precisa existir E pertencer a uma
     *  fazenda do usuário logado, senão bastaria enviar o id de um lote
     *  alheio pra "herdar" os dados dele numa ração de outra conta. */
    private function regraLoteDoUsuario(): array
    {
        return [
            'required',
            Rule::exists('lotes', 'id')->whereIn('fazenda_id', $this->fazendaIdsDoUsuario()),
        ];
    }

    private function exigirLogin()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return null;
    }

    // LISTAR
    public function listar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $lotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        // Cada ração pertence a uma fazenda e a um tipo de animal — os
        // filtros abaixo seguem o mesmo padrão do modal "Filtros Avançados"
        // da tela de Relatórios.
        $racaos = $this->racoesDoUsuario()
            ->with('fazenda')
            ->when($request->filled('fazenda'), function ($query) use ($request) {
                $query->where('fazenda_id', $request->fazenda);
            })
            ->when($request->filled('tipo_animal') && $request->tipo_animal !== 'todos', function ($query) use ($request) {
                $query->where('tipo', $request->tipo_animal);
            })
            ->get();

        $filtrosAplicados = $this->descreverFiltros($request, $fazendas);

        return view('cadastroracao', compact(
            'racaos',
            'fazendas',
            'lotes',
            'cadastroLogado',
            'filtrosAplicados'
        ) + [
            'tiposAnimais' => self::TIPOS_ANIMAIS,
            'sexos' => LoteRegras::SEXOS,
            'idadesMedias' => LoteRegras::IDADES_MEDIAS,
        ]);
    }

    /**
     * Traduz os filtros da URL em rótulos legíveis, para os "chips" de
     * filtro ativo — mesma ideia do RelatorioController::descreverFiltros.
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

        return $itens;
    }

    // FORMULÁRIO CADASTRAR
    // O cadastro acontece via modal na própria tela de listagem.
    public function cadastrar()
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $racaos = $this->racoesDoUsuario()->with('fazenda')->get();
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $lotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        return view('cadastroracao', compact('racaos', 'fazendas', 'lotes', 'cadastroLogado')
            + [
                'tiposAnimais' => self::TIPOS_ANIMAIS,
                'sexos' => LoteRegras::SEXOS,
                'idadesMedias' => LoteRegras::IDADES_MEDIAS,
            ]);
    }

    // SALVAR
    public function add(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $request->validate([
            'fazenda_id' => ['required', Rule::exists('fazendas', 'id')->whereIn('id', $this->fazendaIdsDoUsuario())],
            'nome_racao' => 'required|string|max:255',
            'lote_id' => $this->regraLoteDoUsuario(),
            'estoque' => 'required|numeric|min:0',
            'estoque_total' => 'required|numeric|min:1|gte:estoque',
            'consumo' => 'required|numeric|min:0',
            'valor_kg' => 'required|numeric|min:0',
            'fornecedor' => 'required|string|max:255',
            'reposicao' => 'required|date'
        ], [
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'estoque.min' => 'O estoque não pode ser negativo.',
            'estoque_total.min' => 'A capacidade total precisa ser maior que zero.',
            'estoque_total.gte' => 'A capacidade total não pode ser menor que o estoque atual.',
            'consumo.min' => 'O consumo não pode ser negativo.',
        ]);

        // Tipo/sexo/idade/finalidade não vêm de campos digitados — são lidos
        // direto do lote escolhido, pra garantir que a ração sempre reflita
        // um lote de verdade (em vez de confiar em campos ocultos do
        // formulário, que poderiam ter sido adulterados no navegador).
        $lote = $this->lotesDoUsuario()->findOrFail($request->lote_id);

        $racao = Racao::create([
            'fazenda_id' => $lote->fazenda_id,
            'nome_racao' => $request->nome_racao,
            'tipo' => $lote->tipo_animal,
            'sexo' => $lote->sexo,
            'idade' => $lote->idade_media,
            'finalidade' => $lote->finalidade,
            'estoque' => $request->estoque,
            'estoque_total' => $request->estoque_total,
            'consumo' => $request->consumo,
            'valor_kg' => $request->valor_kg,
            'fornecedor' => $request->fornecedor,
            'reposicao' => $request->reposicao,
        ]);

        // O usuário já escolheu o lote no próprio formulário de cadastro —
        // pedir pra vincular de novo na tela de Plano Alimentar seria
        // repetir a mesma escolha. Vira automaticamente a ração vinculada
        // deste lote (substituindo a anterior, se houver — o cadastro de
        // uma ração nova pra este lote é a intenção mais recente do
        // usuário sobre o que ele deve comer).
        $lote->update(['racao_id' => $racao->id]);

        return redirect()->route('racao.listar')
            ->with('success', 'Ração cadastrada com sucesso!');
    }

    // FORMULÁRIO EDITAR
    // A edição acontece via modal na própria tela de listagem.
    public function atualizar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $this->racoesDoUsuario()->findOrFail($id);

        return redirect()->route('racao.listar');
    }

    // ATUALIZAR
    public function update(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $request->validate([
            'fazenda_id' => ['required', Rule::exists('fazendas', 'id')->whereIn('id', $this->fazendaIdsDoUsuario())],
            'nome_racao' => 'required|string|max:255',
            'lote_id' => $this->regraLoteDoUsuario(),
            'estoque' => 'required|numeric|min:0',
            'estoque_total' => 'required|numeric|min:1|gte:estoque',
            'consumo' => 'required|numeric|min:0',
            'valor_kg' => 'required|numeric|min:0',
            'fornecedor' => 'required|string|max:255',
            'reposicao' => 'required|date'
        ], [
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'estoque.min' => 'O estoque não pode ser negativo.',
            'estoque_total.min' => 'A capacidade total precisa ser maior que zero.',
            'estoque_total.gte' => 'A capacidade total não pode ser menor que o estoque atual.',
            'consumo.min' => 'O consumo não pode ser negativo.',
        ]);

        $lote = $this->lotesDoUsuario()->findOrFail($request->lote_id);
        $racao = $this->racoesDoUsuario()->findOrFail($id);

        $racao->update([
            'fazenda_id' => $lote->fazenda_id,
            'nome_racao' => $request->nome_racao,
            'tipo' => $lote->tipo_animal,
            'sexo' => $lote->sexo,
            'idade' => $lote->idade_media,
            'finalidade' => $lote->finalidade,
            'estoque' => $request->estoque,
            'estoque_total' => $request->estoque_total,
            'consumo' => $request->consumo,
            'valor_kg' => $request->valor_kg,
            'fornecedor' => $request->fornecedor,
            'reposicao' => $request->reposicao,
        ]);

        return redirect()->route('racao.listar')
            ->with('success', 'Ração atualizada com sucesso!');
    }

    // EXCLUIR
    public function deletar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $racao = $this->racoesDoUsuario()->findOrFail($id);

        $racao->delete();

        return redirect()->route('racao.listar')
            ->with('success', 'Ração excluída com sucesso!');
    }
}