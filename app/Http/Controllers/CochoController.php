<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use App\Models\Cocho;
use App\Models\CochoConsumo;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use App\Services\CochoIotService;
use App\Services\EstoqueService;
use App\Support\CompatibilidadeRacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CochoController extends Controller
{
    /**
     * Cocho pertence a um Lote, que pertence a uma Fazenda, que pertence a
     * um Cadastro (usuário). Sem esse encadeamento, `Cocho::all()` (como
     * era antes) mostrava cochos, lotes e fazendas de QUALQUER usuário do
     * sistema pra qualquer pessoa logada.
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

    private function racoesDoUsuario()
    {
        return Racao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).

    // LISTAR (agrupado por Lote, com filtros)
    public function listar(Request $request)
    {
        return $this->renderizarListagem($request);
    }

    // TELA CADASTRO (mesma tela de listagem — não existe mais cadastro
    // manual de cocho individual, ver LoteController::sincronizarCochos()).
    public function cadastrar(Request $request)
    {
        return $this->renderizarListagem($request);
    }

    /**
     * Monta a listagem AGRUPADA POR LOTE: cada grupo mostra fazenda, lote,
     * tipo de animal e quantidade de animais (contexto do lote) e, dentro
     * dele, os cochos pertencentes (cada um com sua própria capacidade,
     * nível, sensor e status). O filtro de "status" opera nos cochos
     * dentro de cada grupo — um lote só desaparece da lista se NENHUM
     * cocho dele bater com o status escolhido.
     */
    private function renderizarListagem(Request $request)
    {
        $filtros = $request->only(['fazenda_id', 'lote_id', 'status']);

        $lotes = $this->lotesDoUsuario()
            ->with(['fazenda', 'cochos.sensores'])
            ->when(!empty($filtros['fazenda_id']), fn ($q) => $q->where('fazenda_id', $filtros['fazenda_id']))
            ->when(!empty($filtros['lote_id']), fn ($q) => $q->where('id', $filtros['lote_id']))
            ->orderBy('nome_lote')
            ->get();

        $gruposPorLote = $lotes->map(function (Lote $lote) {
            $cochos = $lote->cochos->map(function (Cocho $cocho) {
                $cocho->status_abastecimento = $this->statusAbastecimento($cocho);
                return $cocho;
            });

            return (object) [
                'lote' => $lote,
                'cochos' => $cochos,
            ];
        });

        if (!empty($filtros['status'])) {
            $gruposPorLote = $gruposPorLote
                ->map(function ($grupo) use ($filtros) {
                    $grupo->cochos = $grupo->cochos->where('status_abastecimento', $filtros['status'])->values();
                    return $grupo;
                })
                ->filter(fn ($grupo) => $grupo->cochos->isNotEmpty())
                ->values();
        }

        $todosLotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        // Pro modal "Abastecer": lista de rações por fazenda (mesmo padrão
        // de cascata Fazenda → Lote/Ração já usado no cadastro de Lote e
        // Ração) — cada cocho só pode ser abastecido com uma ração da
        // MESMA fazenda do seu lote.
        $racoes = $this->racoesDoUsuario()->orderBy('nome_racao')->get();

        // E, dentro da fazenda, só as rações COMPATÍVEIS com o lote (tipo de
        // animal, sexo, finalidade, status) — mesma regra do vínculo no plano
        // alimentar; abastecer() e abastecerLote() validam de novo.
        $racoesCompativeisPorLote = $todosLotes->mapWithKeys(fn (Lote $lote) => [
            $lote->id => CompatibilidadeRacao::filtrar($lote, $racoes)->pluck('id')->all(),
        ]);

        $totalCochos = $gruposPorLote->sum(fn ($grupo) => $grupo->cochos->count());
        $filtrosAplicados = $this->descreverFiltros($filtros, $fazendas, $todosLotes);

        return view('cadastrococho', [
            'gruposPorLote' => $gruposPorLote,
            'totalCochos' => $totalCochos,
            'lotes' => $todosLotes,
            'fazendas' => $fazendas,
            'racoes' => $racoes,
            'racoesCompativeisPorLote' => $racoesCompativeisPorLote,
            'cadastroLogado' => $cadastroLogado,
            'filtros' => $filtros,
            'filtrosAplicados' => $filtrosAplicados,
        ]);
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

    /**
     * Mesmos limiares já usados nos alertas do Dashboard (alertasCochos):
     * sem capacidade configurada ainda é "Indefinido" (cocho recém-criado
     * automaticamente, à espera de ser editado — ver LoteController::
     * sincronizarCochos()), sem ração é crítico, abaixo de 20% da
     * capacidade é baixo.
     */
    private function statusAbastecimento(Cocho $cocho): string
    {
        // Regra única (limiar de Cocho::PERCENTUAL_ALERTA) — a mesma da API
        // e dos alertas do Dashboard.
        return $cocho->statusAbastecimento();
    }

    // EDITAR (modal na própria tela de listagem — só capacidade, nível
    // atual e sensor: lote e identificação (numero/nome) são geridos pelo
    // Lote, ver LoteController::sincronizarCochos()).
    public function atualizar($id)
    {
        $this->cochosDoUsuario()->findOrFail($id);

        return redirect()->route('cocho.listar');
    }

    public function update(Request $request, CochoIotService $iot, EstoqueService $estoque, $id)
    {
        $cocho = $this->cochosDoUsuario()->findOrFail($id);

        $dados = $request->validate([
            'capacidade' => 'required|numeric|gt:0|max:100000',
            'nivel_atual' => 'required|numeric|min:0|lte:capacidade',
            'localizacao' => 'nullable|string|max:150',
            // Sensores IoT do cocho (códigos que o ESP32 envia) e a
            // calibração do sensor de distância — ver CochoIotService.
            'sensor_distancia' => 'nullable|string|max:50',
            'sensor_balanca' => 'nullable|string|max:50',
            'distancia_vazio_cm' => 'nullable|numeric|gt:0|max:' . config('iot.distancia_maxima_cm', 500),
            'distancia_cheio_cm' => 'nullable|numeric|min:0|lt:distancia_vazio_cm',
        ], [
            'capacidade.gt' => 'A capacidade deve ser maior que zero.',
            'nivel_atual.min' => 'O nível atual não pode ser negativo.',
            'nivel_atual.lte' => 'O nível atual não pode ser maior que a capacidade do cocho.',
            'distancia_vazio_cm.gt' => 'A distância com o cocho vazio deve ser maior que zero.',
            'distancia_cheio_cm.lt' => 'A distância com o cocho cheio deve ser menor que a distância com ele vazio.',
        ]);

        // Ração só ENTRA no cocho pelo "Abastecer" (que desconta do armazém).
        // Aumentar o nível pela edição criaria ração que nunca saiu do
        // estoque — recusado. Baixar o nível é consumo: vai pro histórico.
        $nivelAnterior = round((float) $cocho->nivel_atual, 2);
        $nivelNovo = round((float) $dados['nivel_atual'], 2);

        if ($nivelNovo > $nivelAnterior) {
            return back()->withErrors([
                'nivel_atual' => 'Para colocar ração no cocho use "Abastecer" — assim ela é descontada do estoque do armazém. O nível atual é de ' . number_format($nivelAnterior, 2, ',', '.') . ' kg.',
            ])->withInput();
        }

        DB::transaction(function () use ($cocho, $dados, $request, $iot, $estoque, $nivelAnterior, $nivelNovo) {
            if ($nivelNovo < $nivelAnterior) {
                $estoque->registrarConsumoCocho($cocho, $nivelAnterior - $nivelNovo, [
                    'origem' => CochoConsumo::ORIGEM_AJUSTE_NIVEL,
                    'observacao' => 'Nível ajustado na edição do cocho.',
                ]);
            }

            $cocho->update(['capacidade' => $dados['capacidade']]
                // Só muda quando enviada (versões antigas do app não mandam).
                + ($request->has('localizacao') ? ['localizacao' => $dados['localizacao'] ?? null] : []));

            // Só mexe nos sensores quando o formulário/app envia esses campos
            // (versões antigas do app mandam só capacidade/nível).
            if ($request->hasAny(['sensor_distancia', 'sensor_balanca'])) {
                $iot->salvarSensores($cocho, $dados);
            }
        });

        return redirect()
            ->route('cocho.listar')
            ->with('success', 'Cocho atualizado com sucesso!');
    }

    // DETALHES / MONITORAMENTO IoT — a página só traz a identificação do
    // cocho; todos os valores (nível, sensores, gráficos, indicadores) são
    // carregados pelo JS a partir de iot()/iotHistorico() abaixo.
    public function detalhes($id)
    {
        $cocho = $this->cochosDoUsuario()->with(['lote.fazenda'])->findOrFail($id);

        return view('cochodetalhe', [
            'cocho' => $cocho,
            'cadastroLogado' => Cadastro::find(session('usuario_id')),
        ]);
    }

    /** JSON da situação atual (mesmo conteúdo de GET /api/cochos/{id}/iot). */
    public function iot(CochoIotService $iot, $id)
    {
        return response()->json(['data' => $iot->resumo($this->cochosDoUsuario()->findOrFail($id))]);
    }

    /** JSON do histórico (mesmo conteúdo de GET /api/cochos/{id}/iot/historico). */
    public function iotHistorico(Request $request, CochoIotService $iot, $id)
    {
        $dados = $request->validate(['periodo' => ['nullable', Rule::in(CochoIotService::PERIODOS)]]);

        return response()->json(['data' => $iot->historico($this->cochosDoUsuario()->findOrFail($id), $dados['periodo'] ?? 'hoje')]);
    }

    /**
     * ABASTECER UM COCHO: desconta do Estoque de Rações a quantidade
     * colocada nele. A ração precisa ser da MESMA fazenda do lote do cocho
     * (senão um usuário poderia esvaziar o estoque de uma ração de outra
     * fazenda/conta). Duas validações de negócio, além das de formato:
     *   - a quantidade não pode passar do espaço livre do cocho
     *     (capacidade - nível atual);
     *   - a quantidade não pode passar do estoque disponível da ração.
     * Cocho e Ração só são alterados juntos, numa transação — nunca fica
     * "descontado do estoque mas não abastecido" (ou vice-versa) se algo
     * falhar no meio do caminho.
     */
    public function abastecer(Request $request, EstoqueService $estoque, $id)
    {
        $cocho = $this->cochosDoUsuario()->with('lote')->findOrFail($id);

        $dados = $request->validate([
            'racao_id' => ['required', 'integer'],
            'quantidade' => ['required', 'numeric', 'gt:0'],
        ], [
            'racao_id.required' => 'Selecione a ração que será usada.',
            'quantidade.required' => 'Informe a quantidade a abastecer.',
            'quantidade.gt' => 'Informe uma quantidade maior que zero.',
        ]);

        $racao = $this->racoesDoUsuario()
            ->where('fazenda_id', $cocho->lote->fazenda_id)
            ->find($dados['racao_id']);

        if (!$racao) {
            return back()->withErrors(['racao_id' => 'Selecione uma ração válida desta fazenda.'])->withInput();
        }

        if (!CompatibilidadeRacao::compativel($racao, $cocho->lote)) {
            return back()->withErrors(['racao_id' => CompatibilidadeRacao::MENSAGEM])->withInput();
        }

        $quantidade = round((float) $dados['quantidade'], 2);
        $espacoLivre = round(max(0, $cocho->capacidade - $cocho->nivel_atual), 2);

        if ($espacoLivre <= 0) {
            return back()->withErrors(['quantidade' => "O cocho \"{$cocho->nome_cocho}\" já está cheio."])->withInput();
        }

        if ($quantidade > $espacoLivre) {
            return back()->withErrors([
                'quantidade' => 'Não cabem ' . number_format($quantidade, 2, ',', '.') . " kg neste cocho — o espaço livre é de " . number_format($espacoLivre, 2, ',', '.') . ' kg.',
            ])->withInput();
        }

        if ($quantidade > $racao->estoque) {
            return back()->withErrors([
                'quantidade' => 'Não há ração suficiente no estoque para este abastecimento: há só ' . number_format($racao->estoque, 2, ',', '.') . " kg de \"{$racao->nome_racao}\". Reduza a quantidade ou registre uma entrada de ração.",
            ])->withInput();
        }

        // Saída do estoque + nível do cocho + histórico numa só transação,
        // com a ração e o cocho travados (ver EstoqueService) — revalida
        // estoque e espaço livre lá dentro, contra abastecimentos simultâneos.
        $estoque->abastecerCochos($racao, [$cocho->id => $quantidade]);

        return redirect()->route('cocho.listar')->with('success',
            "Abastecido \"{$cocho->nome_cocho}\" com " . number_format($quantidade, 2, ',', '.') . " kg de \"{$racao->nome_racao}\". Estoque restante: " . number_format($racao->fresh()->estoque, 2, ',', '.') . ' kg.'
        );
    }

    /**
     * REGISTRAR CONSUMO: quanto os animais comeram do cocho (cocho sem
     * sensor). Diminui o nível do cocho e grava o histórico; o armazém não
     * muda (a ração já saiu dele no abastecimento). Ver
     * EstoqueService::registrarConsumoCocho().
     */
    public function registrarConsumo(Request $request, EstoqueService $estoque, $id)
    {
        $cocho = $this->cochosDoUsuario()->findOrFail($id);

        $dados = $request->validate([
            'quantidade' => ['required', 'numeric', 'gt:0', 'max:100000'],
            'observacao' => ['nullable', 'string', 'max:255'],
            'data_consumo' => ['nullable', 'date', 'before_or_equal:now'],
        ], [
            'quantidade.required' => 'Informe a quantidade consumida.',
            'quantidade.gt' => 'Informe uma quantidade maior que zero.',
            'data_consumo.before_or_equal' => 'A data do consumo não pode estar no futuro.',
        ]);

        $consumo = $estoque->registrarConsumoCocho($cocho, (float) $dados['quantidade'], [
            'observacao' => $dados['observacao'] ?? null,
            'data_consumo' => isset($dados['data_consumo']) ? Carbon::parse($dados['data_consumo']) : now(),
        ]);

        return redirect()->route('cocho.listar')->with('success',
            'Consumo de ' . number_format($consumo->quantidade, 2, ',', '.') . " kg registrado em \"{$cocho->nome_cocho}\". Restam " . number_format($consumo->nivel_posterior, 2, ',', '.') . ' kg no cocho.'
        );
    }

    /**
     * ABASTECER TODOS OS COCHOS DE UM LOTE DE UMA VEZ: mesma lógica do
     * abastecer() individual, mas distribuindo uma quantidade total entre
     * todos os cochos do lote que ainda têm espaço livre. A distribuição é
     * em fatias iguais entre os cochos "ativos" a cada rodada — um cocho
     * que enche antes da sua fatia completa sai da rodada seguinte, e o que
     * sobrou dele é redistribuído entre os que ainda têm espaço (evita
     * "perder" quantidade numa fatia que não caberia num cocho quase
     * cheio). Se a quantidade pedida for maior que o espaço total
     * disponível, só o que couber é usado — e descontado do estoque só o
     * que foi realmente aplicado, nunca o pedido "no papel".
     */
    public function abastecerLote(Request $request, EstoqueService $estoque, $id)
    {
        $lote = $this->lotesDoUsuario()->with('cochos')->findOrFail($id);

        $dados = $request->validate([
            'racao_id' => ['required', 'integer'],
            'quantidade_total' => ['required', 'numeric', 'gt:0'],
        ], [
            'racao_id.required' => 'Selecione a ração que será usada.',
            'quantidade_total.required' => 'Informe a quantidade a abastecer.',
            'quantidade_total.gt' => 'Informe uma quantidade maior que zero.',
        ]);

        $racao = $this->racoesDoUsuario()
            ->where('fazenda_id', $lote->fazenda_id)
            ->find($dados['racao_id']);

        if (!$racao) {
            return back()->withErrors(['racao_id' => 'Selecione uma ração válida desta fazenda.'])->withInput();
        }

        if (!CompatibilidadeRacao::compativel($racao, $lote)) {
            return back()->withErrors(['racao_id' => CompatibilidadeRacao::MENSAGEM])->withInput();
        }

        $cochos = $lote->cochos;

        if ($cochos->isEmpty()) {
            return back()->withErrors(['quantidade_total' => 'Este lote não tem cochos cadastrados.'])->withInput();
        }

        $espacoTotal = round($cochos->sum(fn (Cocho $c) => max(0, $c->capacidade - $c->nivel_atual)), 2);

        if ($espacoTotal <= 0) {
            return back()->withErrors(['quantidade_total' => 'Todos os cochos deste lote já estão cheios.'])->withInput();
        }

        $quantidadeTotal = round((float) $dados['quantidade_total'], 2);

        if ($quantidadeTotal > $racao->estoque) {
            return back()->withErrors([
                'quantidade_total' => 'Não há ração suficiente no estoque para este abastecimento: há só ' . number_format($racao->estoque, 2, ',', '.') . " kg de \"{$racao->nome_racao}\". Reduza a quantidade ou registre uma entrada de ração.",
            ])->withInput();
        }

        $aplicados = $this->distribuirEntreCochos($cochos, min($quantidadeTotal, $espacoTotal));
        $totalAplicado = round(array_sum($aplicados), 2);

        if ($totalAplicado <= 0) {
            return back()->withErrors(['quantidade_total' => 'Não foi possível distribuir — todos os cochos já estão cheios.'])->withInput();
        }

        $estoque->abastecerCochos($racao, $aplicados);

        $mensagem = 'Distribuídos ' . number_format($totalAplicado, 2, ',', '.') . " kg de \"{$racao->nome_racao}\" entre os cochos de \"{$lote->nome_lote}\". Estoque restante: " . number_format($racao->fresh()->estoque, 2, ',', '.') . ' kg.';

        if ($totalAplicado < $quantidadeTotal - 0.005) {
            $mensagem .= ' Os cochos ficaram cheios antes de usar tudo — ' . number_format($quantidadeTotal - $totalAplicado, 2, ',', '.') . ' kg não foram aplicados.';
        }

        return redirect()->route('cocho.listar')->with('success', $mensagem);
    }

    /**
     * Divide $quantidade em fatias iguais entre os cochos com espaço livre,
     * redistribuindo o que sobrar dos que encherem antes — retorna
     * [cocho_id => quantidade aplicada].
     */
    private function distribuirEntreCochos($cochos, float $quantidade): array
    {
        $aplicados = $cochos->mapWithKeys(fn (Cocho $c) => [$c->id => 0.0])->all();
        $restante = $quantidade;
        $rodadasRestantes = $cochos->count() + 1;

        while ($restante > 0.005 && $rodadasRestantes-- > 0) {
            $comEspaco = $cochos->filter(
                fn (Cocho $c) => ($c->capacidade - $c->nivel_atual - $aplicados[$c->id]) > 0.005
            );

            if ($comEspaco->isEmpty()) {
                break;
            }

            $fatia = $restante / $comEspaco->count();

            foreach ($comEspaco as $c) {
                $espacoDoCocho = $c->capacidade - $c->nivel_atual - $aplicados[$c->id];
                $aplicar = min($fatia, $espacoDoCocho);
                $aplicados[$c->id] += $aplicar;
                $restante -= $aplicar;
            }
        }

        return $aplicados;
    }
}
