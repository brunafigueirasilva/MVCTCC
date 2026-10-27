<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\EscopoDoUsuario;
use App\Http\Controllers\CadastroController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PlanoController;
use App\Models\Cocho;
use App\Models\EstoqueMovimentacao;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use App\Models\LoteOcorrencia;
use App\Models\Ordenha;
use App\Models\Racao;
use App\Models\Sensor;
use App\Services\CochoIotService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Relatórios e resumo do Dashboard para o app — tudo calculado na hora a
 * partir dos dados reais da fazenda (?fazenda_id=), sempre escopado ao
 * usuário do token. Sem fazenda selecionada, todo relatório devolve o
 * mesmo formato com valores zerados e "fazenda": null (estado vazio do app).
 *
 * Período: ?data_inicio=&data_fim= (padrão: últimos 30 dias).
 * Filtros de lote (consumo, lotes, cochos, produção e movimentações do
 * estoque): ?lote_id=&tipo_animal=&status= — mudam os números, não só a tela.
 */
class RelatorioApiController extends Controller
{
    use EscopoDoUsuario;

    /** Filtros de lote validados em filtros() (lote_id, tipo_animal, status). */
    private array $filtroLote = [];

    public function __construct(private CochoIotService $iot)
    {
    }

    /**
     * GET /api/dashboard/resumo e /api/relatorios/resumo — os MESMOS
     * indicadores do Dashboard web (reaproveita CadastroController::dashboard,
     * pra os números do app e da web nunca divergirem), em formato enxuto.
     */
    public function resumo(Request $request, CadastroController $dashboard, PlanoController $plano)
    {
        $fazenda = $this->fazendaSelecionada($request);
        // O Dashboard web lê a seleção de ?fazenda_id= — garante que venha
        // sempre presente (vazio = nenhuma fazenda = indicadores zerados).
        $request->query->set('fazenda_id', $fazenda?->id ?? '');

        $dados = $dashboard->dashboard($request, $plano)->getData();

        return response()->json([
            'message' => $fazenda ? 'Resumo da fazenda carregado com sucesso.' : 'Nenhuma fazenda selecionada.',
            'data' => [
                'fazenda' => $fazenda?->only(['id', 'nome_fazenda']),
                'lotes' => (int) $dados['lotesCount'],
                'animais' => (int) $dados['totalAnimais'],
                'lotes_novos_semana' => (int) $dados['lotesNovosSemana'],
                'cochos' => (int) $dados['cochosCount'],
                'nivel_medio_cochos' => (int) $dados['nivelMedioCochos'],
                // Previsto pelo plano alimentar (histórico diário do vínculo).
                'consumo_hoje_kg' => (float) $dados['consumoHoje'],
                // Real: registrado hoje nos cochos (cocho_consumos).
                'consumo_registrado_hoje_kg' => (float) $dados['consumoRegistradoHoje'],
                'variacao_consumo' => $dados['variacaoConsumo'],
                'custo_hoje' => (float) $dados['custoHoje'],
                'preco_medio_kg' => (float) $dados['precoMedioKg'],
                'eficiencia_alimentar' => (float) $dados['eficienciaAlimentar'],
                'estoque_atual_kg' => (float) $dados['estoqueAtual'],
                // Armazém (estoque_atual_kg) e cochos são quantidades separadas.
                'racao_nos_cochos_kg' => (float) $dados['racaoNosCochos'],
                'estoque_minimo_kg' => (float) $dados['estoqueMinimo'],
                'dias_restantes_estoque' => $dados['diasRestantesEstoque'],
                'lotes_com_ordenha' => (int) $dados['lotesComOrdenha'],
                'producao_leite_hoje_litros' => (float) $dados['producaoLeiteHoje'],
                'grafico_consumo' => ['labels' => array_values($dados['labelsConsumo']), 'valores' => array_values($dados['dadosConsumo'])],
                'grafico_animais' => ['labels' => array_values($dados['labelsAnimais']), 'valores' => array_values($dados['dadosAnimais'])],
                'alertas' => collect($dados['alertas'])->map(fn ($a) => [
                    'titulo' => $a->titulo,
                    'descricao' => $a->descricao,
                    'prioridade' => $a->prioridade,
                ])->values(),
            ],
        ]);
    }

    // GET /api/relatorios/consumo
    public function consumo(Request $request)
    {
        [$fazenda, $inicio, $fim] = $this->filtros($request);

        if (!$fazenda) {
            return $this->vazio(['total_kg' => 0, 'custo_total' => 0, 'por_dia' => [], 'por_lote' => [], 'saidas_estoque_kg' => 0]);
        }

        $loteIds = $this->lotesDaFazenda($fazenda)->pluck('id');
        $snapshots = LoteConsumoSnapshot::with(['lote:id,nome_lote', 'racao:id,nome_racao,valor_kg'])
            ->whereIn('lote_id', $loteIds)
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->get();

        $custo = fn ($s) => (float) $s->consumo_kg * (float) ($s->racao?->valor_kg ?? 0);

        return $this->ok($fazenda, $inicio, $fim, [
            'total_kg' => round($snapshots->sum('consumo_kg'), 2),
            'custo_total' => round($snapshots->sum($custo), 2),
            // Consumo informado nos cochos (Registrar consumo) no período.
            'consumo_registrado_kg' => round((float) \App\Models\CochoConsumo::whereIn('lote_id', $loteIds)
                ->whereBetween('data_consumo', [$inicio, $fim])->sum('quantidade'), 2),
            // total_kg/por_dia/por_lote vêm do histórico diário do plano.
            'fonte_total' => 'plano',
            'saidas_estoque_kg' => round(EstoqueMovimentacao::where('fazenda_id', $fazenda->id)
                ->when($this->filtroLote, fn ($q) => $q->whereIn('lote_id', $loteIds))
                ->where('tipo', EstoqueMovimentacao::TIPO_SAIDA)
                ->whereBetween('data_movimentacao', [$inicio, $fim])
                ->sum('quantidade'), 2),
            'por_dia' => $snapshots->groupBy(fn ($s) => $s->data->toDateString())->sortKeys()
                ->map(fn ($dia, $data) => ['data' => $data, 'consumo_kg' => round($dia->sum('consumo_kg'), 2), 'custo' => round($dia->sum($custo), 2)])
                ->values(),
            'por_lote' => $snapshots->groupBy('lote_id')
                ->map(fn ($itens) => [
                    'lote_id' => $itens->first()->lote_id,
                    'nome_lote' => $itens->first()->lote?->nome_lote,
                    'consumo_kg' => round($itens->sum('consumo_kg'), 2),
                    'custo' => round($itens->sum($custo), 2),
                ])
                ->sortByDesc('consumo_kg')->values(),
        ]);
    }

    // GET /api/relatorios/estoque
    public function estoque(Request $request)
    {
        [$fazenda, $inicio, $fim] = $this->filtros($request);

        if (!$fazenda) {
            return $this->vazio(['racoes' => [], 'estoque_total_kg' => 0, 'valor_total' => 0, 'entradas_kg' => 0, 'saidas_kg' => 0]);
        }

        $racoes = Racao::where('fazenda_id', $fazenda->id)->orderBy('nome_racao')->get();
        $movs = EstoqueMovimentacao::where('fazenda_id', $fazenda->id)
            ->when($this->filtroLote, fn ($q) => $q->whereIn('lote_id', $this->lotesDaFazenda($fazenda)->pluck('id')))
            ->whereBetween('data_movimentacao', [$inicio, $fim])
            ->get();

        return $this->ok($fazenda, $inicio, $fim, [
            'estoque_total_kg' => round($racoes->sum('estoque'), 2),
            'valor_total' => round($racoes->sum(fn ($r) => $r->estoque * ($r->valor_kg ?? 0)), 2),
            'entradas_kg' => round($movs->where('tipo', EstoqueMovimentacao::TIPO_ENTRADA)->sum('quantidade'), 2),
            'saidas_kg' => round($movs->where('tipo', EstoqueMovimentacao::TIPO_SAIDA)->sum('quantidade'), 2),
            'racoes' => $racoes->map(fn (Racao $r) => [
                'racao_id' => $r->id,
                'nome_racao' => $r->nome_racao,
                'estoque' => (float) $r->estoque,
                'estoque_total' => $r->estoque_total,
                'valor_kg' => $r->valor_kg,
                'status_estoque' => $r->statusEstoque(),
                'percentual_estoque' => $r->percentualEstoque(),
                'dias_restantes_estoque' => $r->diasRestantesEstoque(),
                'entradas_kg' => round($movs->where('racao_id', $r->id)->where('tipo', EstoqueMovimentacao::TIPO_ENTRADA)->sum('quantidade'), 2),
                'saidas_kg' => round($movs->where('racao_id', $r->id)->where('tipo', EstoqueMovimentacao::TIPO_SAIDA)->sum('quantidade'), 2),
            ])->values(),
        ]);
    }

    // GET /api/relatorios/lotes
    public function lotes(Request $request)
    {
        [$fazenda, $inicio, $fim] = $this->filtros($request);

        if (!$fazenda) {
            return $this->vazio(['total_lotes' => 0, 'total_animais' => 0, 'lotes' => []]);
        }

        $lotes = $this->lotesDaFazenda($fazenda)->with(['cochos', 'racao:id,nome_racao'])->orderBy('nome_lote')->get();
        $ocorrencias = LoteOcorrencia::whereIn('lote_id', $lotes->pluck('id'))
            ->whereBetween('data_ocorrencia', [$inicio->toDateString(), $fim->toDateString()])
            ->get()->groupBy('lote_id');

        return $this->ok($fazenda, $inicio, $fim, [
            'total_lotes' => $lotes->count(),
            'total_animais' => (int) $lotes->sum('quantidade_animais'),
            'lotes' => $lotes->map(function (Lote $lote) use ($ocorrencias) {
                $doLote = $ocorrencias->get($lote->id, collect());

                return [
                    'lote_id' => $lote->id,
                    'nome_lote' => $lote->nome_lote,
                    'tipo_animal' => $lote->tipo_animal,
                    'sexo' => $lote->sexo,
                    'status' => $lote->status,
                    'quantidade_animais' => (int) $lote->quantidade_animais,
                    'peso_medio' => (float) $lote->peso_medio,
                    'racao' => $lote->racao?->only(['id', 'nome_racao']),
                    'cochos' => $lote->cochos->count(),
                    'obitos' => (int) $doLote->where('tipo', 'morte')->sum('quantidade'),
                    'doencas' => (int) $doLote->where('tipo', 'doenca')->sum('quantidade'),
                    'nascimentos' => (int) $doLote->whereIn('tipo', ['parto', 'eclosao'])->sum('quantidade'),
                ];
            })->values(),
        ]);
    }

    // GET /api/relatorios/cochos
    public function cochos(Request $request)
    {
        [$fazenda, $inicio, $fim] = $this->filtros($request);

        if (!$fazenda) {
            return $this->vazio(['total' => 0, 'por_status' => [], 'cochos' => []]);
        }

        $cochos = Cocho::whereIn('lote_id', $this->lotesDaFazenda($fazenda)->pluck('id'))
            ->with(['lote:id,nome_lote', 'sensores'])->orderBy('lote_id')->orderBy('numero')->get();

        return $this->ok($fazenda, $inicio, $fim, [
            'total' => $cochos->count(),
            'percentual_alerta' => Cocho::PERCENTUAL_ALERTA,
            'por_status' => collect(['OK', 'Baixo', 'Vazio', 'Indefinido'])
                ->mapWithKeys(fn ($s) => [$s => $cochos->filter(fn (Cocho $c) => $c->statusAbastecimento() === $s)->count()]),
            'por_status_sensor' => collect([Sensor::STATUS_ONLINE, Sensor::STATUS_OFFLINE, Sensor::STATUS_ERRO, Sensor::STATUS_SEM_LEITURA])
                ->mapWithKeys(fn ($s) => [$s => $cochos->filter(fn (Cocho $c) => $c->statusSensor() === $s)->count()]),
            'cochos' => $cochos->map(fn (Cocho $c) => [
                'cocho_id' => $c->id,
                'nome_cocho' => $c->nome_cocho,
                'lote' => $c->lote?->only(['id', 'nome_lote']),
                'capacidade' => $c->capacidade,
                'nivel_atual' => $c->nivel_atual,
                'percentual_nivel' => $c->percentualNivel(),
                'status_abastecimento' => $c->statusAbastecimento(),
                'alerta_nivel_baixo' => $c->precisaAbastecer(),
                'status_sensor' => $c->statusSensor(),
                // Consumo medido pelos sensores no período (leituras IoT).
                'consumo_iot_kg' => $this->iot->consumoDesde($c, $inicio, $fim),
            ])->values(),
        ]);
    }

    // GET /api/relatorios/producao
    public function producao(Request $request)
    {
        [$fazenda, $inicio, $fim] = $this->filtros($request);

        if (!$fazenda) {
            return $this->vazio(['total_litros' => 0, 'registros' => 0, 'por_dia' => [], 'por_lote' => []]);
        }

        $ordenhas = Ordenha::with('lote:id,nome_lote,quantidade_animais')
            ->whereIn('lote_id', $this->lotesDaFazenda($fazenda)->pluck('id'))
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->get();

        $dias = max(1, $inicio->diffInDays($fim) + 1);

        return $this->ok($fazenda, $inicio, $fim, [
            'total_litros' => round($ordenhas->sum('quantidade_litros'), 2),
            'registros' => $ordenhas->count(),
            'media_diaria_litros' => round($ordenhas->sum('quantidade_litros') / $dias, 2),
            'por_dia' => $ordenhas->groupBy(fn ($o) => $o->data->toDateString())->sortKeys()
                ->map(fn ($itens, $data) => ['data' => $data, 'litros' => round($itens->sum('quantidade_litros'), 2)])
                ->values(),
            'por_lote' => $ordenhas->groupBy('lote_id')->map(function ($itens) use ($dias) {
                $lote = $itens->first()->lote;
                $litros = $itens->sum('quantidade_litros');

                return [
                    'lote_id' => $lote?->id,
                    'nome_lote' => $lote?->nome_lote,
                    'litros' => round($litros, 2),
                    'media_por_animal_dia' => $lote && $lote->quantidade_animais > 0
                        ? round($litros / $lote->quantidade_animais / $dias, 2)
                        : 0,
                ];
            })->sortByDesc('litros')->values(),
        ]);
    }

    /** @return array{0: ?Fazenda, 1: Carbon, 2: Carbon} */
    private function filtros(Request $request): array
    {
        $dados = $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'lote_id' => 'nullable|integer|min:1',
            'tipo_animal' => ['nullable', \Illuminate\Validation\Rule::in(\App\Support\LoteRegras::tiposAnimais())],
            'status' => ['nullable', \Illuminate\Validation\Rule::in(\App\Support\LoteRegras::STATUS)],
        ], [
            'data_fim.after_or_equal' => 'A data final não pode ser anterior à data inicial.',
            'tipo_animal.in' => 'Tipo de animal inválido.',
            'status.in' => 'Status inválido: use Ativo, Inativo ou Encerrado.',
        ]);

        $this->filtroLote = array_filter([
            'lote_id' => $dados['lote_id'] ?? null,
            'tipo_animal' => $dados['tipo_animal'] ?? null,
            'status' => $dados['status'] ?? null,
        ]);

        $fim = isset($dados['data_fim']) ? Carbon::parse($dados['data_fim'])->endOfDay() : Carbon::today()->endOfDay();
        $inicio = isset($dados['data_inicio']) ? Carbon::parse($dados['data_inicio'])->startOfDay() : $fim->copy()->subDays(29)->startOfDay();

        return [$this->fazendaSelecionada($request), $inicio, $fim];
    }

    /**
     * Lotes da fazenda com os filtros do relatório. Lote de outra fazenda
     * (ou de outra conta) no ?lote_id= simplesmente não entra: resultado vazio.
     */
    private function lotesDaFazenda(Fazenda $fazenda)
    {
        return Lote::where('fazenda_id', $fazenda->id)
            ->when($this->filtroLote['lote_id'] ?? null, fn ($q, $v) => $q->whereKey($v))
            ->when($this->filtroLote['tipo_animal'] ?? null, fn ($q, $v) => $q->where('tipo_animal', $v))
            ->when($this->filtroLote['status'] ?? null, fn ($q, $v) => $q->where('status', $v));
    }

    private function ok(Fazenda $fazenda, Carbon $inicio, Carbon $fim, array $dados)
    {
        return response()->json([
            'message' => 'Relatório carregado com sucesso.',
            'data' => [
                'fazenda' => $fazenda->only(['id', 'nome_fazenda']),
                'periodo' => ['data_inicio' => $inicio->toDateString(), 'data_fim' => $fim->toDateString()],
                'filtros' => (object) $this->filtroLote,
            ] + $dados,
        ]);
    }

    private function vazio(array $dados)
    {
        return response()->json([
            'message' => 'Nenhuma fazenda selecionada.',
            'data' => ['fazenda' => null, 'periodo' => null] + $dados,
        ]);
    }
}
