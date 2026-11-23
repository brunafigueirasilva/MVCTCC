<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoteRequest;
use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\Lote;
use App\Models\Fazenda;
use App\Support\LoteRegras;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).

    /**
     * Cria ou remove cochos automaticamente pra bater com
     * $lote->quantidade_cochos — o usuário não cadastra cocho nenhum na
     * mão, só diz QUANTOS o lote tem. Cada cocho é identificado por
     * "numero" (posição dentro do lote: 1, 2, 3...), o que permite mudar a
     * quantidade depois sem duplicar nem embaralhar os que já existem:
     *   - Aumentou (ex.: 3 → 5): cria só os que faltam (numero 4 e 5), com
     *     $capacidadeInicial/$nivelInicial (formulário do Lote) — sem
     *     valor informado, nascem com 0/0 pra editar depois.
     *   - Diminuiu (ex.: 5 → 2): remove os de numero MAIS ALTO (4 e 5) —
     *     os que sobram (1 e 2) nunca são tocados, mantendo os dados
     *     (capacidade, nível, sensor) exatamente como estavam — mesmo que
     *     $capacidadeInicial/$nivelInicial venham preenchidos: eles só
     *     valem pros cochos novos desta chamada, nunca sobrescrevem um
     *     cocho que já existia.
     *   - Nome do lote mudou: renomeia os cochos existentes pro nome novo
     *     (ex.: "A1-01" → "A2-01"), já que a identificação é "baseada no
     *     lote" — só o nome muda, capacidade/nível/sensor continuam os
     *     mesmos.
     */
    private function sincronizarCochos(Lote $lote, ?int $quantidadeDesejada, ?float $capacidadeInicial = null, ?float $nivelInicial = null): void
    {
        $quantidadeDesejada = max(0, $quantidadeDesejada ?? 0);
        $capacidadeInicial = ($capacidadeInicial ?? 0) > 0 ? $capacidadeInicial : Cocho::CAPACIDADE_PADRAO;
        $nivelInicial = $nivelInicial ?? 0;

        $cochosAtuais = $lote->cochos()->orderBy('numero')->get();
        $quantidadeAtual = $cochosAtuais->count();

        if ($quantidadeDesejada > $quantidadeAtual) {
            for ($numero = $quantidadeAtual + 1; $numero <= $quantidadeDesejada; $numero++) {
                Cocho::create([
                    'lote_id' => $lote->id,
                    'numero' => $numero,
                    'nome_cocho' => $this->nomeCocho($lote, $numero),
                    'capacidade' => $capacidadeInicial,
                    'nivel_atual' => $nivelInicial,
                    'sensor' => null,
                ]);
            }
        } elseif ($quantidadeDesejada < $quantidadeAtual) {
            $cochosAtuais
                ->sortByDesc('numero')
                ->take($quantidadeAtual - $quantidadeDesejada)
                ->each(fn (Cocho $cocho) => $cocho->delete());
        }

        // Renomeia os que sobraram (incluindo os recém-criados) pro nome
        // atual do lote — cobre tanto uma mudança de nome quanto o cadastro
        // inicial, sem duplicar a lógica de nome em dois lugares.
        $lote->cochos()->get()->each(function (Cocho $cocho) use ($lote) {
            $nomeEsperado = $this->nomeCocho($lote, $cocho->numero);
            if ($cocho->nome_cocho !== $nomeEsperado) {
                $cocho->update(['nome_cocho' => $nomeEsperado]);
            }
        });

        if ($lote->quantidade_cochos !== $quantidadeDesejada) {
            $lote->update(['quantidade_cochos' => $quantidadeDesejada]);
        }
    }

    /** "<nome do lote> - 01", "<nome do lote> - 02"... — mesma identificação em todo lugar que precisar montar o nome de um cocho. */
    private function nomeCocho(Lote $lote, int $numero): string
    {
        return $lote->nome_lote . ' - ' . str_pad((string) $numero, 2, '0', STR_PAD_LEFT);
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
            'tiposComParto' => LoteRegras::tiposComParto(),
            'tiposComPostura' => LoteRegras::tiposComPostura(),
            'tiposComEclosao' => LoteRegras::tiposComEclosao(),
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

            // Opções do modal "Registrar Ordenha" da seção "Ordenha e Leite
            // do Lote" (ver .lote-row-detalhes).
            'turnosOrdenha' => LoteRegras::TURNOS_ORDENHA,
        ];
    }

    // ==========================================
    // LISTAGEM (com busca, filtros combinados, ordenação e contadores)
    // ==========================================

    public function listar(Request $request)
    {
        return $this->renderizarListagem($request);
    }

    public function cadastrar(Request $request)
    {
        return $this->renderizarListagem($request);
    }

    private function renderizarListagem(Request $request)
    {
        $query = $this->lotesDoUsuario()->with([
            'fazenda', 'origem', 'ocorrencias.loteDestino', 'ocorrenciasComoDestino', 'ordenhas',
        ]);

        $filtros = $request->only(['fazenda_id', 'tipo_animal', 'finalidade', 'sexo']);

        $this->aplicarFiltros($query, $filtros);

        $lotes = $query->latest('created_at')->latest('id')->get();

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

        // Lote e cochos juntos: nunca fica um lote "sem os cochos" pedidos.
        DB::transaction(function () use ($dados) {
            $lote = Lote::create([
                'nome_lote' => $dados['nome_lote'],
                'tipo_animal' => $dados['tipo_animal'],
                'raca' => $dados['raca'] ?? null,
                'finalidade' => $dados['finalidade'],
                'sexo' => $dados['sexo'],
                'idade_media' => $dados['idade_media'],
                'quantidade_animais' => $dados['quantidade_animais'],
                'peso_medio' => $dados['peso_medio'],
                'data_entrada' => $dados['data_entrada'] ?? today()->toDateString(),
                'fazenda_id' => $dados['fazenda_id'],
                'status' => $dados['status'] ?? 'Ativo',
            ]);

            $this->sincronizarCochos($lote, $dados['quantidade_cochos'] ?? 0, $dados['cocho_capacidade'] ?? null, $dados['cocho_nivel_atual'] ?? null);
        });

        return redirect()->route('lote.listar')->with('success', 'Lote cadastrado com sucesso!');
    }

    // A edição acontece via modal na própria tela de listagem.
    public function atualizar($id)
    {
        $this->lotesDoUsuario()->findOrFail($id);

        return redirect()->route('lote.listar');
    }

    public function update(LoteRequest $request, $id)
    {
        $dados = $request->validated();

        $lote = $this->lotesDoUsuario()->findOrFail($id);

        // Diminuir a quantidade de cochos remove os de número mais alto. Se
        // algum deles ainda tem ração dentro, ela sumiria sem registro (já
        // saiu do estoque quando o cocho foi abastecido) — recusa e explica.
        $quantidadeCochos = max(0, (int) ($dados['quantidade_cochos'] ?? 0));
        $removidos = $lote->cochos()->orderByDesc('numero')
            ->take(max(0, $lote->cochos()->count() - $quantidadeCochos))
            ->withCount(['sensores' => fn ($q) => $q->where('ativo', true)])
            ->get();
        $comRacao = $removidos->filter(fn (Cocho $c) => $c->nivel_atual > 0);

        if ($comRacao->isNotEmpty()) {
            return back()->withErrors([
                'quantidade_cochos' => 'Não dá para remover ' . $comRacao->pluck('nome_cocho')->implode(', ')
                    . ' porque ainda tem ração dentro (' . number_format($comRacao->sum('nivel_atual'), 1, ',', '.') . ' kg). '
                    . 'Espere o lote consumir ou zere o nível do cocho na Gestão de Cochos antes de diminuir a quantidade.',
            ])->withInput();
        }

        // Cocho com sensor IoT ativo: removê-lo apagaria em cascata os
        // sensores e todo o histórico de leituras, e o ESP32 passaria a ter
        // as leituras recusadas — sem nenhum aviso.
        $comSensor = $removidos->filter(fn (Cocho $c) => $c->sensores_count > 0);
        if ($comSensor->isNotEmpty()) {
            return back()->withErrors([
                'quantidade_cochos' => 'Não dá para remover ' . $comSensor->pluck('nome_cocho')->implode(', ')
                    . ' porque tem sensor IoT ligado (removê-lo apagaria o histórico de leituras). '
                    . 'Retire os códigos dos sensores em "Editar" na Gestão de Cochos antes de diminuir a quantidade.',
            ])->withInput();
        }

        // Lote com ordenhas registradas não pode virar "Macho" (nem espécie
        // que não dá leite): o histórico de leite ficaria num lote que, pela
        // regra, nunca poderia ter ordenha. O banco também barra (trigger).
        if (!LoteRegras::loteAceitaOrdenha($dados['tipo_animal'], $dados['sexo']) && $lote->ordenhas()->exists()) {
            return back()->withErrors([
                'sexo' => "O lote {$lote->nome_lote} tem ordenhas registradas — não é possível mudar para sexo "
                    . "{$dados['sexo']} / espécie {$dados['tipo_animal']}. Exclua as ordenhas antes, se for o caso.",
            ])->withInput();
        }

        // Trocar a fazenda de um lote deixaria a ração vinculada, os planos e
        // o histórico de estoque na fazenda antiga (dados da Fazenda A
        // aparecendo na B). Só um lote "limpo" pode mudar de fazenda.
        if ((int) $dados['fazenda_id'] !== (int) $lote->fazenda_id) {
            $preso = $lote->racao_id
                || $lote->planos()->exists()
                || $lote->movimentacoesEstoque()->exists()
                || $lote->ordenhas()->exists()
                || $lote->ocorrencias()->exists()
                || $lote->cochos()->where('nivel_atual', '>', 0)->exists();

            if ($preso) {
                return back()->withErrors([
                    'fazenda_id' => "O lote {$lote->nome_lote} já tem ração vinculada, plano alimentar, cochos com ração ou histórico nesta fazenda — não é possível mudá-lo de fazenda. Cadastre um lote novo na outra fazenda.",
                ])->withInput();
            }
        }

        // Data de entrada não pode ficar depois do primeiro registro do lote.
        if (!empty($dados['data_entrada'])) {
            $primeiro = collect([
                $lote->ocorrencias()->min('data_ocorrencia'),
                $lote->ordenhas()->min('data'),
            ])->filter()->map(fn ($d) => \Carbon\Carbon::parse($d)->startOfDay())->min();

            if ($primeiro && \Carbon\Carbon::parse($dados['data_entrada'])->startOfDay()->gt($primeiro)) {
                return back()->withErrors([
                    'data_entrada' => 'A data de entrada não pode ser depois do primeiro registro do lote (' . $primeiro->format('d/m/Y') . ').',
                ])->withInput();
            }
        }

        $lote->update([
            'data_entrada' => $dados['data_entrada'] ?? $lote->data_entrada,
            'nome_lote' => $dados['nome_lote'],
            'tipo_animal' => $dados['tipo_animal'],
            'raca' => $dados['raca'] ?? null,
            'finalidade' => $dados['finalidade'],
            'sexo' => $dados['sexo'],
            'idade_media' => $dados['idade_media'],
            'quantidade_animais' => $dados['quantidade_animais'],
            'peso_medio' => $dados['peso_medio'],
            'fazenda_id' => $dados['fazenda_id'],
            'status' => $dados['status'] ?? $lote->status,
        ]);

        $this->sincronizarCochos($lote, $dados['quantidade_cochos'] ?? 0, $dados['cocho_capacidade'] ?? null, $dados['cocho_nivel_atual'] ?? null);

        return redirect()->route('lote.listar')->with('success', 'Lote atualizado com sucesso!');
    }

    public function deletar($id)
    {
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

        // Cocho com ração: ela já saiu do armazém e sumiria sem registro.
        $comRacao = $lote->cochos()->where('nivel_atual', '>', 0)->count();
        if ($comRacao > 0) {
            return back()->withErrors([
                'lote' => "O lote \"{$lote->nome_lote}\" não pode ser excluído: {$comRacao} cocho(s) ainda têm ração. Registre o consumo (ou esvazie os cochos) antes.",
            ]);
        }

        // Com histórico (ordenhas, ocorrências, consumo, estoque, pesagens),
        // a exclusão apagaria esse histórico em cascata — o certo é encerrar
        // o lote (exclusão lógica: status "Encerrado" mantém tudo).
        $temHistorico = $lote->ordenhas()->exists()
            || $lote->ocorrencias()->exists()
            || $lote->movimentacoesEstoque()->exists()
            || \App\Models\CochoConsumo::where('lote_id', $lote->id)->exists()
            || \App\Models\DesempenhoLote::where('lote_id', $lote->id)->exists()
            || \App\Models\LoteConsumoSnapshot::where('lote_id', $lote->id)->where('consumo_kg', '>', 0)->exists()
            || \App\Models\LeituraIot::whereIn('cocho_id', $lote->cochos()->pluck('id'))->exists();

        if ($temHistorico) {
            return back()->withErrors([
                'lote' => "O lote \"{$lote->nome_lote}\" tem histórico registrado (ordenhas, ocorrências, consumo ou estoque) e não pode ser excluído. Edite o lote e mude o status para \"Encerrado\" — ele sai das telas e dos cálculos, e o histórico fica preservado.",
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
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($lote)],
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
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($lote)],
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
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($loteIsolamento)],
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

    // ==========================================
    // PARTO (aumenta a quantidade do lote automaticamente — mesmo espírito
    // do óbito, só que inverso: soma em vez de subtrai. Fica registrado no
    // histórico do lote como qualquer outra ocorrência. Sempre por LOTE,
    // sem nenhum vínculo com animal individual.)
    // ==========================================

    /** Só lotes ativos, com animais, de espécie que tem parto (mamíferos —
     *  não se aplica à Avicultura), com pelo menos uma fêmea, e que não
     *  sejam um lote de isolamento (devolva os animais antes). */
    private function motivoBloqueioParto(Lote $lote): ?string
    {
        if ($lote->status !== 'Ativo') {
            return "O lote {$lote->nome_lote} está {$lote->status} — só é possível registrar parto em lotes ativos.";
        }

        if ($lote->quantidade_animais < 1) {
            return "O lote {$lote->nome_lote} não tem animais ativos para registrar parto.";
        }

        if ((bool) $lote->lote_origem_id) {
            return "\"{$lote->nome_lote}\" é um lote de isolamento — devolva os animais ao lote de origem antes de registrar parto.";
        }

        if (!LoteRegras::tipoTemParto($lote->tipo_animal)) {
            return "Lotes de {$lote->tipo_animal} não têm parto registrado neste sistema.";
        }

        if (!in_array($lote->sexo, LoteRegras::SEXOS_COM_FEMEA, true)) {
            return "Parto só é possível em lotes com fêmeas (sexo Fêmea ou Misto).";
        }

        return null;
    }

    public function registrarParto(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);

        if ($motivo = $this->motivoBloqueioParto($lote)) {
            return redirect()->route('lote.listar')->withErrors(['parto' => $motivo]);
        }

        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($lote)],
            'observacao' => ['nullable', 'string', 'max:255'],
        ], [
            'quantidade.min' => 'Informe ao menos 1 cria nascida.',
        ]);

        $antes = $lote->quantidade_animais;
        $depois = $antes + $dados['quantidade'];

        DB::transaction(function () use ($lote, $dados, $antes, $depois) {
            $lote->ocorrencias()->create([
                'tipo' => 'parto',
                'quantidade' => $dados['quantidade'],
                'quantidade_antes' => $antes,
                'quantidade_depois' => $depois,
                'data_ocorrencia' => $dados['data_ocorrencia'],
                'observacao' => $dados['observacao'] ?? null,
            ]);

            $lote->update(['quantidade_animais' => $depois]);
        });

        $mensagem = "Parto registrado: {$dados['quantidade']} cria(s) nascida(s) no {$lote->nome_lote}. Quantidade atualizada de {$antes} para {$depois}.";

        return redirect()->route('lote.listar')->with('success', $mensagem);
    }

    // ==========================================
    // ECLOSÃO (equivalente ao Parto, só para Avicultura — ovos postos que
    // chocam e viram pintinhos, aumentando a quantidade do lote. Mesmo
    // espírito do óbito, só que inverso: soma em vez de subtrai.)
    // ==========================================

    /** Só lotes ativos, com animais, de espécie que tem eclosão (só
     *  Avicultura), com pelo menos uma fêmea, e que não sejam um lote de
     *  isolamento (devolva os animais antes). */
    private function motivoBloqueioEclosao(Lote $lote): ?string
    {
        if ($lote->status !== 'Ativo') {
            return "O lote {$lote->nome_lote} está {$lote->status} — só é possível registrar eclosão em lotes ativos.";
        }

        if ($lote->quantidade_animais < 1) {
            return "O lote {$lote->nome_lote} não tem animais ativos para registrar eclosão.";
        }

        if ((bool) $lote->lote_origem_id) {
            return "\"{$lote->nome_lote}\" é um lote de isolamento — devolva os animais ao lote de origem antes de registrar eclosão.";
        }

        if (!LoteRegras::tipoTemEclosao($lote->tipo_animal)) {
            return 'Só lotes de Avicultura têm eclosão registrada neste sistema.';
        }

        if (!in_array($lote->sexo, LoteRegras::SEXOS_COM_FEMEA, true)) {
            return 'Eclosão só é possível em lotes com fêmeas (sexo Fêmea ou Misto).';
        }

        return null;
    }

    public function registrarEclosao(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);

        if ($motivo = $this->motivoBloqueioEclosao($lote)) {
            return redirect()->route('lote.listar')->withErrors(['eclosao' => $motivo]);
        }

        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($lote)],
            'observacao' => ['nullable', 'string', 'max:255'],
        ], [
            'quantidade.min' => 'Informe ao menos 1 pintinho eclodido.',
        ]);

        $antes = $lote->quantidade_animais;
        $depois = $antes + $dados['quantidade'];

        DB::transaction(function () use ($lote, $dados, $antes, $depois) {
            $lote->ocorrencias()->create([
                'tipo' => 'eclosao',
                'quantidade' => $dados['quantidade'],
                'quantidade_antes' => $antes,
                'quantidade_depois' => $depois,
                'data_ocorrencia' => $dados['data_ocorrencia'],
                'observacao' => $dados['observacao'] ?? null,
            ]);

            $lote->update(['quantidade_animais' => $depois]);
        });

        $mensagem = "Eclosão registrada: {$dados['quantidade']} pintinho(s) no {$lote->nome_lote}. Quantidade atualizada de {$antes} para {$depois}.";

        return redirect()->route('lote.listar')->with('success', $mensagem);
    }

    // ==========================================
    // POSTURA (quantidade de ovos do dia — só Avicultura). Não altera a
    // quantidade de animais do lote (ovo não é cabeça de animal); fica
    // registrada como ocorrência simples, igual Parto/Óbito, sempre por
    // LOTE, sem nenhum vínculo com animal individual.
    // ==========================================

    /** Só lotes ativos, com animais, de espécie que põe ovos (só
     *  Avicultura), com pelo menos uma fêmea, e que não sejam um lote de
     *  isolamento. */
    private function motivoBloqueioPostura(Lote $lote): ?string
    {
        if ($lote->status !== 'Ativo') {
            return "O lote {$lote->nome_lote} está {$lote->status} — só é possível registrar postura em lotes ativos.";
        }

        if ($lote->quantidade_animais < 1) {
            return "O lote {$lote->nome_lote} não tem animais ativos para registrar postura.";
        }

        if ((bool) $lote->lote_origem_id) {
            return "\"{$lote->nome_lote}\" é um lote de isolamento — devolva os animais ao lote de origem antes de registrar postura.";
        }

        if (!LoteRegras::tipoTemPostura($lote->tipo_animal)) {
            return 'Só lotes de Avicultura têm postura de ovos registrada neste sistema.';
        }

        if (!in_array($lote->sexo, LoteRegras::SEXOS_COM_FEMEA, true)) {
            return 'Postura só é possível em lotes com fêmeas (sexo Fêmea ou Misto).';
        }

        return null;
    }

    public function registrarPostura(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);

        if ($motivo = $this->motivoBloqueioPostura($lote)) {
            return redirect()->route('lote.listar')->withErrors(['postura' => $motivo]);
        }

        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
            'data_ocorrencia' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($lote)],
            'observacao' => ['nullable', 'string', 'max:255'],
        ], [
            'quantidade.min' => 'Informe ao menos 1 ovo.',
        ]);

        // quantidade_antes/quantidade_depois do lote não mudam (ovo não é
        // cabeça de animal) — só ficam preenchidos porque a coluna é
        // obrigatória; "quantidade" aqui é a quantidade de ovos do dia.
        $lote->ocorrencias()->create([
            'tipo' => 'postura',
            'quantidade' => $dados['quantidade'],
            'quantidade_antes' => $lote->quantidade_animais,
            'quantidade_depois' => $lote->quantidade_animais,
            'data_ocorrencia' => $dados['data_ocorrencia'],
            'observacao' => $dados['observacao'] ?? null,
        ]);

        return redirect()->route('lote.listar')->with('success', "Postura registrada: {$dados['quantidade']} ovo(s) no {$lote->nome_lote}.");
    }

    // ==========================================
    // ORDENHA E LEITE (produção de leite controlada exclusivamente por
    // LOTE — sem nenhum vínculo com animal individual).
    // ==========================================

    private const HISTORICO_ORDENHA_INICIAL = 10;

    /** Só lotes ativos, de espécie que produz leite e com pelo menos uma
     *  fêmea (sexo "Fêmea" ou "Misto") podem registrar ordenha. */
    private function motivoBloqueioOrdenha(Lote $lote): ?string
    {
        if ($lote->status !== 'Ativo') {
            return "O lote {$lote->nome_lote} está {$lote->status} — só é possível registrar ordenha em lotes ativos.";
        }

        if (!LoteRegras::tipoTemOrdenha($lote->tipo_animal)) {
            return "Lotes de {$lote->tipo_animal} não produzem leite — ordenha não se aplica a este lote.";
        }

        if (!in_array($lote->sexo, LoteRegras::SEXOS_COM_FEMEA, true)) {
            return "Ordenha só é possível em lotes com fêmeas (sexo Fêmea ou Misto).";
        }

        return null;
    }

    /**
     * Ocorrência/ordenha não pode ter data anterior à entrada do lote na
     * fazenda (lote sem data de entrada: sem essa trava).
     */
    private function regraAposEntrada(?Lote $lote): array
    {
        if (!$lote?->data_entrada) {
            return [];
        }

        $entrada = $lote->data_entrada->copy()->startOfDay();

        return [function ($atributo, $valor, $falhar) use ($entrada) {
            if (strtotime((string) $valor) !== false && \Carbon\Carbon::parse($valor)->startOfDay()->lt($entrada)) {
                $falhar('A data não pode ser anterior à entrada do lote na fazenda (' . $entrada->format('d/m/Y') . ').');
            }
        }];
    }

    private function validarDadosOrdenha(Request $request, ?Lote $lote = null): array
    {
        $dados = $request->validate([
            'data' => ['required', 'date', 'before_or_equal:today', ...$this->regraAposEntrada($lote)],
            'horario' => ['required', 'date_format:H:i'],
            'turno' => ['required', Rule::in(LoteRegras::TURNOS_ORDENHA)],
            'quantidade_litros' => ['required', 'numeric', 'gt:0'],
            'observacoes' => ['nullable', 'string', 'max:255'],
        ], [
            'data.before_or_equal' => 'Não é possível registrar ordenha com data futura.',
            'turno.in' => 'Turno inválido.',
            'quantidade_litros.gt' => 'A quantidade de litros deve ser maior que zero.',
        ]);

        // O turno tem que bater com o horário (Manhã a partir das 04:00,
        // Tarde a partir das 12:00, Noite a partir das 18:00) — o banco
        // também recusa (CHECK), aqui a mensagem é amigável.
        $turnoCerto = LoteRegras::turnoPorHorario($dados['horario']);
        if ($dados['turno'] !== $turnoCerto) {
            throw ValidationException::withMessages([
                'turno' => "O horário {$dados['horario']} pertence ao turno da {$turnoCerto} "
                    . '(Manhã a partir das 04:00, Tarde a partir das 12:00, Noite a partir das 18:00).',
            ]);
        }

        return $dados;
    }

    public function registrarOrdenha(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);

        if ($motivo = $this->motivoBloqueioOrdenha($lote)) {
            return redirect()->route('lote.listar')->withErrors(['ordenha' => $motivo]);
        }

        $dados = $this->validarDadosOrdenha($request, $lote);

        $duplicada = $lote->ordenhas()
            ->whereDate('data', $dados['data'])
            ->where('turno', $dados['turno'])
            ->where('horario', $dados['horario'])
            ->exists();

        if ($duplicada) {
            return back()->withErrors(['ordenha' => 'Já existe uma ordenha registrada para este lote nessa data, turno e horário.'])->withInput();
        }

        $lote->ordenhas()->create([
            'data' => $dados['data'],
            'horario' => $dados['horario'],
            'turno' => $dados['turno'],
            'quantidade_litros' => $dados['quantidade_litros'],
            'responsavel_id' => session('usuario_id'),
            'observacoes' => $dados['observacoes'] ?? null,
        ]);

        return redirect()->route('lote.listar')->with('success', "Ordenha registrada: {$dados['quantidade_litros']} L no lote {$lote->nome_lote}.");
    }

    public function atualizarOrdenha(Request $request, $id, $ordenhaId)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);
        $ordenha = $lote->ordenhas()->findOrFail($ordenhaId);

        if ($motivo = $this->motivoBloqueioOrdenha($lote)) {
            return redirect()->route('lote.listar')->withErrors(['ordenha' => $motivo]);
        }

        $dados = $this->validarDadosOrdenha($request, $lote);

        $duplicada = $lote->ordenhas()
            ->where('id', '!=', $ordenha->id)
            ->whereDate('data', $dados['data'])
            ->where('turno', $dados['turno'])
            ->where('horario', $dados['horario'])
            ->exists();

        if ($duplicada) {
            return back()->withErrors(['ordenha' => 'Já existe uma ordenha registrada para este lote nessa data, turno e horário.'])->withInput();
        }

        $ordenha->update([
            'data' => $dados['data'],
            'horario' => $dados['horario'],
            'turno' => $dados['turno'],
            'quantidade_litros' => $dados['quantidade_litros'],
            'observacoes' => $dados['observacoes'] ?? null,
        ]);

        return redirect()->route('lote.listar')->with('success', "Ordenha atualizada no lote {$lote->nome_lote}.");
    }

    public function excluirOrdenha($id, $ordenhaId)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);
        $ordenha = $lote->ordenhas()->findOrFail($ordenhaId);

        $ordenha->delete();

        return redirect()->route('lote.listar')->with('success', "Ordenha excluída do lote {$lote->nome_lote}.");
    }

    /** CONSULTAR MAIS ORDENHAS do lote (fora das HISTORICO_ORDENHA_INICIAL
     *  mais recentes já exibidas), com filtro opcional de período — mesmo
     *  padrão de "ver mais" já usado na tela Ordenha e Leite. */
    public function historicoOrdenha(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($id);

        $dataInicio = $request->query('data_inicio');
        $dataFim = $request->query('data_fim');

        $query = $lote->ordenhas();

        if ($dataInicio) {
            $query->whereDate('data', '>=', $dataInicio);
        }
        if ($dataFim) {
            $query->whereDate('data', '<=', $dataFim);
        }

        $historico = $query->get();
        $restante = $historico->slice(self::HISTORICO_ORDENHA_INICIAL)->values();

        return response()->json([
            'success' => true,
            'html' => view('partials.ordenhas-lote-linhas', ['ordenhas' => $restante, 'lote' => $lote])->render(),
        ]);
    }

}
