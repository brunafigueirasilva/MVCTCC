<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Http\Requests\AtualizarPerfilRequest;
use App\Models\Cadastro;
use App\Models\CodigoVerificacao;
use App\Models\Racao;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Cocho;
use App\Models\Plano;
use App\Models\Animal;
use App\Models\Parto;
use App\Models\Lactacao;
use App\Models\Ordenha;
use App\Models\Bezerro;
use App\Notifications\CodigoVerificacaoNotification;
use App\Notifications\PerfilAtualizadoNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CadastroController extends Controller
{
    // Preço médio de referência por kg de ração (mesmo valor usado no
    // módulo de Relatórios, já que não existe um campo de preço por ração
    // cadastrado no sistema). Fica centralizado aqui para todo o cálculo
    // financeiro do dashboard sair pronto do back-end.
    private const PRECO_MEDIO_KG = 3.5;

    // Política de senha do sistema: mínimo 8 caracteres, com pelo menos uma
    // letra maiúscula, uma minúscula, um número e o caractere @.
    private const SENHA_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*@).{8,}$/';
    private const SENHA_MENSAGEM = 'A senha deve ter no mínimo 8 caracteres e incluir letra maiúscula, letra minúscula, número e o caractere @.';

    // TELA LOGIN
    public function login()
    {
        return view('login');
    }

    // DASHBOARD
    public function dashboard(Request $request)
    {
        // A rota não tinha nenhum middleware de autenticação: qualquer
        // pessoa com o link acessava o painel (fazendas, lotes, consumo,
        // alertas) sem estar logada.
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        // Fazenda escolhida na barra "Unidade Produtiva". Quando definida,
        // todo o painel (lotes, cochos, animais e histórico de consumo)
        // passa a refletir somente essa fazenda. Sem fazenda selecionada,
        // todo cartão/gráfico/indicador é exibido zerado.
        $fazendaSelecionadaId = $request->query('fazenda_id') ?: null;

        // Fazendas são exclusivas de cada usuário: o painel só pode listar e
        // aceitar como selecionada uma fazenda que pertença à conta logada,
        // senão bastaria trocar o "fazenda_id" na URL para ver os lotes,
        // cochos e alertas de outra pessoa.
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))
            ->orderBy('nome_fazenda')
            ->get();

        if ($fazendaSelecionadaId && !$fazendas->contains('id', (int) $fazendaSelecionadaId)) {
            $fazendaSelecionadaId = null;
        }

        // Com uma única fazenda cadastrada não faz sentido obrigar o usuário
        // a escolhê-la manualmente na barra "Unidade Produtiva" — ela já
        // vem selecionada de cara.
        if (!$fazendaSelecionadaId && $fazendas->count() === 1) {
            $fazendaSelecionadaId = $fazendas->first()->id;
        }

        // O modal "Editar Perfil" (acessível pelo avatar na navbar) precisa
        // dos dados do usuário logado para pré-preencher o formulário; sem
        // isso, os campos chegavam sempre vazios e qualquer alteração
        // enviada por ali sobrescrevia os dados reais com valores em branco.
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        if (!$fazendaSelecionadaId) {
            return view('dashboard', $this->dadosDashboardVazio($fazendas) + ['cadastroLogado' => $cadastroLogado]);
        }

        return view('dashboard', $this->dadosDashboardDaFazenda($fazendas, $fazendaSelecionadaId) + ['cadastroLogado' => $cadastroLogado]);
    }

    /**
     * Estrutura completa do dashboard sem nenhuma fazenda selecionada:
     * todos os cartões, gráficos e indicadores zerados.
     */
    private function dadosDashboardVazio($fazendas): array
    {
        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => null,
            'lotesCount' => 0,
            'totalAnimais' => 0,
            'lotesNovosSemana' => 0,
            'cochosCount' => 0,
            'nivelMedioCochos' => 0,
            'consumoHoje' => 0.0,
            'variacaoConsumo' => null,
            'custoHoje' => 0.0,
            'precoMedioKg' => self::PRECO_MEDIO_KG,
            'eficienciaAlimentar' => 0.0,
            'estoqueAtual' => 0.0,
            'estoqueMinimo' => 0.0,
            'diasRestantesEstoque' => null,
            'labelsConsumo' => $this->labelsUltimos7Dias(),
            'dadosConsumo' => array_fill(0, 7, 0.0),
            'labelsAnimais' => [],
            'dadosAnimais' => [],
            'alertas' => collect(),

            // Módulo de Animal (Parto/Reprodução/Lactação/Ordenha) — fonte
            // separada do módulo de Lote acima, nunca somada a ele.
            'animaisEmLactacao' => 0,
            'producaoLeiteHoje' => 0.0,
            'producaoMediaDiaria' => 0.0,
            'partosProximos' => 0,
            'partosAtrasados' => 0,
            'partosRealizados' => 0,
            'bezerrosNascidos' => 0,
            'labelsProducaoDiaria' => $this->labelsUltimos7Dias(),
            'dadosProducaoDiaria' => array_fill(0, 7, 0.0),
            'labelsEvolucaoProducao' => $this->labelsUltimos6Meses(),
            'dadosEvolucaoProducao' => array_fill(0, 6, 0.0),
            'labelsProducaoPorAnimal' => [],
            'dadosProducaoPorAnimal' => [],
            'labelsPartosPorPeriodo' => $this->labelsUltimos6Meses(),
            'dadosPartosPorPeriodo' => array_fill(0, 6, 0),
        ];
    }

    /**
     * Estrutura completa do dashboard com todos os cartões, gráficos e
     * indicadores calculados a partir dos dados reais da fazenda escolhida.
     */
    private function dadosDashboardDaFazenda($fazendas, $fazendaSelecionadaId): array
    {
        $lotes = Lote::where('fazenda_id', $fazendaSelecionadaId)->get();
        $loteIds = $lotes->pluck('id');
        $loteIdsTexto = $loteIds->map(fn ($id) => (string) $id);

        $cochos = Cocho::whereIn('lote_id', $loteIds)->get();
        $nivelMedioCochos = $cochos->isNotEmpty()
            ? (int) round($cochos->avg(fn ($c) => $c->capacidade > 0 ? min(100, ($c->nivel_atual / $c->capacidade) * 100) : 0))
            : 0;

        $seteDiasAtras = Carbon::today()->subDays(7);
        $lotesNovosSemana = $lotes->filter(fn ($l) => $l->data_entrada && Carbon::parse($l->data_entrada)->greaterThanOrEqualTo($seteDiasAtras))->count();

        // Consumo real registrado no Plano Alimentar (mesma fonte usada nos
        // Relatórios), filtrado pelos lotes da fazenda selecionada.
        $planos = Plano::whereIn('lote_id', $loteIdsTexto)->get();

        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();

        $consumoHoje = (float) $planos->filter(fn ($p) => Carbon::parse($p->horario)->isSameDay($hoje))->sum('quantidade');
        $consumoOntem = (float) $planos->filter(fn ($p) => Carbon::parse($p->horario)->isSameDay($ontem))->sum('quantidade');

        $custoHoje = round($consumoHoje * self::PRECO_MEDIO_KG, 2);

        $variacaoConsumo = $this->variacaoPercentual($consumoHoje, $consumoOntem);

        // Histórico dos últimos 7 dias a partir do Plano Alimentar.
        $labelsConsumo = $this->labelsUltimos7Dias();
        $dadosConsumo = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $dadosConsumo[] = (float) $planos
                ->filter(fn ($p) => Carbon::parse($p->horario)->isSameDay($dia))
                ->sum('quantidade');
        }

        // Eficiência alimentar real: quanto do consumo ideal planejado
        // (planos.consumo_ideal) foi de fato consumido (planos.quantidade).
        $totalIdeal = (float) $planos->sum(fn ($p) => (float) $p->consumo_ideal);
        $eficienciaAlimentar = $totalIdeal > 0
            ? round(($planos->sum('quantidade') / $totalIdeal) * 100, 1)
            : 0.0;

        // Rações cadastradas para esta fazenda. Antes disso era derivado
        // via Plano (rações "usadas" nos planos alimentares), porque a
        // tabela de rações não guardava fazenda_id — desde que esse campo
        // foi adicionado, uma ração recém-cadastrada mas ainda não usada
        // em nenhum plano ficava invisível pros alertas de estoque baixo.
        $racoesFazenda = Racao::where('fazenda_id', $fazendaSelecionadaId)->get();

        $estoqueAtual = (float) $racoesFazenda->sum('estoque');
        $consumoMedioDiarioRacao = (float) $racoesFazenda->sum('consumo');
        $estoqueMinimo = round($consumoMedioDiarioRacao * Racao::DIAS_SEGURANCA_ESTOQUE, 2);
        $diasRestantesEstoque = $consumoMedioDiarioRacao > 0
            ? (int) floor($estoqueAtual / $consumoMedioDiarioRacao)
            : null;

        $animaisPorTipo = Lote::select('tipo_animal', DB::raw('SUM(quantidade_animais) as total'))
            ->where('fazenda_id', $fazendaSelecionadaId)
            ->groupBy('tipo_animal')
            ->orderByDesc('total')
            ->get();

        $labelsAnimais = $animaisPorTipo->pluck('tipo_animal')->values()->all();
        $dadosAnimais = $animaisPorTipo->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

        // ==========================================
        // MÓDULO DE ANIMAL (Parto/Reprodução/Lactação/Ordenha) — fonte de
        // dados separada do módulo de Lote acima (nunca somada com
        // LoteOcorrencia), conforme decidido: os dois módulos coexistem.
        // ==========================================
        $animais = Animal::whereIn('lote_id', $loteIds)->get();
        $animalIds = $animais->pluck('id');

        $animaisEmLactacao = $animais->where('status_reprodutivo', 'Em lactação')->count();

        $producaoLeiteHoje = (float) Ordenha::whereIn('animal_id', $animalIds)
            ->whereDate('data', $hoje)
            ->sum('quantidade_litros');

        $lactacoesAtivas = Lactacao::whereIn('animal_id', $animalIds)->where('status', 'Ativa')->with('animal')->get();
        $producaoMediaDiaria = $lactacoesAtivas->isNotEmpty()
            ? round((float) $lactacoesAtivas->avg('media_diaria'), 2)
            : 0.0;

        // O accessor Parto::status calcula Previsto/Próximo/Atrasado a
        // partir das datas — não é uma coluna filtrável via query, então o
        // agrupamento é feito em memória sobre a coleção já carregada.
        $partos = Parto::whereIn('animal_id', $animalIds)->with('animal')->get();
        $partosProximos = $partos->filter(fn ($p) => $p->status === 'Próximo')->count();
        $partosAtrasados = $partos->filter(fn ($p) => $p->status === 'Atrasado')->count();
        $partosRealizados = $partos->filter(
            fn ($p) => $p->status === 'Realizado' && $p->data_parto && $p->data_parto->greaterThanOrEqualTo(Carbon::today()->subDays(30))
        )->count();

        $bezerrosNascidos = Bezerro::whereIn('parto_id', $partos->pluck('id'))
            ->where('data_nascimento', '>=', Carbon::today()->subDays(30))
            ->count();

        // Produção de leite dos últimos 7 dias (mesmo loop de $dadosConsumo).
        $ordenhas7Dias = Ordenha::whereIn('animal_id', $animalIds)
            ->whereDate('data', '>=', Carbon::today()->subDays(6))
            ->get();
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
            $dadosEvolucaoProducao[] = (float) $ordenhas6Meses
                ->filter(fn ($o) => $o->data->isSameMonth($mesReferencia))
                ->sum('quantidade_litros');
        }

        // Produção por animal — top 8 do mês atual.
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

        // Partos por período — quantidade de partos REALIZADOS por mês, 6 meses.
        $partosRealizadosTodos = $partos->filter(fn ($p) => $p->status === 'Realizado' && $p->data_parto);
        $labelsPartosPorPeriodo = $this->labelsUltimos6Meses();
        $dadosPartosPorPeriodo = [];
        for ($i = 5; $i >= 0; $i--) {
            $mesReferencia = Carbon::today()->subMonths($i);
            $dadosPartosPorPeriodo[] = $partosRealizadosTodos->filter(fn ($p) => $p->data_parto->isSameMonth($mesReferencia))->count();
        }

        // Todo alerta possível vem de uma condição real dos dados (nunca
        // inventado): estoque de ração, nível dos cochos, lotes sem cocho
        // vinculado, lotes sem alimentação registrada hoje, partos
        // atrasados e lactações ativas sem ordenha registrada hoje.
        $alertas = collect()
            ->merge($this->alertasEstoqueRacao($racoesFazenda))
            ->merge($this->alertasCochos($cochos))
            ->merge($this->alertasLotesSemCocho($lotes, $cochos))
            ->merge($this->alertasAlimentacaoHoje($lotes, $planos, $hoje))
            ->merge($this->alertasPartosAtrasados($partos))
            ->merge($this->alertasLactacaoSemOrdenhaHoje($lactacoesAtivas, $ordenhas7Dias, $hoje))
            ->sortBy(fn ($alerta) => $alerta->prioridade === 'critico' ? 0 : 1)
            ->values();

        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => $fazendaSelecionadaId,
            'lotesCount' => $lotes->count(),
            'totalAnimais' => (int) $lotes->sum('quantidade_animais'),
            'lotesNovosSemana' => $lotesNovosSemana,
            'cochosCount' => $cochos->count(),
            'nivelMedioCochos' => $nivelMedioCochos,
            'consumoHoje' => $consumoHoje,
            'variacaoConsumo' => $variacaoConsumo,
            'custoHoje' => $custoHoje,
            'precoMedioKg' => self::PRECO_MEDIO_KG,
            'eficienciaAlimentar' => $eficienciaAlimentar,
            'estoqueAtual' => $estoqueAtual,
            'estoqueMinimo' => $estoqueMinimo,
            'diasRestantesEstoque' => $diasRestantesEstoque,
            'labelsConsumo' => $labelsConsumo,
            'dadosConsumo' => $dadosConsumo,
            'labelsAnimais' => $labelsAnimais,
            'dadosAnimais' => $dadosAnimais,
            'alertas' => $alertas,

            'animaisEmLactacao' => $animaisEmLactacao,
            'producaoLeiteHoje' => $producaoLeiteHoje,
            'producaoMediaDiaria' => $producaoMediaDiaria,
            'partosProximos' => $partosProximos,
            'partosAtrasados' => $partosAtrasados,
            'partosRealizados' => $partosRealizados,
            'bezerrosNascidos' => $bezerrosNascidos,
            'labelsProducaoDiaria' => $labelsProducaoDiaria,
            'dadosProducaoDiaria' => $dadosProducaoDiaria,
            'labelsEvolucaoProducao' => $labelsEvolucaoProducao,
            'dadosEvolucaoProducao' => $dadosEvolucaoProducao,
            'labelsProducaoPorAnimal' => $labelsProducaoPorAnimal,
            'dadosProducaoPorAnimal' => $dadosProducaoPorAnimal,
            'labelsPartosPorPeriodo' => $labelsPartosPorPeriodo,
            'dadosPartosPorPeriodo' => $dadosPartosPorPeriodo,
        ];
    }

    /**
     * Estoque de ração: zerado (crítico, independente do consumo médio) ou
     * baixo o suficiente para acabar dentro da margem de segurança.
     */
    private function alertasEstoqueRacao($racoesFazenda)
    {
        return $racoesFazenda->flatMap(function ($racao) {
            $status = $racao->statusEstoque();

            if ($status === 'esgotado') {
                return [(object) [
                    'prioridade' => 'critico',
                    'titulo' => "Estoque zerado de {$racao->nome_racao}",
                    'descricao' => 'Não há mais estoque registrado desta ração. Reponha o quanto antes para não interromper a alimentação.',
                ]];
            }

            if ($status === 'baixo') {
                $diasRestantes = $racao->diasRestantesEstoque();

                return [(object) [
                    'prioridade' => $diasRestantes <= 3 ? 'critico' : 'baixo',
                    'titulo' => "Estoque baixo de {$racao->nome_racao}",
                    'descricao' => "Restam aproximadamente {$diasRestantes} dia(s) de estoque no ritmo de consumo atual.",
                ]];
            }

            return [];
        })->values();
    }

    /** Cochos vazios (crítico) ou com nível abaixo de 20% da capacidade. */
    private function alertasCochos($cochos)
    {
        return $cochos->flatMap(function ($cocho) {
            if ($cocho->capacidade <= 0) {
                return [];
            }

            if ($cocho->nivel_atual <= 0) {
                return [(object) [
                    'prioridade' => 'critico',
                    'titulo' => "Cocho {$cocho->nome_cocho} vazio",
                    'descricao' => 'Nenhuma ração disponível neste cocho no momento.',
                ]];
            }

            $percentual = ($cocho->nivel_atual / $cocho->capacidade) * 100;

            if ($percentual < 20) {
                return [(object) [
                    'prioridade' => 'baixo',
                    'titulo' => "Nível baixo no cocho {$cocho->nome_cocho}",
                    'descricao' => 'Está com apenas ' . round($percentual) . '% da capacidade — considere reabastecer.',
                ]];
            }

            return [];
        })->values();
    }

    /** Lotes com animais mas sem nenhum cocho de alimentação vinculado. */
    private function alertasLotesSemCocho($lotes, $cochos)
    {
        $loteIdsComCocho = $cochos->pluck('lote_id')->map(fn ($id) => (string) $id);

        return $lotes
            ->reject(fn ($lote) => $loteIdsComCocho->contains((string) $lote->id))
            ->map(fn ($lote) => (object) [
                'prioridade' => 'baixo',
                'titulo' => "{$lote->nome_lote} sem cocho cadastrado",
                'descricao' => 'Este lote não tem nenhum cocho de alimentação vinculado.',
            ])
            ->values();
    }

    /**
     * Lotes que já têm plano alimentar ativo (algum registro histórico em
     * "planos") mas nenhuma alimentação lançada hoje — indício de que a
     * rotina foi esquecida. Lotes recém-cadastrados, sem nenhum histórico
     * ainda, não geram esse alerta.
     */
    /** Partos previstos cuja data já venceu sem terem sido registrados como
     *  realizados (ver Parto::getStatusAttribute — status calculado). */
    private function alertasPartosAtrasados($partos)
    {
        return $partos->where('status', 'Atrasado')->map(fn ($parto) => (object) [
            'prioridade' => 'critico',
            'titulo' => "Parto atrasado: {$parto->animal->nome}",
            'descricao' => 'Data prevista em ' . $parto->data_prevista->format('d/m/Y') . ' já passou sem o parto ser registrado.',
        ])->values();
    }

    /** Animais com lactação ativa que não têm nenhuma ordenha registrada
     *  hoje — indício de que a rotina de ordenha foi esquecida. */
    private function alertasLactacaoSemOrdenhaHoje($lactacoesAtivas, $ordenhasRecentes, $hoje)
    {
        $lactacaoIdsComOrdenhaHoje = $ordenhasRecentes
            ->filter(fn ($o) => $o->data->isSameDay($hoje))
            ->pluck('lactacao_id');

        return $lactacoesAtivas
            ->reject(fn ($lactacao) => $lactacaoIdsComOrdenhaHoje->contains($lactacao->id))
            ->map(fn ($lactacao) => (object) [
                'prioridade' => 'baixo',
                'titulo' => "Sem ordenha registrada hoje: {$lactacao->animal->nome}",
                'descricao' => "Lactação nº {$lactacao->numero_lactacao} está ativa, mas nenhuma ordenha foi registrada hoje.",
            ])->values();
    }

    private function alertasAlimentacaoHoje($lotes, $planos, $hoje)
    {
        $planosPorLote = $planos->groupBy(fn ($p) => (string) $p->lote_id);

        return $lotes->flatMap(function ($lote) use ($planosPorLote, $hoje) {
            $planosDoLote = $planosPorLote->get((string) $lote->id, collect());

            if ($planosDoLote->isEmpty()) {
                return [];
            }

            $temRegistroHoje = $planosDoLote->contains(
                fn ($p) => Carbon::parse($p->horario)->isSameDay($hoje)
            );

            if ($temRegistroHoje) {
                return [];
            }

            return [(object) [
                'prioridade' => 'baixo',
                'titulo' => "Sem alimentação registrada hoje: {$lote->nome_lote}",
                'descricao' => 'Este lote tem plano alimentar ativo, mas nenhum registro de consumo foi feito hoje.',
            ]];
        })->values();
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

    /** Rótulos (mês/ano abreviado, em português) dos últimos 6 meses,
     *  terminando no atual — mesmo padrão de labelsUltimos7Dias() (nomes
     *  fixos em vez de depender do locale do Carbon, que não está
     *  configurado como pt-BR neste projeto). */
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

    /** Variação percentual entre hoje e o valor anterior, ou null sem base de comparação. */
    private function variacaoPercentual(float $atual, float $anterior): ?float
    {
        if ($anterior <= 0) {
            return $atual > 0 ? null : 0.0;
        }

        return round((($atual - $anterior) / $anterior) * 100, 1);
    }

    // PÁGINA PROJETO VÍDEO
    public function projetovideo()
    {
        return view('projetovideo');
    }

    // Landing page: cada seção (menos o Início/Hero) virou uma página
    // própria, sem rolagem entre elas — mesmo padrão que a Avaliações
    // (avaliacoes.blade.php) já usava.
    public function landingProblema()
    {
        return view('landing.problema');
    }

    public function landingSolucao()
    {
        return view('landing.solucao');
    }

    public function landingComoFunciona()
    {
        return view('landing.como-funciona');
    }

    public function landingBeneficios()
    {
        return view('landing.beneficios');
    }

    public function landingSobre()
    {
        return view('landing.sobre');
    }

    public function landingContato()
    {
        return view('landing.contato');
    }

    // LISTAR CADASTROS
    // Não existe uma tela dedicada de administração de usuários no momento.
    public function listar()
    {
        return redirect()->route('cadastro.index');
    }

    // FORMULÁRIO CADASTRO
    public function cadastro()
    {
        return view('cadastro');
    }

    // CADASTRAR USUÁRIO
    public function add(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:cadastros,email',
            'password' => ['required', 'string', 'confirmed', 'regex:' . self::SENHA_REGEX],
            'telefone' => 'required|string|max:20'
        ], [
            'password.regex' => self::SENHA_MENSAGEM,
        ]);

        Cadastro::create([
            'responsavel' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->password),
            'telefone' => $request->telefone
        ]);

        return redirect()
            ->route('login.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    // AUTENTICAR LOGIN
    public function autenticar(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Cadastro::where('email', $request->email)->first();

        if (!$usuario) {
            return back()
                ->withErrors([
                    'email' => 'Usuário não encontrado.'
                ])
                ->withInput();
        }

        if (!Hash::check($request->password, $usuario->senha)) {
            return back()
                ->withErrors([
                    'password' => 'Senha inválida.'
                ])
                ->withInput();
        }

        session([
            'usuario_id' => $usuario->id,
            'usuario_nome' => $usuario->responsavel,
            'usuario_email' => $usuario->email
        ]);

        return redirect()
            ->route('cadastro.index')
            ->with('success', 'Login realizado com sucesso!');
    }

    // LOGOUT
    public function logout()
    {
        session()->flush();

        return redirect()
            ->route('login.index')
            ->with('success', 'Logout realizado com sucesso!');
    }

    // PERFIL DO ADMINISTRADOR (acessado pelo avatar na navbar)
    public function perfil()
    {
        // Antes disparava um 404 (findOrFail) para quem acessasse sem
        // sessão, expondo o comportamento interno da rota. Agora manda
        // direto pro login, como as demais páginas internas do sistema.
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        $cadastro = Cadastro::findOrFail(session('usuario_id'));

        return view('perfil', compact('cadastro'));
    }

    // ATUALIZAR PERFIL
    //
    // Fluxo de segurança: qualquer alteração de perfil exige a senha atual
    // (AtualizarPerfilRequest::rules -> SenhaAtualCorreta), CPF só é aceito
    // para maiores de 18 anos (comprovado pela data de nascimento), a nova
    // senha precisa ser forte e diferente da anterior, e um e-mail de
    // alerta é disparado a cada alteração sensível — inclusive para o
    // endereço de e-mail ANTIGO quando o próprio e-mail é trocado, para que
    // o dono original da conta seja avisado mesmo que o atacante já tenha
    // trocado o e-mail cadastrado.
    public function atualizarPerfil(AtualizarPerfilRequest $request)
    {
        $cadastro = $request->cadastroLogado();
        $dadosValidados = $request->validated();

        $emailAnterior = $cadastro->email;
        $fotoAnterior = $cadastro->foto;

        $novoCpf = $dadosValidados['cpf'] ?? null;
        $senhaAlterada = $request->filled('password');
        $fotoAlterada = $request->hasFile('foto');
        $emailAlterado = $dadosValidados['email'] !== $emailAnterior;

        // A senha não entra na lista de "campos alterados" do aviso abaixo
        // porque ela ainda não foi aplicada neste momento — só entra em
        // vigor depois que o código de confirmação (enviado logo adiante)
        // for validado em confirmarSenha().
        $camposAlterados = array_filter([
            $dadosValidados['responsavel'] !== $cadastro->responsavel ? 'Nome' : null,
            $novoCpf !== $cadastro->cpf ? 'CPF' : null,
            $dadosValidados['telefone'] !== $cadastro->telefone ? 'Telefone' : null,
            $emailAlterado ? 'E-mail' : null,
            $fotoAlterada ? 'Foto de perfil' : null,
        ]);

        $dados = [
            'responsavel' => $dadosValidados['responsavel'],
            'cpf' => $novoCpf,
            'data_nascimento' => $dadosValidados['data_nascimento'] ?? null,
            'email' => $dadosValidados['email'],
            'telefone' => $dadosValidados['telefone'],
        ];

        if ($emailAlterado) {
            $dados['email_alterado_em'] = now();
        }

        if ($fotoAlterada) {
            $dados['foto'] = $request->file('foto')->store('perfil', 'public');
        }

        DB::transaction(function () use ($cadastro, $dados, $fotoAlterada, $fotoAnterior) {
            $cadastro->update($dados);

            // Só remove o arquivo antigo depois que o novo caminho já foi
            // gravado com sucesso, para nunca ficar sem nenhuma foto no
            // disco caso o update falhe.
            if ($fotoAlterada && $fotoAnterior) {
                Storage::disk('public')->delete($fotoAnterior);
            }
        });

        session(['usuario_nome' => $cadastro->responsavel]);

        if ($camposAlterados !== []) {
            $alerta = new PerfilAtualizadoNotification(
                array_values($camposAlterados),
                now()->format('d/m/Y H:i'),
                $request->ip()
            );

            $cadastro->notify($alerta);

            if ($emailAlterado) {
                Notification::route('mail', $emailAnterior)->notify($alerta);
            }
        }

        // A senha nova fica "pendente": guardada com hash junto do código
        // (nunca gravada no cadastro ainda) até o usuário confirmar o
        // código enviado por e-mail em /perfil/confirmar-senha.
        if ($senhaAlterada) {
            $codigo = CodigoVerificacao::gerar(
                $cadastro->email,
                'alteracao_senha',
                Hash::make($dadosValidados['password'])
            );

            Notification::route('mail', $cadastro->email)
                ->notify(new CodigoVerificacaoNotification($codigo, 'troca de senha'));

            return redirect()
                ->route('perfil.confirmarSenha')
                ->with('perfil_success', 'Perfil atualizado! Enviamos um código para confirmar a nova senha.');
        }

        // volta para a página de onde o formulário foi enviado: tanto a
        // tela /perfil quanto o modal "Editar Perfil" (presente na navbar
        // de todas as telas) usam esta mesma rota.
        return redirect()
            ->back()
            ->with('perfil_success', 'Perfil atualizado com sucesso!');
    }

    // FORMULÁRIO EDITAR
    // Não existe uma tela dedicada de administração de usuários no momento.
    public function atualizar($id)
    {
        Cadastro::findOrFail($id);

        return redirect()->route('cadastro.index');
    }

    // ATUALIZAR
    public function update(Request $request, $id)
    {
        $cadastro = Cadastro::findOrFail($id);

        $request->validate([
            'responsavel' => 'required|string|max:255',
            'email' => 'required|email|unique:cadastros,email,' . $id,
            'telefone' => 'required|string|max:20'
        ]);

        $dados = [
            'responsavel' => $request->responsavel,
            'email' => $request->email,
            'telefone' => $request->telefone
        ];

        if ($request->filled('password')) {
            $dados['senha'] = Hash::make($request->password);
        }

        $cadastro->update($dados);

        return redirect()
            ->route('cadastro.listar')
            ->with('success', 'Cadastro atualizado com sucesso!');
    }

    // EXCLUIR
    public function deletar($id)
    {
        $cadastro = Cadastro::findOrFail($id);

        $cadastro->delete();

        return redirect()
            ->route('cadastro.listar')
            ->with('success', 'Cadastro deletado com sucesso!');
    }

    // ==========================================
    // ESQUECEU A SENHA & REDEFINIÇÃO (por código de verificação)
    // ==========================================

    // TELA: Solicitar redefinição (informar o e-mail)
    public function esqueciSenha()
    {
        return view('esqueciSenha');
    }

    // AÇÃO: Gera o código de verificação e envia por e-mail
    public function enviarCodigoSenha(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:cadastros,email'
        ], [
            'email.exists' => 'Este e-mail não está cadastrado no sistema.'
        ]);

        $codigo = CodigoVerificacao::gerar($request->email, 'redefinicao_senha');

        Notification::route('mail', $request->email)
            ->notify(new CodigoVerificacaoNotification($codigo, 'redefinição de senha'));

        return redirect()
            ->route('password.reset', ['email' => $request->email])
            ->with('success', 'Enviamos um código de verificação para o seu e-mail.');
    }

    // TELA: Digitar o código + nova senha
    public function mostrarFormRedefinir(Request $request)
    {
        $email = $request->query('email');

        return view('auth.reset-password', compact('email'));
    }

    // AÇÃO: Confirma o código e grava a nova senha
    public function redefinirSenha(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:cadastros,email',
            'codigo' => 'required|digits:6',
            'password' => ['required', 'string', 'confirmed', 'regex:' . self::SENHA_REGEX],
        ], [
            'password.regex' => self::SENHA_MENSAGEM,
        ]);

        $codigoValido = CodigoVerificacao::validar($request->email, $request->codigo, 'redefinicao_senha');

        if (!$codigoValido) {
            return back()->withErrors(['codigo' => 'Código inválido ou expirado.'])->withInput();
        }

        $usuario = Cadastro::where('email', $request->email)->first();
        $usuario->update([
            'senha' => Hash::make($request->password),
            'senha_alterada_em' => now(),
        ]);
        $codigoValido->marcarUsado();

        return redirect()
            ->route('login.index')
            ->with('success', 'Sua senha foi redefinida com sucesso!');
    }

    // ==========================================
    // CONFIRMAÇÃO DE TROCA DE SENHA (a partir do Perfil, já logado)
    // ==========================================

    // TELA: Digitar o código enviado para confirmar a troca de senha
    public function mostrarConfirmarSenha()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return view('perfilConfirmarSenha');
    }

    // AÇÃO: Confirma o código e efetiva a nova senha, deixada pendente em atualizarPerfil()
    public function confirmarSenha(Request $request)
    {
        $cadastro = Cadastro::find(session('usuario_id'));

        if (!$cadastro) {
            return redirect()->route('login.index');
        }

        $request->validate(['codigo' => 'required|digits:6']);

        $codigoValido = CodigoVerificacao::validar($cadastro->email, $request->codigo, 'alteracao_senha');

        if (!$codigoValido) {
            return back()->withErrors(['codigo' => 'Código inválido ou expirado.']);
        }

        $cadastro->update([
            'senha' => $codigoValido->payload,
            'senha_alterada_em' => now(),
        ]);
        $codigoValido->marcarUsado();

        return redirect()->route('perfil')->with('perfil_success', 'Senha alterada com sucesso!');
    }
}
