<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Bezerro;
use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Lactacao;
use App\Models\Lote;
use App\Models\Ordenha;
use App\Models\Parto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Tela "Ordenha e Leite": extraída do Dashboard (que só tinha os cartões e
 * gráficos do "Módulo de Animal" espalhados no meio do grid de Lote/Cocho/
 * Ração). Todo o cálculo aqui é o mesmo que já existia em
 * CadastroController::dadosDashboardDaFazenda() — só ganhou os comparativos
 * (hoje vs ontem, mês vs mês anterior), o destaque de melhor produtora e o
 * histórico de ordenhas, que não cabiam dentro do Dashboard.
 */
class OrdenhaLeiteController extends Controller
{
    private const HISTORICO_JANELA_DIAS = 60;
    private const HISTORICO_INICIAL = 8;

    public function listar(Request $request)
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        $fazendaSelecionadaId = $request->query('fazenda_id') ?: null;

        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))
            ->orderBy('nome_fazenda')
            ->get();

        if ($fazendaSelecionadaId && !$fazendas->contains('id', (int) $fazendaSelecionadaId)) {
            $fazendaSelecionadaId = null;
        }

        if (!$fazendaSelecionadaId && $fazendas->count() === 1) {
            $fazendaSelecionadaId = $fazendas->first()->id;
        }

        $cadastroLogado = Cadastro::find(session('usuario_id'));

        if (!$fazendaSelecionadaId) {
            return view('ordenhaleite', $this->dadosVazios($fazendas) + ['cadastroLogado' => $cadastroLogado]);
        }

        return view('ordenhaleite', $this->dadosDaFazenda($fazendas, $fazendaSelecionadaId) + ['cadastroLogado' => $cadastroLogado]);
    }

    /**
     * CONSULTAR MAIS ORDENHAS: restante do histórico (fora dos
     * HISTORICO_INICIAL mais recentes já exibidos) via AJAX — mesmo padrão
     * de "ver mais" já usado em Plano Alimentar/Relatórios.
     */
    public function historicoJson(Request $request)
    {
        $fazendaSelecionadaId = $request->query('fazenda_id') ?: null;
        $fazendasDoUsuario = Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');

        if (!$fazendaSelecionadaId || !$fazendasDoUsuario->contains((int) $fazendaSelecionadaId)) {
            return response()->json(['success' => true, 'html' => '']);
        }

        $animalIds = Animal::whereIn('lote_id', Lote::where('fazenda_id', $fazendaSelecionadaId)->pluck('id'))->pluck('id');

        $historico = Ordenha::whereIn('animal_id', $animalIds)
            ->whereDate('data', '>=', Carbon::today()->subDays(self::HISTORICO_JANELA_DIAS))
            ->with('animal')
            ->orderByDesc('data')
            ->orderByDesc('horario')
            ->get();

        $restante = $historico->slice(self::HISTORICO_INICIAL)->values();

        return response()->json([
            'success' => true,
            'html' => view('partials.ordenhas-recentes-linhas', ['ordenhas' => $restante])->render(),
        ]);
    }

    /** Estrutura completa da tela sem nenhuma fazenda selecionada: tudo zerado. */
    private function dadosVazios($fazendas): array
    {
        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => null,
            'animaisEmLactacao' => 0,
            'producaoLeiteHoje' => 0.0,
            'producaoMediaDiaria' => 0.0,
            'variacaoProducaoHoje' => null,
            'variacaoProducaoMes' => null,
            'partosProximos' => 0,
            'partosAtrasados' => 0,
            'partosRealizados' => 0,
            'bezerrosNascidos' => 0,
            'melhorProdutoraNome' => null,
            'melhorProdutoraTotal' => 0.0,
            'labelsProducaoDiaria' => $this->labelsUltimos7Dias(),
            'dadosProducaoDiaria' => array_fill(0, 7, 0.0),
            'labelsEvolucaoProducao' => $this->labelsUltimos6Meses(),
            'dadosEvolucaoProducao' => array_fill(0, 6, 0.0),
            'labelsProducaoPorAnimal' => [],
            'dadosProducaoPorAnimal' => [],
            'labelsPartosPorPeriodo' => $this->labelsUltimos6Meses(),
            'dadosPartosPorPeriodo' => array_fill(0, 6, 0),
            'historicoOrdenhas' => collect(),
            'historicoOrdenhasTotal' => 0,
        ];
    }

    /** Estrutura completa da tela com todos os cartões, gráficos, comparativos e histórico reais da fazenda escolhida. */
    private function dadosDaFazenda($fazendas, $fazendaSelecionadaId): array
    {
        $loteIds = Lote::where('fazenda_id', $fazendaSelecionadaId)->pluck('id');
        $animais = Animal::whereIn('lote_id', $loteIds)->get();
        $animalIds = $animais->pluck('id');

        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();

        $animaisEmLactacao = $animais->where('status_reprodutivo', 'Em lactação')->count();

        $producaoLeiteHoje = (float) Ordenha::whereIn('animal_id', $animalIds)->whereDate('data', $hoje)->sum('quantidade_litros');
        $producaoLeiteOntem = (float) Ordenha::whereIn('animal_id', $animalIds)->whereDate('data', $ontem)->sum('quantidade_litros');
        $variacaoProducaoHoje = $this->variacaoPercentual($producaoLeiteHoje, $producaoLeiteOntem);

        $lactacoesAtivas = Lactacao::whereIn('animal_id', $animalIds)->where('status', 'Ativa')->with('animal')->get();
        $producaoMediaDiaria = $lactacoesAtivas->isNotEmpty() ? round((float) $lactacoesAtivas->avg('media_diaria'), 2) : 0.0;

        // Partos: accessor calculado em memória (não é coluna filtrável via query).
        $partos = Parto::whereIn('animal_id', $animalIds)->with('animal')->get();
        $partosProximos = $partos->filter(fn ($p) => $p->status === 'Próximo')->count();
        $partosAtrasados = $partos->filter(fn ($p) => $p->status === 'Atrasado')->count();
        $partosRealizados = $partos->filter(
            fn ($p) => $p->status === 'Realizado' && $p->data_parto && $p->data_parto->greaterThanOrEqualTo(Carbon::today()->subDays(30))
        )->count();

        $bezerrosNascidos = Bezerro::whereIn('parto_id', $partos->pluck('id'))
            ->where('data_nascimento', '>=', Carbon::today()->subDays(30))
            ->count();

        // Produção diária dos últimos 7 dias.
        $ordenhas7Dias = Ordenha::whereIn('animal_id', $animalIds)->whereDate('data', '>=', Carbon::today()->subDays(6))->get();
        $labelsProducaoDiaria = $this->labelsUltimos7Dias();
        $dadosProducaoDiaria = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $dadosProducaoDiaria[] = (float) $ordenhas7Dias->filter(fn ($o) => $o->data->isSameDay($dia))->sum('quantidade_litros');
        }

        // Evolução da produção — total de litros por mês, últimos 6 meses.
        $ordenhas6Meses = Ordenha::whereIn('animal_id', $animalIds)
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

        // Produção por animal — top 8 do mês atual (também usado para o destaque "Melhor Produtora").
        $producaoPorAnimalMes = Ordenha::whereIn('animal_id', $animalIds)
            ->whereMonth('data', $hoje->month)
            ->whereYear('data', $hoje->year)
            ->select('animal_id', DB::raw('SUM(quantidade_litros) as total'))
            ->groupBy('animal_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $nomesPorAnimalId = $animais->pluck('nome', 'id');
        $labelsProducaoPorAnimal = $producaoPorAnimalMes->map(fn ($p) => $nomesPorAnimalId[$p->animal_id] ?? '—')->values()->all();
        $dadosProducaoPorAnimal = $producaoPorAnimalMes->pluck('total')->map(fn ($v) => (float) $v)->values()->all();
        $melhorProdutoraNome = $labelsProducaoPorAnimal[0] ?? null;
        $melhorProdutoraTotal = (float) ($dadosProducaoPorAnimal[0] ?? 0);

        // Partos por período — quantidade de partos REALIZADOS por mês, 6 meses.
        $partosRealizadosTodos = $partos->filter(fn ($p) => $p->status === 'Realizado' && $p->data_parto);
        $labelsPartosPorPeriodo = $this->labelsUltimos6Meses();
        $dadosPartosPorPeriodo = [];
        for ($i = 5; $i >= 0; $i--) {
            $mesReferencia = Carbon::today()->subMonths($i);
            $dadosPartosPorPeriodo[] = $partosRealizadosTodos->filter(fn ($p) => $p->data_parto->isSameMonth($mesReferencia))->count();
        }

        // Histórico recente de ordenhas (últimos 60 dias), carregado só até
        // HISTORICO_INICIAL — o restante vem via historicoJson() sob demanda.
        $historicoCompleto = Ordenha::whereIn('animal_id', $animalIds)
            ->whereDate('data', '>=', Carbon::today()->subDays(self::HISTORICO_JANELA_DIAS))
            ->with('animal')
            ->orderByDesc('data')
            ->orderByDesc('horario')
            ->get();

        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => $fazendaSelecionadaId,

            'animaisEmLactacao' => $animaisEmLactacao,
            'producaoLeiteHoje' => $producaoLeiteHoje,
            'producaoMediaDiaria' => $producaoMediaDiaria,
            'variacaoProducaoHoje' => $variacaoProducaoHoje,
            'variacaoProducaoMes' => $variacaoProducaoMes,
            'partosProximos' => $partosProximos,
            'partosAtrasados' => $partosAtrasados,
            'partosRealizados' => $partosRealizados,
            'bezerrosNascidos' => $bezerrosNascidos,
            'melhorProdutoraNome' => $melhorProdutoraNome,
            'melhorProdutoraTotal' => $melhorProdutoraTotal,

            'labelsProducaoDiaria' => $labelsProducaoDiaria,
            'dadosProducaoDiaria' => $dadosProducaoDiaria,
            'labelsEvolucaoProducao' => $labelsEvolucaoProducao,
            'dadosEvolucaoProducao' => $dadosEvolucaoProducao,
            'labelsProducaoPorAnimal' => $labelsProducaoPorAnimal,
            'dadosProducaoPorAnimal' => $dadosProducaoPorAnimal,
            'labelsPartosPorPeriodo' => $labelsPartosPorPeriodo,
            'dadosPartosPorPeriodo' => $dadosPartosPorPeriodo,

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
