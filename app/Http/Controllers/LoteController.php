<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoteRequest;
use App\Models\Cadastro;
use App\Models\Lote;
use App\Models\Fazenda;
use App\Support\AnimalRegras;
use App\Support\LoteRegras;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    /**
     * Lotes pertencem a uma fazenda, e cada fazenda pertence a um único
     * usuário — então um lote só é "do usuário" através da fazenda dona
     * dele. Toda consulta abaixo passa por aqui para nunca vazar (ou
     * aceitar edição/exclusão de) lotes de fazendas de outra conta.
     */
    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function lotesDoUsuario()
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    private function exigirLogin()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return null;
    }

    /** Opções fixas dos selects, repassadas pra view montar os <option> e o
     *  filtro dinâmico em JS sem duplicar as listas em vários lugares. */
    private function opcoesLote(): array
    {
        return [
            'tiposAnimais' => LoteRegras::tiposAnimais(),
            'finalidadesPorAnimal' => LoteRegras::FINALIDADES_POR_ANIMAL,
            'todasFinalidades' => LoteRegras::todasFinalidades(),
            'sexos' => LoteRegras::SEXOS,
            'sexosComFemea' => LoteRegras::SEXOS_COM_FEMEA,
            'tiposComOrdenha' => LoteRegras::tiposComOrdenha(),
            'idadesMedias' => LoteRegras::IDADES_MEDIAS,
            'statusLote' => LoteRegras::STATUS,
            'faixaPesoPorAnimal' => LoteRegras::FAIXA_PESO_POR_ANIMAL,
            'doencasPorAnimal' => LoteRegras::DOENCAS_POR_ANIMAL,
            'doencaOutra' => LoteRegras::DOENCA_OUTRA,
            'causasMorte' => LoteRegras::CAUSAS_MORTE,
            'causaMorteOutra' => LoteRegras::CAUSA_MORTE_OUTRA,
            // Data mínima aceita no campo "Data de entrada" — sempre a data
            // atual do servidor, nunca a do computador de quem preenche.
            'hoje' => now()->format('Y-m-d'),

            // Opções dos modais "Adicionar Animal"/"Registrar Ordenha" da
            // seção "Animais deste lote" (ver .lote-row-detalhes) — mesma
            // fonte que AnimalController::opcoesAnimal() usa pra tela de
            // detalhe do animal, nunca duplicada/dessincronizada.
            'sexosAnimal' => AnimalRegras::SEXOS,
            'turnosOrdenha' => AnimalRegras::TURNOS_ORDENHA,
        ];
    }

    // ==========================================
    // LISTAGEM (com busca, filtros combinados, ordenação e contadores)
    // ==========================================

    public function listar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        return $this->renderizarListagem($request);
    }

    public function cadastrar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        return $this->renderizarListagem($request);
    }

    private function renderizarListagem(Request $request)
    {
        $query = $this->lotesDoUsuario()->with([
            'fazenda', 'origem', 'ocorrencias.loteDestino', 'ocorrenciasComoDestino',
            'animais' => fn ($q) => $q->orderBy('nome'),
            'animais.lactacaoAtiva',
        ]);

        $filtros = $request->only(['fazenda_id', 'tipo_animal', 'finalidade', 'sexo']);

        $this->aplicarFiltros($query, $filtros);

        $lotes = $query->latest('data_entrada')->latest('id')->get();

        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        $filtrosAplicados = $this->descreverFiltros($filtros, $fazendas);

        return view(
            'cadastrolote',
            compact('lotes', 'fazendas', 'cadastroLogado', 'filtros', 'filtrosAplicados') + $this->opcoesLote()
        );
    }

    /**
     * Traduz os filtros ativos em rótulos legíveis para os "chips" do modal
     * de Filtros Avançados — mesmo padrão do RelatorioController/
     * RacaoController/EstoqueController.
     */
    private function descreverFiltros(array $filtros, $fazendas): array
    {
        $itens = [];

        if (!empty($filtros['fazenda_id'])) {
            $nome = optional($fazendas->firstWhere('id', (int) $filtros['fazenda_id']))->nome_fazenda;
            $itens[] = ['chave' => 'fazenda_id', 'label' => 'Fazenda: ' . ($nome ?? '—')];
        }

        if (!empty($filtros['tipo_animal'])) {
            $itens[] = ['chave' => 'tipo_animal', 'label' => 'Animal: ' . $filtros['tipo_animal']];
        }

        if (!empty($filtros['finalidade'])) {
            $itens[] = ['chave' => 'finalidade', 'label' => 'Finalidade: ' . $filtros['finalidade']];
        }

        if (!empty($filtros['sexo'])) {
            $itens[] = ['chave' => 'sexo', 'label' => 'Sexo: ' . $filtros['sexo']];
        }

        return $itens;
    }

    /** Todos os filtros são combinados com "E" (AND) — quanto mais
     *  preenchidos, mais estreito o resultado. Nenhum filtro preenchido
     *  significa nenhuma cláusula extra, ou seja, mostra tudo. */
    private function aplicarFiltros($query, array $filtros): void
    {
        foreach (['fazenda_id', 'tipo_animal', 'finalidade', 'sexo'] as $campo) {
            if (!empty($filtros[$campo])) {
                $query->where($campo, $filtros[$campo]);
            }
        }
    }

    // ==========================================
    // CRUD
    // ==========================================

    public function add(LoteRequest $request)
    {
        $dados = $request->validated();

        Lote::create([
            'nome_lote' => $dados['nome_lote'],
            'tipo_animal' => $dados['tipo_animal'],
            'finalidade' => $dados['finalidade'],
            'sexo' => $dados['sexo'],
            'idade_media' => $dados['idade_media'],
            'quantidade_animais' => $dados['quantidade_animais'],
            'peso_medio' => $dados['peso_medio'],
            'data_entrada' => $dados['data_entrada'],
            'fazenda_id' => $dados['fazenda_id'],
            'status' => $dados['status'] ?? 'Ativo',
        ]);

        return redirect()->route('lote.listar')->with('success', 'Lote cadastrado com sucesso!');
    }

    // A edição acontece via modal na própria tela de listagem.
    public function atualizar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $this->lotesDoUsuario()->findOrFail($id);

        return redirect()->route('lote.listar');
    }

    public function update(LoteRequest $request, $id)
    {
        $dados = $request->validated();

        $lote = $this->lotesDoUsuario()->findOrFail($id);

        $lote->update([
            'nome_lote' => $dados['nome_lote'],
            'tipo_animal' => $dados['tipo_animal'],
            'finalidade' => $dados['finalidade'],
            'sexo' => $dados['sexo'],
            'idade_media' => $dados['idade_media'],
            'quantidade_animais' => $dados['quantidade_animais'],
            'peso_medio' => $dados['peso_medio'],
            'data_entrada' => $dados['data_entrada'],
            'fazenda_id' => $dados['fazenda_id'],
            'status' => $dados['status'] ?? $lote->status,
        ]);

        return redirect()->route('lote.listar')->with('success', 'Lote atualizado com sucesso!');
    }

    public function deletar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $lote = $this->lotesDoUsuario()->findOrFail($id);

        // Lote de isolamento (criado por "Registrar Doença") aponta de
        // volta pra este lote via lote_origem_id — é o único caminho pra
        // devolver os animais depois (ver registrarRecuperacao()). Excluir
        // o lote de origem agora (ON DELETE SET NULL) deixaria esse
        // isolamento "órfão": os animais ficariam presos nele, sem jeito
        // de voltar pelo fluxo normal, e sem nenhum aviso na hora.
        if ($this->lotesDoUsuario()->where('lote_origem_id', $lote->id)->exists()) {
            return back()->withErrors([
                'lote' => 'Este lote não pode ser excluído: existe um lote de isolamento (criado por "Registrar Doença") vinculado a ele. Devolva ou resolva esse isolamento primeiro.',
            ]);
        }

        $lote->delete();

        return redirect()->route('lote.listar')->with('success', 'Lote excluído com sucesso!');
    }

    // ==========================================
    // ÓBITO (reduz a quantidade do lote automaticamente, sem permitir
    // digitar "-1" direto no campo — fica registrado no histórico do lote).
    // ==========================================

    public function registrarMorte(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $lote = $this->lotesDoUsuario()->findOrFail($id);

        if ($lote->status !== 'Ativo') {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "O lote {$lote->nome_lote} está {$lote->status} — só é possível registrar óbito em lotes ativos."]);
        }

        if ($lote->quantidade_animais < 1) {
            return redirect()->route('lote.listar')
                ->withErrors(['quantidade' => "O lote {$lote->nome_lote} não tem animais ativos para registrar óbito."]);
        }

        $eraIsolamento = (bool) $lote->lote_origem_id;

        // "Corte" não faz sentido num lote de isolamento — animais em
        // tratamento não estão em produção pra abate, então essa causa
        // nem aparece como opção pra esses lotes.
        $causasPermitidas = $eraIsolamento
            ? array_values(array_diff(LoteRegras::CAUSAS_MORTE, ['Corte']))
            : LoteRegras::CAUSAS_MORTE;
        $causasValidas = array_merge($causasPermitidas, [LoteRegras::CAUSA_MORTE_OUTRA]);

        $dados = $request->validate([
            'causa' => ['required', 'string', 'in:' . implode(',', $causasValidas)],
            'causa_outra' => ['required_if:causa,' . LoteRegras::CAUSA_MORTE_OUTRA, 'nullable', 'string', 'max:100'],
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ], [
            'causa.in' => $eraIsolamento
                ? 'Essa causa não é válida para um lote de isolamento (ex.: "Corte" não se aplica a animais em tratamento).'
                : 'Causa de morte inválida.',
            'causa_outra.required_if' => 'Informe a causa da morte.',
        ]);

        $causaFinal = $dados['causa'] === LoteRegras::CAUSA_MORTE_OUTRA
            ? trim($dados['causa_outra'])
            : $dados['causa'];

        // Num lote de isolamento, óbito por "Doença" pede quantos morreram
        // de cada doença ativa ali — assim o óbito também abate do estoque
        // "quem ainda está doente", igual a recuperação já faz.
        $porDoenca = $eraIsolamento && $dados['causa'] === 'Doença';
        $doencaLivre = null;

        if ($porDoenca) {
            $estoquePorDoenca = $lote->estoquePorDoenca();

            $dadosDoenca = $request->validate([
                'doenca' => ['required', 'array', 'min:1'],
                'doenca.*' => ['required', 'string'],
                'quantidade' => ['required', 'array'],
                'quantidade.*' => ['required', 'integer', 'min:0'],
            ]);

            if (count($dadosDoenca['doenca']) !== count($dadosDoenca['quantidade'])) {
                return back()->withErrors(['quantidade' => 'Dados inconsistentes no formulário de óbito.'])->withInput();
            }

            $quantidadePorDoenca = [];
            foreach ($dadosDoenca['doenca'] as $indice => $nomeDoenca) {
                $quantidade = (int) $dadosDoenca['quantidade'][$indice];
                if ($quantidade < 1) {
                    continue;
                }

                if (!array_key_exists($nomeDoenca, $estoquePorDoenca)) {
                    return back()->withErrors(['quantidade' => "\"{$nomeDoenca}\" não está mais entre as doenças ativas de \"{$lote->nome_lote}\"."])->withInput();
                }

                if ($quantidade > $estoquePorDoenca[$nomeDoenca]) {
                    return back()->withErrors(['quantidade' => "\"{$lote->nome_lote}\" tem apenas {$estoquePorDoenca[$nomeDoenca]} animal(is) com \"{$nomeDoenca}\" — não é possível registrar mais óbitos do que isso."])->withInput();
                }

                $quantidadePorDoenca[$nomeDoenca] = $quantidade;
            }

            $totalMortos = array_sum($quantidadePorDoenca);

            if ($totalMortos < 1) {
                return back()->withErrors(['quantidade' => 'Informe ao menos 1 óbito de alguma doença.'])->withInput();
            }
        } else {
            $dadosQuantidade = $request->validate([
                'quantidade' => ['required', 'integer', 'min:1', 'max:' . $lote->quantidade_animais],
                // Opcional: registra qual doença, mesmo sem estoque
                // rastreado (lote normal, nunca passou por isolamento) —
                // só fica de referência no histórico.
                'doenca_livre' => ['nullable', 'string', 'max:100'],
            ], [
                'quantidade.max' => "O lote {$lote->nome_lote} tem apenas {$lote->quantidade_animais} animal(is) — não é possível registrar mais óbitos do que isso.",
            ]);

            $totalMortos = $dadosQuantidade['quantidade'];
            $quantidadePorDoenca = null;
            $doencaLivre = $dadosQuantidade['doenca_livre'] ?? null;
        }

        if ($totalMortos > $lote->quantidade_animais) {
            return back()->withErrors(['quantidade' => "\"{$lote->nome_lote}\" tem apenas {$lote->quantidade_animais} animal(is) — não é possível registrar mais óbitos do que isso."])->withInput();
        }

        $antes = $lote->quantidade_animais;
        $depois = $antes - $totalMortos;

        DB::transaction(function () use ($lote, $dados, $causaFinal, $porDoenca, $quantidadePorDoenca, $totalMortos, $depois, $doencaLivre) {
            if ($porDoenca) {
                $quantidadeAtual = $lote->quantidade_animais;
                foreach ($quantidadePorDoenca as $nomeDoenca => $quantidade) {
                    $antesPasso = $quantidadeAtual;
                    $quantidadeAtual -= $quantidade;

                    $lote->ocorrencias()->create([
                        'tipo' => 'morte',
                        'causa' => 'Doença',
                        'doenca' => $nomeDoenca,
                        'quantidade' => $quantidade,
                        'quantidade_antes' => $antesPasso,
                        'quantidade_depois' => $quantidadeAtual,
                        'data_ocorrencia' => $dados['data_ocorrencia'],
                        'observacao' => $dados['observacao'] ?? null,
                    ]);
                }
            } else {
                $lote->ocorrencias()->create([
                    'tipo' => 'morte',
                    'causa' => $causaFinal,
                    'doenca' => $doencaLivre ?: null,
                    'quantidade' => $totalMortos,
                    'quantidade_antes' => $lote->quantidade_animais,
                    'quantidade_depois' => $depois,
                    'data_ocorrencia' => $dados['data_ocorrencia'],
                    'observacao' => $dados['observacao'] ?? null,
                ]);
            }

            $lote->update(['quantidade_animais' => $depois]);
        });

        $mensagem = "Óbito registrado ({$causaFinal}): {$lote->nome_lote} atualizado de {$antes} para {$depois} animais.";

        return redirect()->route('lote.listar')->with('success', $mensagem);
    }

    // ==========================================
    // DOENÇA (move os animais doentes para um lote de isolamento próprio,
    // criado — ou reaproveitado, se já existir — automaticamente).
    // ==========================================

    public function registrarDoenca(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $lote = $this->lotesDoUsuario()->findOrFail($id);

        if ($lote->status !== 'Ativo') {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "O lote {$lote->nome_lote} está {$lote->status} — só é possível registrar doença em lotes ativos."]);
        }

        if ($lote->quantidade_animais < 1) {
            return redirect()->route('lote.listar')
                ->withErrors(['quantidade' => "O lote {$lote->nome_lote} não tem animais ativos para registrar doença."]);
        }

        // Um lote de isolamento (que já veio de uma separação por doença)
        // não pode gerar outro lote de isolamento — os animais dele só
        // podem voltar pro lote original ("Devolver ao Lote"), nunca ser
        // encaminhados pra um lote novo.
        if ($lote->lote_origem_id) {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "\"{$lote->nome_lote}\" já é um lote de isolamento — use \"Devolver ao Lote\" para retornar os animais ao lote original."]);
        }

        // Cada linha do formulário é um animal doente — "doenca" e
        // "doenca_outra" chegam como arrays paralelos (mesmo índice = mesmo
        // animal), pra permitir que cada um tenha uma doença diferente
        // dentro do mesmo registro, em vez de uma quantidade única com uma
        // única doença pra todo mundo.
        $dados = $request->validate([
            'doenca' => ['required', 'array', 'min:1', 'max:' . $lote->quantidade_animais],
            'doenca.*' => ['required', 'string'],
            'doenca_outra' => ['array'],
            'doenca_outra.*' => ['nullable', 'string', 'max:100'],
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ], [
            'doenca.required' => 'Adicione ao menos um animal doente.',
            'doenca.max' => "O lote {$lote->nome_lote} tem apenas {$lote->quantidade_animais} animal(is) — não é possível registrar mais animais doentes do que isso.",
            'doenca.*.required' => 'Selecione a doença de cada animal.',
        ]);

        $doencasDaEspecie = LoteRegras::doencasPorAnimal($lote->tipo_animal);
        $doencaOutraPorIndice = $dados['doenca_outra'] ?? [];

        // Resolve o nome final de cada animal (troca "Outra" pelo texto
        // digitado) e já valida contra a lista da espécie — o back-end não
        // confia no filtro feito em JS na tela.
        $nomesPorAnimal = [];
        foreach ($dados['doenca'] as $indice => $doencaSelecionada) {
            if ($doencaSelecionada === LoteRegras::DOENCA_OUTRA) {
                $nomeCustom = trim($doencaOutraPorIndice[$indice] ?? '');
                if ($nomeCustom === '') {
                    return back()->withErrors(['doenca_outra' => 'Informe o nome da doença de cada animal marcado como "Outra".'])->withInput();
                }
                $nomesPorAnimal[] = $nomeCustom;
                continue;
            }

            if (!in_array($doencaSelecionada, $doencasDaEspecie, true)) {
                return back()->withErrors(['doenca' => "Doença \"{$doencaSelecionada}\" inválida para o tipo de animal deste lote."])->withInput();
            }

            $nomesPorAnimal[] = $doencaSelecionada;
        }

        // Agrupa animais com a mesma doença — assim o histórico mostra "3
        // animais com Verminose" numa linha só, em vez de 3 linhas
        // idênticas, mesmo que o formulário tenha sido preenchido animal a
        // animal.
        $quantidadePorDoenca = array_count_values($nomesPorAnimal);
        $totalAnimais = count($nomesPorAnimal);
        $nomeLoteDestino = $lote->nome_lote . ' - Doença';
        $antesTotal = $lote->quantidade_animais;

        DB::transaction(function () use ($lote, $quantidadePorDoenca, $totalAnimais, $nomeLoteDestino, $dados) {
            $loteDestino = Lote::where('fazenda_id', $lote->fazenda_id)
                ->where('nome_lote', $nomeLoteDestino)
                ->first();

            if ($loteDestino) {
                $loteDestino->increment('quantidade_animais', $totalAnimais);
            } else {
                $loteDestino = Lote::create([
                    'nome_lote' => $nomeLoteDestino,
                    'tipo_animal' => $lote->tipo_animal,
                    'finalidade' => LoteRegras::FINALIDADE_TRATAMENTO,
                    'sexo' => $lote->sexo,
                    'idade_media' => $lote->idade_media,
                    'quantidade_animais' => $totalAnimais,
                    'peso_medio' => $lote->peso_medio,
                    'data_entrada' => now()->format('Y-m-d'),
                    'fazenda_id' => $lote->fazenda_id,
                    'lote_origem_id' => $lote->id,
                    'status' => 'Ativo',
                ]);
            }

            // Uma ocorrência por doença distinta, decrementando o lote
            // passo a passo — assim o histórico conta a sequência real
            // (ex.: 40 → 38 por Mastite, depois 38 → 37 por Verminose).
            // O espelho ("entrada") fica registrado no lote de isolamento
            // — é o que permite calcular quantos animais ainda estão com
            // cada doença ali dentro, pra pedir isso na hora de devolver.
            $quantidadeAtual = $lote->quantidade_animais;
            $quantidadeDestinoAtual = $loteDestino->quantidade_animais - $totalAnimais;
            foreach ($quantidadePorDoenca as $nomeDoenca => $quantidade) {
                $antes = $quantidadeAtual;
                $quantidadeAtual -= $quantidade;

                $lote->ocorrencias()->create([
                    'tipo' => 'doenca',
                    'doenca' => $nomeDoenca,
                    'quantidade' => $quantidade,
                    'quantidade_antes' => $antes,
                    'quantidade_depois' => $quantidadeAtual,
                    'lote_destino_id' => $loteDestino->id,
                    'data_ocorrencia' => $dados['data_ocorrencia'],
                    'observacao' => $dados['observacao'] ?? null,
                ]);

                $antesDestino = $quantidadeDestinoAtual;
                $quantidadeDestinoAtual += $quantidade;

                $loteDestino->ocorrencias()->create([
                    'tipo' => 'entrada',
                    'doenca' => $nomeDoenca,
                    'quantidade' => $quantidade,
                    'quantidade_antes' => $antesDestino,
                    'quantidade_depois' => $quantidadeDestinoAtual,
                    'lote_destino_id' => $lote->id,
                    'data_ocorrencia' => $dados['data_ocorrencia'],
                    'observacao' => $dados['observacao'] ?? null,
                ]);
            }

            $lote->update(['quantidade_animais' => $quantidadeAtual]);
        });

        $depoisTotal = $antesTotal - $totalAnimais;
        $resumoDoencas = collect($quantidadePorDoenca)
            ->map(fn ($quantidade, $doenca) => "{$quantidade}x {$doenca}")
            ->implode(', ');

        return redirect()->route('lote.listar')
            ->with('success', "Doença registrada: {$totalAnimais} animal(is) do {$lote->nome_lote} ({$resumoDoencas}) foram movidos para \"{$nomeLoteDestino}\". Quantidade atualizada de {$antesTotal} para {$depoisTotal}.");
    }

    // ==========================================
    // RECUPERAÇÃO (devolve animais de um lote de isolamento pro lote de
    // origem — o único destino possível; não existe seleção de lote aqui
    // de propósito, pra nunca permitir mandar o animal pra um lote novo).
    // ==========================================

    public function registrarRecuperacao(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $loteIsolamento = $this->lotesDoUsuario()->findOrFail($id);

        if (!$loteIsolamento->lote_origem_id) {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "\"{$loteIsolamento->nome_lote}\" não é um lote de isolamento — não há lote de origem para devolver animais."]);
        }

        $loteOrigem = $this->lotesDoUsuario()->find($loteIsolamento->lote_origem_id);

        if (!$loteOrigem) {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "O lote original de \"{$loteIsolamento->nome_lote}\" não existe mais — não é possível devolver os animais automaticamente."]);
        }

        if ($loteIsolamento->status !== 'Ativo') {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "\"{$loteIsolamento->nome_lote}\" está {$loteIsolamento->status} — só é possível devolver animais de lotes ativos."]);
        }

        if ($loteOrigem->status !== 'Ativo') {
            return redirect()->route('lote.listar')
                ->withErrors(['status' => "O lote original \"{$loteOrigem->nome_lote}\" está {$loteOrigem->status} — não é possível devolver animais pra um lote inativo."]);
        }

        if ($loteIsolamento->quantidade_animais < 1) {
            return redirect()->route('lote.listar')
                ->withErrors(['quantidade' => "\"{$loteIsolamento->nome_lote}\" não tem animais para devolver."]);
        }

        // O formulário pede a quantidade separada por doença — precisa
        // ficar registrado qual doença cada animal devolvido tinha, pra
        // saber quantos (e de qual doença) continuam doentes no lote de
        // isolamento depois desse retorno parcial.
        $estoquePorDoenca = $loteIsolamento->estoquePorDoenca();

        $dados = $request->validate([
            'doenca' => ['required', 'array', 'min:1'],
            'doenca.*' => ['required', 'string'],
            'quantidade' => ['required', 'array'],
            'quantidade.*' => ['required', 'integer', 'min:0'],
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ]);

        if (count($dados['doenca']) !== count($dados['quantidade'])) {
            return back()->withErrors(['quantidade' => 'Dados inconsistentes no formulário de recuperação.'])->withInput();
        }

        $quantidadePorDoenca = [];
        foreach ($dados['doenca'] as $indice => $nomeDoenca) {
            $quantidade = (int) $dados['quantidade'][$indice];
            if ($quantidade < 1) {
                continue;
            }

            if (!array_key_exists($nomeDoenca, $estoquePorDoenca)) {
                return back()->withErrors(['quantidade' => "\"{$nomeDoenca}\" não está mais entre as doenças ativas de \"{$loteIsolamento->nome_lote}\"."])->withInput();
            }

            if ($quantidade > $estoquePorDoenca[$nomeDoenca]) {
                return back()->withErrors(['quantidade' => "\"{$loteIsolamento->nome_lote}\" tem apenas {$estoquePorDoenca[$nomeDoenca]} animal(is) com \"{$nomeDoenca}\" — não é possível devolver mais do que isso."])->withInput();
            }

            $quantidadePorDoenca[$nomeDoenca] = $quantidade;
        }

        $totalRecuperado = array_sum($quantidadePorDoenca);

        if ($totalRecuperado < 1) {
            return back()->withErrors(['quantidade' => 'Informe ao menos 1 animal recuperado de alguma doença.'])->withInput();
        }

        if ($totalRecuperado > $loteIsolamento->quantidade_animais) {
            return back()->withErrors(['quantidade' => "\"{$loteIsolamento->nome_lote}\" tem apenas {$loteIsolamento->quantidade_animais} animal(is) — não é possível devolver mais do que isso."])->withInput();
        }

        $antesIsolamento = $loteIsolamento->quantidade_animais;

        DB::transaction(function () use ($loteIsolamento, $loteOrigem, $dados, $quantidadePorDoenca, $totalRecuperado) {
            // Uma ocorrência por doença devolvida, registrada no lote de
            // ORIGEM (não no de isolamento) — assim o histórico completo da
            // "ida e volta" fica concentrado no lote que o usuário
            // realmente acompanha no dia a dia.
            $quantidadeOrigemAtual = $loteOrigem->quantidade_animais;
            foreach ($quantidadePorDoenca as $nomeDoenca => $quantidade) {
                $antesOrigem = $quantidadeOrigemAtual;
                $quantidadeOrigemAtual += $quantidade;

                $loteOrigem->ocorrencias()->create([
                    'tipo' => 'recuperacao',
                    'doenca' => $nomeDoenca,
                    'quantidade' => $quantidade,
                    'quantidade_antes' => $antesOrigem,
                    'quantidade_depois' => $quantidadeOrigemAtual,
                    'lote_destino_id' => $loteIsolamento->id,
                    'data_ocorrencia' => $dados['data_ocorrencia'],
                    'observacao' => $dados['observacao'] ?? null,
                ]);
            }

            $loteOrigem->update(['quantidade_animais' => $quantidadeOrigemAtual]);

            $quantidadeIsolamentoFinal = $loteIsolamento->quantidade_animais - $totalRecuperado;
            $loteIsolamento->update(['quantidade_animais' => $quantidadeIsolamentoFinal]);
        });

        $depoisIsolamento = max(0, $antesIsolamento - $totalRecuperado);
        $resumoDoencas = collect($quantidadePorDoenca)
            ->map(fn ($quantidade, $doenca) => "{$quantidade}x {$doenca}")
            ->implode(', ');

        $mensagem = "Recuperação registrada: {$totalRecuperado} animal(is) ({$resumoDoencas}) voltaram de \"{$loteIsolamento->nome_lote}\" para \"{$loteOrigem->nome_lote}\"."
            . " Quantidade em tratamento atualizada de {$antesIsolamento} para {$depoisIsolamento}.";

        return redirect()->route('lote.listar')->with('success', $mensagem);
    }

}
