<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cadastro;
use App\Models\DesempenhoLote;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use App\Models\LoteConsumoSnapshot;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RelatorioController extends Controller
{
    // Preço médio de referência por kg de ração (mesmo valor usado no Dashboard,
    // já que não existe um campo de preço por ração cadastrado no sistema).
    private const PRECO_MEDIO_KG = 3.5;

    // Quantidade de lotes exibidos de início no "Ranking de Lotes por
    // Desempenho"; o restante só é carregado ao clicar em "Ver mais" —
    // mesmo padrão do "Consultar Lotes" do Plano Alimentar.
    private const RANKING_INICIAL = 5;

    /**
     * Fazenda/Lote/Ração pertencem ao usuário logado — mesmo padrão de
     * escopo usado em Cocho/Estoque/PlanoController. Antes esta tela
     * consultava Fazenda::all()/Lote::all()/Racao::all() e Plano::with(...)
     * sem nenhum filtro por usuário, vazando fazendas, lotes, rações e
     * consumo de QUALQUER conta do sistema pros filtros e gráficos de
     * Relatórios.
     */
    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function lotesDoUsuario()
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    private function racoesDoUsuario()
    {
        return Racao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    public function listar(Request $request)
    {
        $cadastroLogado = Cadastro::find(session('usuario_id'));
        $dados = $this->montarDadosRelatorio($request);

        return view('relatorio', array_merge($dados, compact('cadastroLogado')));
    }

    /**
     * Aplica os filtros do modal "Filtros Avançados" em cima do histórico
     * real de consumo (lote_consumo_snapshots — uma linha por lote por dia,
     * gravada automaticamente pelo Plano Alimentar a cada visita à tela,
     * ver PlanoController::capturarSnapshotsDeHoje()), sempre restrito aos
     * lotes do usuário logado. Antes esta consulta lia da tabela "planos",
     * que era alimentada por um "registrar alimentação" manual que não
     * existe mais na tela de Plano Alimentar — a tabela ficou parada em
     * zero registros e os Relatórios sempre vinham vazios, mesmo com
     * consumo real acontecendo. $dataInicio/$dataFim, quando informados,
     * sobrescrevem o período do request — usado pra buscar o período
     * "anterior" de comparação sem depender do que está na URL.
     */
    private function filtrarConsumo(Request $request, $dataInicio = null, $dataFim = null)
    {
        $dataInicio = $dataInicio ?? $request->input('data_inicio');
        $dataFim = $dataFim ?? $request->input('data_fim');

        return LoteConsumoSnapshot::with(['lote.fazenda', 'racao'])
            ->whereIn('lote_id', $this->lotesDoUsuario()->pluck('id'))
            ->when($request->filled('fazenda'), function ($query) use ($request) {
                $query->whereHas('lote', fn ($q) => $q->where('fazenda_id', $request->fazenda));
            })
            ->when($request->filled('lote') && $request->lote !== 'todos', function ($query) use ($request) {
                $query->where('lote_id', $request->lote);
            })
            ->when($request->filled('categoria_animal'), function ($query) use ($request) {
                $query->whereHas('lote', fn ($q) => $q->where('tipo_animal', $request->categoria_animal));
            })
            ->when($request->filled('tipo_racao') && $request->tipo_racao !== 'todos', function ($query) use ($request) {
                $query->where('racao_id', $request->tipo_racao);
            })
            ->when($dataInicio, fn ($query) => $query->whereDate('data', '>=', $dataInicio))
            ->when($dataFim, fn ($query) => $query->whereDate('data', '<=', $dataFim))
            ->get();
    }

    /**
     * Período "atual" mostrado nos gráficos/cards e período "anterior" de
     * mesma duração, usado só pra calcular a variação percentual dos cards
     * do topo. Sem filtro de período aplicado, o padrão é "últimos 7 dias"
     * (mesma janela que o gráfico de linha usa por padrão) comparado com os
     * 7 dias imediatamente anteriores.
     */
    private function periodoAtualEAnterior(Request $request): array
    {
        if ($request->filled('data_inicio') || $request->filled('data_fim')) {
            $inicioAtual = $request->filled('data_inicio')
                ? Carbon::parse($request->data_inicio)->startOfDay()
                : Carbon::parse($request->data_fim)->startOfDay();
            $fimAtual = $request->filled('data_fim')
                ? Carbon::parse($request->data_fim)->endOfDay()
                : now()->endOfDay();
        } else {
            $fimAtual = now()->endOfDay();
            $inicioAtual = now()->subDays(6)->startOfDay();
        }

        $duracaoDias = $inicioAtual->diffInDays($fimAtual) + 1;
        $fimAnterior = $inicioAtual->copy()->subDay()->endOfDay();
        $inicioAnterior = $inicioAtual->copy()->subDays($duracaoDias)->startOfDay();

        return [$inicioAtual, $fimAtual, $inicioAnterior, $fimAnterior];
    }

    /**
     * Mesma semântica do CadastroController::variacaoPercentual() (usado no
     * Dashboard) — null quando não há base de comparação (evita fabricar um
     * "+infinito%"), 0.0 quando os dois períodos estão zerados.
     */
    private function variacaoPercentual(float $atual, float $anterior): ?float
    {
        if ($anterior <= 0) {
            return $atual > 0 ? null : 0.0;
        }

        return round((($atual - $anterior) / $anterior) * 100, 1);
    }

    /**
     * Métricas por lote (peso, consumo do período, e os 6 indicadores 0-100
     * usados tanto no radar "Eficiência por Lote" quanto no "Ranking de
     * Lotes por Desempenho") — centralizado aqui pra as duas seções nunca
     * divergirem no cálculo de eficiência de um mesmo lote.
     */
    private function metricasPorLote(Collection $lotesEscopados, Collection $consumoPeriodo): Collection
    {
        if ($lotesEscopados->isEmpty()) {
            return collect();
        }

        $consumoPorLoteId = $consumoPeriodo->groupBy('lote_id')
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'));

        $maxPeso = (float) $lotesEscopados->max('peso_medio') ?: 1;
        $maxConsumo = (float) $consumoPorLoteId->max() ?: 1;

        // Conversão Alimentar REAL (App\Models\DesempenhoLote — ver
        // PlanoController::registrarPesagem): quando o lote já tem pesagem
        // suficiente pra um FCR de verdade, ele substitui o heurístico
        // "peso médio / consumo por animal" abaixo. Score relativo ao MELHOR
        // (menor) FCR do conjunto em escopo, mesmo espírito de $ganhoPeso/
        // $consumoScore acima (sempre relativo ao lote de referência, nunca
        // um valor absoluto inventado).
        $fcrRealPorLoteId = DesempenhoLote::whereIn('lote_id', $lotesEscopados->pluck('id'))
            ->where('eficiencia_alimentar', '>', 0)
            ->orderByDesc('data_registro')
            ->get()
            ->groupBy('lote_id')
            ->map(fn ($grupo) => (float) $grupo->first()->eficiencia_alimentar);
        $melhorFcrReal = $fcrRealPorLoteId->isNotEmpty() ? $fcrRealPorLoteId->min() : null;

        return $lotesEscopados->map(function ($lote) use ($consumoPorLoteId, $maxPeso, $maxConsumo, $fcrRealPorLoteId, $melhorFcrReal) {
            $consumoLote = (float) ($consumoPorLoteId[$lote->id] ?? 0);
            $consumoPorAnimal = $lote->quantidade_animais > 0 ? $consumoLote / $lote->quantidade_animais : 0;
            $custoLote = round($consumoLote * self::PRECO_MEDIO_KG, 2);

            $ganhoPeso = (int) round(($lote->peso_medio / $maxPeso) * 100);
            $consumoScore = (int) round(($consumoLote / $maxConsumo) * 100);

            $fcrRealLote = $fcrRealPorLoteId->get($lote->id);
            $conversao = ($fcrRealLote !== null && $melhorFcrReal !== null)
                ? (int) round(min(100, ($melhorFcrReal / $fcrRealLote) * 100))
                : ($consumoPorAnimal > 0 ? (int) round(min(100, ($lote->peso_medio / $consumoPorAnimal) * 20)) : 50);
            $saude = $lote->cochos->isNotEmpty()
                ? (int) round($lote->cochos->avg(fn ($c) => $c->capacidade > 0 ? min(100, ($c->nivel_atual / $c->capacidade) * 100) : 0))
                : 50;
            $custoBeneficio = $custoLote > 0
                ? (int) round(min(100, ($lote->peso_medio / $custoLote) * 500))
                : 50;
            $sustentabilidade = ($lote->cochos->isNotEmpty() && $lote->quantidade_animais > 0)
                ? (int) round(min(100, ($lote->cochos->sum('capacidade') / $lote->quantidade_animais) * 10))
                : 50;

            $scores = [$ganhoPeso, $consumoScore, $conversao, $saude, $custoBeneficio, $sustentabilidade];

            return (object) [
                'id' => $lote->id,
                'nome_lote' => $lote->nome_lote,
                'tipo_animal' => $lote->tipo_animal,
                'peso_medio' => (float) $lote->peso_medio,
                'consumo_total' => $consumoLote,
                'scores' => $scores,
                'eficiencia_media' => (int) round(array_sum($scores) / count($scores)),
            ];
        })->values();
    }

    /**
     * Datasets do radar "Eficiência por Lote": só os 4 lotes de maior peso
     * médio (mais que isso deixa o radar ilegível), a partir das métricas
     * já calculadas em metricasPorLote().
     */
    private function montarRadarLotes(Collection $metricas): array
    {
        $cores = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'];

        return $metricas->sortByDesc('peso_medio')->take(4)->values()
            ->map(fn ($m, $indice) => [
                'label' => $m->nome_lote,
                'data' => $m->scores,
                'borderColor' => $cores[$indice % count($cores)],
                'backgroundColor' => $this->corComTransparencia($cores[$indice % count($cores)], 0.15),
            ])->values()->all();
    }

    /**
     * "Ranking de Lotes por Desempenho": TODOS os lotes em escopo,
     * ordenados por eficiência (mesmo cálculo do radar), com consumo/custo
     * médio diário e variação em relação ao período anterior de mesma
     * duração.
     */
    private function montarRankingLotes(Collection $metricas, Collection $consumoAtual, Collection $consumoAnterior, int $diasComRegistroAtual): Collection
    {
        // "Consumo Médio"/"Eficiência" vêm de $metricas (histórico
        // completo — ver montarDadosRelatorio); a "Variação" compara só os
        // últimos 7 dias contra os 7 anteriores, senão comparar um total
        // histórico contra uma janela de 7 dias sempre dava uma alta enorme
        // e sem sentido.
        $consumoAtualPorLoteId = $consumoAtual->groupBy('lote_id')
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'));
        $consumoAnteriorPorLoteId = $consumoAnterior->groupBy('lote_id')
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'));

        return $metricas->sortByDesc('eficiencia_media')->values()
            ->map(function ($m, $indice) use ($consumoAtualPorLoteId, $consumoAnteriorPorLoteId, $diasComRegistroAtual) {
                $consumoMedioDia = round($m->consumo_total / $diasComRegistroAtual, 1);
                $custoMedioDia = round($consumoMedioDia * self::PRECO_MEDIO_KG, 2);
                $consumoAtualLote = (float) ($consumoAtualPorLoteId[$m->id] ?? 0);
                $consumoAnteriorLote = (float) ($consumoAnteriorPorLoteId[$m->id] ?? 0);

                return (object) [
                    'posicao' => $indice + 1,
                    'nome_lote' => $m->nome_lote,
                    'tipo_animal' => $m->tipo_animal,
                    'consumo_medio_dia' => $consumoMedioDia,
                    'custo_medio_dia' => $custoMedioDia,
                    'eficiencia' => $m->eficiencia_media,
                    'variacao' => $this->variacaoPercentual($consumoAtualLote, $consumoAnteriorLote),
                ];
            });
    }

    /**
     * "Resumo por Categoria": consumo/custo/quantidade de lotes agrupados
     * por tipo_animal — só o que o banco realmente distingue (a tabela não
     * guarda "Corte" vs "Leite" separado do tipo do animal).
     */
    private function montarResumoPorCategoria(Collection $lotesEscopados, Collection $consumoPeriodo): Collection
    {
        $iconesPorTipo = [
            'Bovino' => 'fa-cow', 'Suíno' => 'fa-piggy-bank', 'Suino' => 'fa-piggy-bank',
            'Equino' => 'fa-horse', 'Avicultura' => 'fa-dove', 'Ovino' => 'fa-paw', 'Caprino' => 'fa-paw',
        ];
        $labelPlural = [
            'Bovino' => 'Bovinos', 'Suíno' => 'Suínos', 'Suino' => 'Suínos',
            'Equino' => 'Equinos', 'Avicultura' => 'Aves', 'Ovino' => 'Ovinos', 'Caprino' => 'Caprinos',
        ];

        $consumoPorLoteId = $consumoPeriodo->groupBy('lote_id')
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'));

        return $lotesEscopados->groupBy('tipo_animal')
            ->map(function ($lotesDoTipo, $tipo) use ($consumoPorLoteId, $iconesPorTipo, $labelPlural) {
                $consumoTotal = $lotesDoTipo->sum(fn ($lote) => $consumoPorLoteId[$lote->id] ?? 0);

                return (object) [
                    'tipo' => $tipo,
                    'label' => $labelPlural[$tipo] ?? ($tipo . 's'),
                    'icone' => $iconesPorTipo[$tipo] ?? 'fa-paw',
                    'quantidade_lotes' => $lotesDoTipo->count(),
                    'consumo_total' => round($consumoTotal, 1),
                    'custo_total' => round($consumoTotal * self::PRECO_MEDIO_KG, 2),
                ];
            })
            ->sortByDesc('consumo_total')
            ->values();
    }

    /**
     * Monta todos os dados dos 4 gráficos + cards + resumo + ranking a
     * partir do mesmo recorte filtrado de planos. Usado pela tela
     * (Chart.js) e pelo PDF exportado, para os dois sempre mostrarem
     * exatamente os mesmos números.
     */
    private function montarDadosRelatorio(Request $request): array
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $lotes = $this->lotesDoUsuario()->get();
        $racoes = $this->racoesDoUsuario()->get();

        // Categorias de animal realmente cadastradas (antes o filtro tinha
        // uma lista fixa no HTML — incluindo "Suíno" com acento, que nunca
        // batia com o valor gravado no banco, "Suino" sem acento — e
        // "Caprino", que nenhum lote usa).
        $categoriasAnimais = $lotes->pluck('tipo_animal')->filter()->unique()->sort()->values();

        [$inicioAtual, $fimAtual, $inicioAnterior, $fimAnterior] = $this->periodoAtualEAnterior($request);

        // $consumo: SEM limite de data por padrão (só fica restrito se o
        // usuário aplicar um período nos Filtros Avançados) — alimenta os 4
        // gráficos, o radar, a distribuição por ração, o ranking e o resumo
        // por categoria, que precisam do histórico completo pra fazer
        // sentido. $consumoAtual/$consumoAnterior (últimos 7 dias vs os 7
        // anteriores) são usados só pelos 5 cards do topo, pra "vs período
        // anterior" ter uma janela curta e comparável — misturar as duas
        // coisas deixava os gráficos artificialmente vazios sempre que o
        // grosso do histórico ficava fora dos últimos 7 dias.
        $consumo = $this->filtrarConsumo($request);
        $consumoAtual = $this->filtrarConsumo($request, $inicioAtual->toDateString(), $fimAtual->toDateString());
        $consumoAnterior = $this->filtrarConsumo($request, $inicioAnterior->toDateString(), $fimAnterior->toDateString());

        // GRÁFICO 1: Consumo ao longo do tempo (soma diária dos snapshots de consumo)
        $consumoPorDia = $consumo
            ->groupBy(fn ($s) => $s->data->format('Y-m-d'))
            ->sortKeys();

        $labelsDatas = $consumoPorDia->keys()
            ->map(fn ($data) => Carbon::parse($data)->format('d/m'))
            ->values();
        $valoresConsumo = $consumoPorDia
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'))
            ->values();

        // GRÁFICO 2: Consumo (e custo estimado) por lote
        $porLote = $consumo
            ->groupBy(fn ($s) => $s->lote->nome_lote ?? 'Sem lote')
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'))
            ->sortDesc()
            ->take(8);

        $labelsLotes = $porLote->keys()->values();
        $kgPorLote = $porLote->values();
        $custoPorLote = $porLote
            ->map(fn ($kg) => round($kg * self::PRECO_MEDIO_KG, 2))
            ->values();
        $loteMaiorConsumo = $labelsLotes->first();

        // GRÁFICO 3: Distribuição de gastos pelos tipos de ração REALMENTE
        // cadastrados no sistema (antes usava ingredientes fixos sem relação
        // com o projeto, tipo "Milho/Soja/Minerais").
        $porRacao = $consumo
            ->groupBy(fn ($s) => $s->racao->nome_racao ?? 'Não informado')
            ->map(fn ($grupo) => (float) $grupo->sum('consumo_kg'))
            ->sortDesc();

        $totalKgRacao = $porRacao->sum();
        $labelsGastos = $porRacao->keys()->values();
        $valoresGastos = $totalKgRacao > 0
            ? $porRacao->map(fn ($kg) => round(($kg / $totalKgRacao) * 100, 1))->values()
            : collect();

        // Lotes em escopo (fazenda/lote/categoria) usados pro radar, ranking
        // e resumo por categoria — igual ao filtro já aplicado nos planos,
        // só que aqui inclui lotes SEM nenhum consumo no período também
        // (pra "Lotes Ativos" e "Resumo por Categoria" não esconderem lote
        // nenhum).
        $lotesEscopados = $lotes
            ->when($request->filled('fazenda'), fn ($c) => $c->where('fazenda_id', (int) $request->fazenda))
            ->when($request->filled('lote') && $request->lote !== 'todos', fn ($c) => $c->where('id', (int) $request->lote))
            ->when($request->filled('categoria_animal'), fn ($c) => $c->where('tipo_animal', $request->categoria_animal))
            ->values();
        $lotesEscopados->load('cochos');

        // GRÁFICO 4: Eficiência por lote, calculada a partir de dados reais
        // (peso médio, consumo do plano alimentar e nível de ração nos
        // cochos) — histórico completo, mesma base dos outros gráficos.
        $metricas = $this->metricasPorLote($lotesEscopados, $consumo);
        $dadosRadarLotes = $this->montarRadarLotes($metricas);

        // RANKING DE LOTES POR DESEMPENHO — "Consumo Médio"/"Eficiência" do
        // histórico completo; "Variação" compara só os últimos 7 dias
        // contra os 7 anteriores (ver montarRankingLotes).
        $diasComRegistroAtual = max($consumoPorDia->count(), 1);
        $rankingLotesCompleto = $this->montarRankingLotes($metricas, $consumoAtual, $consumoAnterior, $diasComRegistroAtual);
        $rankingLotesTotal = $rankingLotesCompleto->count();
        $rankingLotesExibidos = $rankingLotesCompleto->take(self::RANKING_INICIAL)->values();

        // RESUMO POR CATEGORIA — histórico completo, mesma base dos gráficos.
        $resumoPorCategoria = $this->montarResumoPorCategoria($lotesEscopados, $consumo);

        // EVOLUÇÃO HISTÓRICA (6 meses) — sempre a mesma janela, independente
        // do filtro de período ativo (ver montarEvolucaoHistorica).
        $evolucaoHistorica = $this->montarEvolucaoHistorica($lotesEscopados);

        // CARDS DO TOPO: aqui sim os últimos 7 dias vs os 7 anteriores —
        // são os únicos elementos da tela pensados como "janela curta e
        // comparável", diferente dos gráficos/ranking/resumo acima.
        $totalConsumoKg = (float) $consumoAtual->sum('consumo_kg');
        $totalConsumoKgAnterior = (float) $consumoAnterior->sum('consumo_kg');
        $custoTotalEstimado = round($totalConsumoKg * self::PRECO_MEDIO_KG, 2);
        $custoTotalAnterior = round($totalConsumoKgAnterior * self::PRECO_MEDIO_KG, 2);

        // $consumoMedioDiario (usado só no PDF) continua sobre o histórico
        // completo — o relatório exportado é o retrato geral, não a janela
        // curta dos cards do topo.
        $consumoMedioDiario = round(((float) $consumo->sum('consumo_kg')) / $diasComRegistroAtual, 1);

        $metricasAtual = $this->metricasPorLote($lotesEscopados, $consumoAtual);
        $metricasAnterior = $this->metricasPorLote($lotesEscopados, $consumoAnterior);
        $eficienciaMedia = $metricasAtual->isNotEmpty() ? (int) round($metricasAtual->avg('eficiencia_media')) : 0;
        $eficienciaMediaAnterior = $metricasAnterior->isNotEmpty() ? (int) round($metricasAnterior->avg('eficiencia_media')) : 0;

        $melhorLote = $rankingLotesCompleto->first()->nome_lote ?? ($labelsLotes->first() ?? 'N/D');

        // Média Diária e Consumo por kg Vivo — mesmas fórmulas usadas nos
        // cards do topo do Plano Alimentar (só que aqui sobre a janela
        // "atual" de período, em vez do mês corrente): total consumido no
        // período ÷ dias do período, e total consumido ÷ peso vivo total
        // (peso médio × quantidade de animais) dos lotes em escopo.
        $diasPeriodoAtual = $inicioAtual->diffInDays($fimAtual) + 1;
        $mediaDiariaAtual = round($totalConsumoKg / $diasPeriodoAtual, 1);
        $mediaDiariaAnterior = round($totalConsumoKgAnterior / $diasPeriodoAtual, 1);

        $pesoVivoTotal = $lotesEscopados->sum(fn ($l) => (float) ($l->peso_medio ?? 0) * (int) ($l->quantidade_animais ?? 0));
        $consumoPorKgVivoAtual = $pesoVivoTotal > 0 ? round($totalConsumoKg / $pesoVivoTotal, 3) : null;
        $consumoPorKgVivoAnterior = $pesoVivoTotal > 0 ? round($totalConsumoKgAnterior / $pesoVivoTotal, 3) : null;

        $statCards = [
            'consumo' => [
                'valor' => $totalConsumoKg,
                'variacao' => $this->variacaoPercentual($totalConsumoKg, $totalConsumoKgAnterior),
            ],
            'custo' => [
                'valor' => $custoTotalEstimado,
                'variacao' => $this->variacaoPercentual($custoTotalEstimado, $custoTotalAnterior),
            ],
            'mediaDiaria' => [
                'valor' => $mediaDiariaAtual,
                'variacao' => $this->variacaoPercentual($mediaDiariaAtual, $mediaDiariaAnterior),
            ],
            'porKgVivo' => [
                'valor' => $consumoPorKgVivoAtual,
                // variacaoPercentual() exige float não-nulo — sem peso vivo
                // cadastrado (pesoVivoTotal = 0) os dois valores acima ficam
                // null, então a variação também fica null direto, sem chamar.
                'variacao' => ($consumoPorKgVivoAtual !== null && $consumoPorKgVivoAnterior !== null)
                    ? $this->variacaoPercentual($consumoPorKgVivoAtual, $consumoPorKgVivoAnterior)
                    : null,
            ],
            'eficiencia' => [
                'valor' => $eficienciaMedia,
                'variacao' => $this->variacaoPercentual($eficienciaMedia, $eficienciaMediaAnterior),
            ],
        ];

        $periodoLabel = $inicioAtual->isSameDay($fimAtual)
            ? $inicioAtual->format('d/m/Y')
            : $inicioAtual->format('d/m/Y') . ' a ' . $fimAtual->format('d/m/Y');

        $filtrosAplicados = $this->descreverFiltros($request, $fazendas, $lotes, $racoes);

        return compact(
            'fazendas',
            'lotes',
            'racoes',
            'categoriasAnimais',
            'labelsDatas',
            'valoresConsumo',
            'labelsLotes',
            'kgPorLote',
            'custoPorLote',
            'loteMaiorConsumo',
            'labelsGastos',
            'valoresGastos',
            'dadosRadarLotes',
            'totalConsumoKg',
            'custoTotalEstimado',
            'consumoMedioDiario',
            'melhorLote',
            'eficienciaMedia',
            'filtrosAplicados',
            'statCards',
            'resumoPorCategoria',
            'evolucaoHistorica',
            'rankingLotesExibidos',
            'rankingLotesTotal',
            'rankingLotesCompleto',
            'periodoLabel'
        );
    }

    /**
     * Traduz os parâmetros de filtro da URL em frases legíveis, exibidas no
     * topo do PDF e nos "chips" de filtro ativo na tela, para deixar claro
     * qual recorte de dados está sendo mostrado.
     */
    private function descreverFiltros(Request $request, $fazendas, $lotes, $racoes): array
    {
        $itens = [];

        if ($request->filled('fazenda')) {
            $nome = optional($fazendas->firstWhere('id', (int) $request->fazenda))->nome_fazenda;
            $itens[] = ['chave' => 'fazenda', 'label' => 'Fazenda: ' . ($nome ?? '—')];
        }

        if ($request->filled('lote') && $request->lote !== 'todos') {
            $nome = optional($lotes->firstWhere('id', (int) $request->lote))->nome_lote;
            $itens[] = ['chave' => 'lote', 'label' => 'Lote: ' . ($nome ?? '—')];
        }

        if ($request->filled('categoria_animal')) {
            $itens[] = ['chave' => 'categoria_animal', 'label' => 'Animal: ' . $request->categoria_animal];
        }

        if ($request->filled('tipo_racao') && $request->tipo_racao !== 'todos') {
            $nome = optional($racoes->firstWhere('id', (int) $request->tipo_racao))->nome_racao;
            $itens[] = ['chave' => 'tipo_racao', 'label' => 'Ração: ' . ($nome ?? '—')];
        }

        if ($request->filled('data_inicio') || $request->filled('data_fim')) {
            $inicio = $request->filled('data_inicio') ? Carbon::parse($request->data_inicio)->format('d/m/Y') : '—';
            $fim = $request->filled('data_fim') ? Carbon::parse($request->data_fim)->format('d/m/Y') : '—';
            $itens[] = ['chave' => 'periodo', 'label' => "Período: {$inicio} a {$fim}"];
        }

        return $itens;
    }

    /**
     * "Evolução Histórica (6 meses)": consumo total mensal a partir de
     * lote_consumo_snapshots, sempre os últimos 6 meses — independente do
     * filtro de período (data_inicio/data_fim) ativo na tela, pra dar uma
     * visão de longo prazo mesmo quando o usuário filtrou um recorte curto.
     * Respeita os filtros de Fazenda/Lote/Categoria (via $lotesEscopados).
     */
    private function montarEvolucaoHistorica(Collection $lotesEscopados): array
    {
        $inicio = now()->subMonths(5)->startOfMonth();

        $snapshots = LoteConsumoSnapshot::whereIn('lote_id', $lotesEscopados->pluck('id'))
            ->where('data', '>=', $inicio->toDateString())
            ->get();

        $mesesAbreviados = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $labels = [];
        $valores = [];

        for ($i = 5; $i >= 0; $i--) {
            $mesReferencia = now()->subMonths($i);
            $labels[] = $mesesAbreviados[$mesReferencia->month - 1] . '/' . $mesReferencia->format('y');
            $valores[] = round((float) $snapshots->filter(fn ($s) => $s->data->isSameMonth($mesReferencia))->sum('consumo_kg'), 1);
        }

        return [
            'labels' => $labels,
            'valores' => $valores,
            'temDados' => array_sum($valores) > 0,
        ];
    }

    private function corComTransparencia(string $hex, float $alpha): string
    {
        $hex = ltrim($hex, '#');
        [$r, $g, $b] = array_map(fn ($c) => hexdec(str_pad($c, 2, $c)), str_split($hex, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    // CONSULTAR MAIS RANKING: retorna em JSON (HTML já renderizado) as
    // linhas do "Ranking de Lotes por Desempenho" que ficaram de fora da
    // carga inicial — respeitando os mesmos filtros da página.
    public function rankingJson(Request $request)
    {
        $dados = $this->montarDadosRelatorio($request);

        // montarDadosRelatorio() já devolve só a página inicial do ranking
        // (rankingLotesExibidos); recalcula o conjunto completo aqui só pra
        // fatiar o restante — mesmo custo de query que a tela já paga.
        // $consumo sem limite de data (mesma base do "Consumo Médio"/
        // "Eficiência" da carga inicial); $consumoAtual/$consumoAnterior (7
        // dias) só pra a coluna "Variação" — ver montarRankingLotes().
        [$inicioAtual, $fimAtual, $inicioAnterior, $fimAnterior] = $this->periodoAtualEAnterior($request);
        $consumo = $this->filtrarConsumo($request);
        $consumoAtual = $this->filtrarConsumo($request, $inicioAtual->toDateString(), $fimAtual->toDateString());
        $consumoAnterior = $this->filtrarConsumo($request, $inicioAnterior->toDateString(), $fimAnterior->toDateString());

        $lotes = $this->lotesDoUsuario()->get()
            ->when($request->filled('fazenda'), fn ($c) => $c->where('fazenda_id', (int) $request->fazenda))
            ->when($request->filled('lote') && $request->lote !== 'todos', fn ($c) => $c->where('id', (int) $request->lote))
            ->when($request->filled('categoria_animal'), fn ($c) => $c->where('tipo_animal', $request->categoria_animal))
            ->values();
        $lotes->load('cochos');

        $metricas = $this->metricasPorLote($lotes, $consumo);
        $diasComRegistro = max($consumo->groupBy(fn ($s) => $s->data->format('Y-m-d'))->count(), 1);
        $rankingCompleto = $this->montarRankingLotes($metricas, $consumoAtual, $consumoAnterior, $diasComRegistro);
        $restante = $rankingCompleto->slice(self::RANKING_INICIAL)->values();

        return response()->json([
            'success' => true,
            'html' => view('partials.ranking-lotes-linhas', ['ranking' => $restante])->render(),
        ]);
    }

// FORMULÁRIO CADASTRAR
// O cadastro/edição de relatórios acontece na própria tela de listagem.
public function cadastrar()
{
    return redirect()->route('relatorio.listar');
}

// FORMULÁRIO ATUALIZAR
public function atualizar($id)
{
    Relatorio::findOrFail($id);

    return redirect()->route('relatorio.listar');
}

// SALVAR
public function salvar(Request $request)
{
    $request->validate([
        'lote_id' => 'required|exists:lotes,id',
        'data_relatorio' => 'required|date',
        'quantidade_animais' => 'required|integer',
        'peso_medio' => 'required|numeric',
        'consumo_total' => 'required|numeric',
        'observacoes' => 'nullable|string',
    ]);

    Relatorio::create($request->only([
        'lote_id',
        'data_relatorio',
        'quantidade_animais',
        'peso_medio',
        'consumo_total',
        'observacoes',
    ]));

    return redirect()->route('relatorio.listar')
        ->with('success', 'Relatório cadastrado com sucesso!');
}

// UPDATE
public function update(Request $request, $id)
{
    $request->validate([
        'lote_id' => 'required|exists:lotes,id',
        'data_relatorio' => 'required|date',
        'quantidade_animais' => 'required|integer',
        'peso_medio' => 'required|numeric',
        'consumo_total' => 'required|numeric',
        'observacoes' => 'nullable|string',
    ]);

    $relatorio = Relatorio::findOrFail($id);

    $relatorio->update($request->only([
        'lote_id',
        'data_relatorio',
        'quantidade_animais',
        'peso_medio',
        'consumo_total',
        'observacoes',
    ]));

    return redirect()->route('relatorio.listar')
        ->with('success', 'Relatório atualizado com sucesso!');
}

// DELETE
public function delete($id)
{
    $relatorio = Relatorio::findOrFail($id);
    $relatorio->delete();

    return redirect()->route('relatorio.listar')
        ->with('success', 'Relatório excluído com sucesso!');
}

    /**
     * O dompdf não consegue carregar a logo por asset()/URL de forma
     * confiável num PDF baixado (precisaria de dompdf.enable_remote e do
     * app acessível por HTTP no momento da geração), então ela é embutida
     * como data URI base64 direto no HTML do PDF.
     */
    private function logoBase64(): ?string
    {
        $caminho = public_path('agrifarm-removebg-preview.png');

        if (!file_exists($caminho)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($caminho));
    }

    public function exportarPDF(Request $request)
    {
        // Usa exatamente o mesmo recorte de dados (mesmos filtros, mesmos
        // cálculos) que a tela de Relatórios exibe nos gráficos — antes o
        // PDF tinha sua própria query em cima de colunas que não existem na
        // tabela "cochos" (fazenda_id, tipo_animal, nome_lote,
        // quantidade_consumida, ganho_peso, eficiencia...), então o
        // download vinha sempre vazio ou quebrava com erro de SQL quando um
        // filtro de fazenda era aplicado.
        $dados = $this->montarDadosRelatorio($request);
        $dados['cadastroLogado'] = Cadastro::find(session('usuario_id'));
        $dados['dataEmissao'] = now();
        $dados['logoBase64'] = $this->logoBase64();

        // Achata o radar (label + 6 métricas 0-100) em linhas de tabela,
        // formato mais adequado para o PDF do que o array usado pelo Chart.js.
        $dados['eficienciaLotes'] = collect($dados['dadosRadarLotes'])->map(fn ($lote) => [
            'lote' => $lote['label'],
            'ganho_peso' => $lote['data'][0] ?? 0,
            'consumo' => $lote['data'][1] ?? 0,
            'conversao' => $lote['data'][2] ?? 0,
            'saude' => $lote['data'][3] ?? 0,
            'custo_beneficio' => $lote['data'][4] ?? 0,
            'sustentabilidade' => $lote['data'][5] ?? 0,
        ])->values();

        // Recombina os pares label/valor (formato do Chart.js) em linhas de
        // tabela simples, mais fáceis de percorrer no template do PDF.
        $dados['consumoPorDia'] = collect($dados['labelsDatas'])
            ->map(fn ($data, $i) => ['data' => $data, 'consumo' => $dados['valoresConsumo'][$i] ?? 0])
            ->values();

        $dados['consumoLotes'] = collect($dados['labelsLotes'])
            ->map(fn ($nome, $i) => [
                'nome' => $nome,
                'consumo' => $dados['kgPorLote'][$i] ?? 0,
                'custo' => $dados['custoPorLote'][$i] ?? 0,
            ])
            ->values();

        $dados['gastos'] = collect($dados['labelsGastos'])
            ->map(fn ($nome, $i) => ['nome' => $nome, 'percentual' => $dados['valoresGastos'][$i] ?? 0])
            ->values();

        $pdf = Pdf::loadView('pdf.relatorioPDF', $dados)->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-agrifarm-' . now()->format('Y-m-d_His') . '.pdf');
    }
}
