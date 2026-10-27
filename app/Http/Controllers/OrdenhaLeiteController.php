<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolveFazendaSelecionada;
use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Ordenha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Tela "Ordenha e Leite": produção de leite controlada exclusivamente por
 * LOTE (sem nenhum cadastro ou vínculo com animal individual). Todo indicador
 * é calculado sob demanda a partir dos registros de Ordenha do(s) lote(s) da
 * fazenda selecionada, agrupados por período.
 */
class OrdenhaLeiteController extends Controller
{
    use ResolveFazendaSelecionada;

    private const HISTORICO_JANELA_DIAS_PADRAO = 60;
    private const HISTORICO_INICIAL = 8;

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php) — cobre
    // também historicoJson(), que antes não tinha checagem nenhuma.
    public function listar(Request $request)
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))
            ->orderBy('nome_fazenda')
            ->get();

        // Mesma fazenda selecionada (persistida em sessão) do Dashboard —
        // se já foi escolhida por lá, essa tela já abre nela, sem pedir pra
        // escolher de novo.
        $fazendaSelecionadaId = $this->resolverFazendaSelecionadaComSessao($request, $fazendas);

        $cadastroLogado = Cadastro::find(session('usuario_id'));
        [$dataInicio, $dataFim] = $this->periodoDaRequisicao($request);

        if (!$fazendaSelecionadaId) {
            return view('ordenhaleite', $this->dadosVazios($fazendas, $dataInicio, $dataFim) + ['cadastroLogado' => $cadastroLogado]);
        }

        return view('ordenhaleite', $this->dadosDaFazenda($fazendas, $fazendaSelecionadaId, $dataInicio, $dataFim) + ['cadastroLogado' => $cadastroLogado]);
    }

    /**
     * CONSULTAR MAIS ORDENHAS: restante do histórico (fora dos
     * HISTORICO_INICIAL mais recentes já exibidos) via AJAX — mesmo padrão
     * de "ver mais" já usado em Plano Alimentar/Relatórios.
     */
    public function historicoJson(Request $request)
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))
            ->orderBy('nome_fazenda')
            ->get();
        $fazendaSelecionadaId = $this->resolverFazendaSelecionadaComSessao($request, $fazendas);

        if (!$fazendaSelecionadaId) {
            return response()->json(['success' => true, 'html' => '']);
        }

        [$dataInicio, $dataFim] = $this->periodoDaRequisicao($request);
        $loteIds = Lote::where('fazenda_id', $fazendaSelecionadaId)->pluck('id');

        $historico = Ordenha::whereIn('lote_id', $loteIds)
            ->whereDate('data', '>=', $dataInicio)
            ->whereDate('data', '<=', $dataFim)
            ->with('lote')
            ->orderByDesc('data')
            ->orderByDesc('horario')
            ->get();

        $restante = $historico->slice(self::HISTORICO_INICIAL)->values();

        return response()->json([
            'success' => true,
            'html' => view('partials.ordenhas-recentes-linhas', ['ordenhas' => $restante])->render(),
        ]);
    }

    /**
     * Período de "acompanhar a produção por período": se o usuário não
     * informar data_inicio/data_fim, usa a janela padrão dos últimos
     * HISTORICO_JANELA_DIAS_PADRAO dias até hoje.
     */
    private function periodoDaRequisicao(Request $request): array
    {
        $hoje = Carbon::today();

        $dataFim = $request->query('data_fim')
            ? Carbon::parse($request->query('data_fim'))->startOfDay()
            : $hoje->copy();

        $dataInicio = $request->query('data_inicio')
            ? Carbon::parse($request->query('data_inicio'))->startOfDay()
            : $hoje->copy()->subDays(self::HISTORICO_JANELA_DIAS_PADRAO);

        if ($dataInicio->greaterThan($dataFim)) {
            [$dataInicio, $dataFim] = [$dataFim, $dataInicio];
        }

        return [$dataInicio, $dataFim];
    }

    /** Estrutura completa da tela sem nenhuma fazenda selecionada: tudo zerado. */
    private function dadosVazios($fazendas, Carbon $dataInicio, Carbon $dataFim): array
    {
        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => null,
            'dataInicio' => $dataInicio->format('Y-m-d'),
            'dataFim' => $dataFim->format('Y-m-d'),
            'lotesComOrdenha' => 0,
            'producaoLeiteHoje' => 0.0,
            'producaoMediaDiaria' => 0.0,
            'variacaoProducaoHoje' => null,
            'variacaoProducaoMes' => null,
            'melhorLoteNome' => null,
            'melhorLoteTotal' => 0.0,
            'labelsProducaoDiaria' => $this->labelsUltimos7Dias(),
            'dadosProducaoDiaria' => array_fill(0, 7, 0.0),
            'labelsEvolucaoProducao' => $this->labelsUltimos6Meses(),
            'dadosEvolucaoProducao' => array_fill(0, 6, 0.0),
            'labelsProducaoPorLote' => [],
            'dadosProducaoPorLote' => [],
            'historicoOrdenhas' => collect(),
            'historicoOrdenhasTotal' => 0,
        ];
    }

    /** Estrutura completa da tela com todos os cartões, gráficos, comparativos e histórico reais da fazenda escolhida. */
    private function dadosDaFazenda($fazendas, $fazendaSelecionadaId, Carbon $dataInicio, Carbon $dataFim): array
    {
        $loteIds = Lote::where('fazenda_id', $fazendaSelecionadaId)->pluck('id');

        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();

        $producaoLeiteHoje = (float) Ordenha::whereIn('lote_id', $loteIds)->whereDate('data', $hoje)->sum('quantidade_litros');
        $producaoLeiteOntem = (float) Ordenha::whereIn('lote_id', $loteIds)->whereDate('data', $ontem)->sum('quantidade_litros');
        $variacaoProducaoHoje = $this->variacaoPercentual($producaoLeiteHoje, $producaoLeiteOntem);

        // Produção dos últimos 7 dias — base tanto do gráfico diário quanto
        // da "média diária" (não há mais Lactação individual pra persistir
        // essa média; ela é sempre recalculada: total do lote nos últimos 7
        // dias / 7).
        $ordenhas7Dias = Ordenha::whereIn('lote_id', $loteIds)->whereDate('data', '>=', Carbon::today()->subDays(6))->get();
        $producaoMediaDiaria = round(((float) $ordenhas7Dias->sum('quantidade_litros')) / 7, 2);

        $labelsProducaoDiaria = $this->labelsUltimos7Dias();
        $dadosProducaoDiaria = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $dadosProducaoDiaria[] = (float) $ordenhas7Dias->filter(fn ($o) => $o->data->isSameDay($dia))->sum('quantidade_litros');
        }

        // "Lotes em produção": lotes elegíveis à ordenha (espécie/sexo) que
        // tiveram ao menos um registro de ordenha nos últimos 7 dias.
        $lotesComOrdenha = $ordenhas7Dias->pluck('lote_id')->unique()->count();

        // Evolução da produção — total de litros por mês, últimos 6 meses.
        $ordenhas6Meses = Ordenha::whereIn('lote_id', $loteIds)
            ->whereDate('data', '>=', Carbon::today()->subMonths(5)->startOfMonth())
            ->get();
        $labelsEvolucaoProducao = $this->labelsUltimos6Meses();
        $dadosEvolucaoProducao = [];
        for ($i = 5; $i >= 0; $i--) {
            $mesReferencia = Carbon::today()->subMonths($i);
            $dadosEvolucaoProducao[] = (float) $ordenhas6Meses->filter(fn ($o) => $o->data->isSameMonth($mesReferencia))->sum('quantidade_litros');
        }
        $producaoMesAtual = $dadosEvolucaoProducao[5] ?? 0.0;
        $producaoMesAnterior = $dadosEvolucaoProducao[4] ?? 0.0;
        $variacaoProducaoMes = $this->variacaoPercentual($producaoMesAtual, $producaoMesAnterior);

        // Produção por lote — top 8 do mês atual (também usado para o destaque "Lote com Maior Produção").
        $producaoPorLoteMes = Ordenha::whereIn('lote_id', $loteIds)
            ->whereMonth('data', $hoje->month)
            ->whereYear('data', $hoje->year)
            ->select('lote_id', DB::raw('SUM(quantidade_litros) as total'))
            ->groupBy('lote_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $nomesPorLoteId = Lote::whereIn('id', $loteIds)->pluck('nome_lote', 'id');
        $labelsProducaoPorLote = $producaoPorLoteMes->map(fn ($p) => $nomesPorLoteId[$p->lote_id] ?? '—')->values()->all();
        $dadosProducaoPorLote = $producaoPorLoteMes->pluck('total')->map(fn ($v) => (float) $v)->values()->all();
        $melhorLoteNome = $labelsProducaoPorLote[0] ?? null;
        $melhorLoteTotal = (float) ($dadosProducaoPorLote[0] ?? 0);

        // Histórico de ordenhas no período selecionado, carregado só até
        // HISTORICO_INICIAL — o restante vem via historicoJson() sob demanda.
        $historicoCompleto = Ordenha::whereIn('lote_id', $loteIds)
            ->whereDate('data', '>=', $dataInicio)
            ->whereDate('data', '<=', $dataFim)
            ->with('lote')
            ->orderByDesc('data')
            ->orderByDesc('horario')
            ->get();

        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => $fazendaSelecionadaId,
            'dataInicio' => $dataInicio->format('Y-m-d'),
            'dataFim' => $dataFim->format('Y-m-d'),

            'lotesComOrdenha' => $lotesComOrdenha,
            'producaoLeiteHoje' => $producaoLeiteHoje,
            'producaoMediaDiaria' => $producaoMediaDiaria,
            'variacaoProducaoHoje' => $variacaoProducaoHoje,
            'variacaoProducaoMes' => $variacaoProducaoMes,
            'melhorLoteNome' => $melhorLoteNome,
            'melhorLoteTotal' => $melhorLoteTotal,

            'labelsProducaoDiaria' => $labelsProducaoDiaria,
            'dadosProducaoDiaria' => $dadosProducaoDiaria,
            'labelsEvolucaoProducao' => $labelsEvolucaoProducao,
            'dadosEvolucaoProducao' => $dadosEvolucaoProducao,
            'labelsProducaoPorLote' => $labelsProducaoPorLote,
            'dadosProducaoPorLote' => $dadosProducaoPorLote,

            'historicoOrdenhas' => $historicoCompleto->take(self::HISTORICO_INICIAL)->values(),
            'historicoOrdenhasTotal' => $historicoCompleto->count(),
        ];
    }

    /** Rótulos (Dom..Sáb) dos últimos 7 dias, terminando hoje. */
    private function labelsUltimos7Dias(): array
    {
        $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = $diasSemana[Carbon::today()->subDays($i)->dayOfWeek];
        }

        return $labels;
    }

    /** Rótulos (mês/ano abreviado) dos últimos 6 meses, terminando no atual. */
    private function labelsUltimos6Meses(): array
    {
        $mesesAbreviados = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::today()->subMonths($i);
            $labels[] = $mesesAbreviados[$mes->month - 1] . '/' . $mes->format('y');
        }

        return $labels;
    }

    /** Variação percentual entre o valor atual e o anterior, ou null sem base de comparação. */
    private function variacaoPercentual(float $atual, float $anterior): ?float
    {
        if ($anterior <= 0) {
            return $atual > 0 ? null : 0.0;
        }

        return round((($atual - $anterior) / $anterior) * 100, 1);
    }
}
