<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cadastro;
use App\Models\DesempenhoLote;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use App\Models\Racao;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PlanoController extends Controller
{
    // Preço médio de referência por kg de ração (mesmo valor usado no
    // Dashboard/Relatórios, já que não existe um campo de preço por ração
    // cadastrado no sistema).
    private const PRECO_MEDIO_KG = 3.5;

    // Quantidade de lotes exibidos de início no card "Consumo por Lote -
    // Plano Diário"; o restante só é carregado ao clicar em "Consultar
    // Lotes".
    private const LOTES_DASHBOARD_INICIAL = 3;

    // Quantidade de cards exibidos de início em "Conversão Alimentar por
    // Lote"; o restante só é carregado ao clicar em "Consultar mais
    // Conversões" — evita a seção crescer sem limite com muitos lotes.
    private const CONVERSOES_INICIAL = 3;

    /**
     * Lotes/Rações pertencem a uma fazenda do usuário logado — mesmo
     * padrão de escopo usado em Cocho/Estoque/RacaoController, pra nunca
     * vazar dado de outra conta.
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

    private function exigirLogin()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return null;
    }

    /**
     * Rações compatíveis com um lote: mesma fazenda, mesmo tipo de animal
     * e sexo compatível ("Misto" aceita qualquer um) — mesma regra de
     * compatibilidade já usada no cadastro de Lote/Estoque. Usada tanto
     * pra montar a lista de opções do "vincular ração" quanto pra validar
     * no servidor a ração escolhida (fonte única de verdade — ver
     * vincularRacao()).
     */
    private function racoesCompativeisComLote($lote, $racoes)
    {
        return $racoes->filter(function ($racao) use ($lote) {
            if ($racao->fazenda_id !== $lote->fazenda_id) {
                return false;
            }

            if ($racao->tipo !== $lote->tipo_animal) {
                return false;
            }

            if ($racao->sexo && $lote->sexo
                && $racao->sexo !== 'Misto' && $lote->sexo !== 'Misto'
                && $racao->sexo !== $lote->sexo) {
                return false;
            }

            return true;
        })->values();
    }

    /**
     * Ids de ração já vinculados a lotes do usuário (lote_id => racao_id,
     * só os que têm vínculo) — usado pra tirar da lista de "vincular
     * ração" as rações que já estão em uso em OUTRO lote (uma ração só
     * deve alimentar o cálculo de consumo/estoque de um lote por vez;
     * deixar vincular a dois lotes ao mesmo tempo contaria o mesmo
     * consumo/estoque duas vezes).
     */
    private function mapaRacaoPorLote(): Collection
    {
        return $this->lotesDoUsuario()->whereNotNull('racao_id')->pluck('racao_id', 'id');
    }

    /**
     * Ração efetivamente vinculada a um lote — só conta o vínculo
     * explícito salvo em lotes.racao_id (ver vincularRacao()). Sem
     * "adivinhação": se o usuário não vinculou nada, ou se a ração
     * vinculada não está mais no conjunto filtrado (ex.: filtro de Ração
     * aplicado na tela), retorna null e o lote fica "sem ração vinculada".
     */
    private function racaoVinculadaAoLote($lote, $racoes)
    {
        if (!$lote->racao_id) {
            return null;
        }

        return $racoes->firstWhere('id', $lote->racao_id);
    }

    /**
     * Status do plano de um lote a partir da ração vinculada (ou da falta
     * dela) — mesma regra usada na tabela "Consumo por Lote" e reutilizada
     * nos cards de "Conversão Alimentar", pra nunca haver dois critérios
     * diferentes na mesma tela. Binário de propósito (vinculada/não
     * vinculada): o nível de estoque da ração já tem seu próprio indicador
     * dedicado na página de Estoque de Ração — repeti-lo aqui só duplicava
     * informação sem ajudar a leitura rápida da tela do plano.
     */
    private function statusRacaoParaLote(?Racao $racao): array
    {
        if (!$racao) {
            return ['status-warning', 'Sem ração vinculada', 'fa-link-slash'];
        }

        return ['status-success', 'Ração vinculada', 'fa-circle-check'];
    }

    /**
     * Ração sugerida pra um lote sem vínculo explícito: só quando existe
     * exatamente uma ração compatível, ou mais de uma mas apenas uma bate
     * também a finalidade do lote (mesmo critério que a extinta
     * auto-vinculação da migration usava). Usada só pra PREENCHER a coluna
     * de consumo da tabela — nunca grava em lotes.racao_id nem muda o
     * status "Sem ração vinculada" (esse continua exigindo o vínculo
     * manual). Lote ambíguo (2+ rações igualmente compatíveis) ou sem
     * nenhuma continua sem número, porque não dá pra "advinhar" qual delas.
     */
    private function racaoSugeridaParaLote($lote, $racoes): ?Racao
    {
        $compativeis = $this->racoesCompativeisComLote($lote, $racoes);

        if ($compativeis->count() === 1) {
            return $compativeis->first();
        }

        if ($compativeis->count() > 1) {
            return $compativeis->firstWhere('finalidade', $lote->finalidade);
        }

        return null;
    }

    /**
     * Monta o card "Consumo por Lote - Plano Diário" direto da ração
     * vinculada a cada lote. O consumo diário (kg/dia, sempre um número
     * inteiro no banco) é dividido em 3 porções com aritmética inteira —
     * cada porção recebe 1kg extra de "sobra" antes das demais, garantindo
     * que manhã + tarde + noite bata exatamente com o total, sem qualquer
     * erro de arredondamento de ponto flutuante.
     */
    private function montarLotesDashboardAutomatico($lotesDados, $racoes, $racaoId = null)
    {
        $racoesFiltradas = $racaoId
            ? $racoes->where('id', (int) $racaoId)->values()
            : $racoes;

        $racaoPorLote = $this->mapaRacaoPorLote();

        return $lotesDados->map(function ($lote) use ($racoesFiltradas, $racoes, $racaoPorLote) {
            $racao = $this->racaoVinculadaAoLote($lote, $racoesFiltradas);
            $quantidadeAnimais = (int) ($lote->quantidade_animais ?? 0);

            if (!$racao) {
                // Sem vínculo manual — a coluna de consumo ainda mostra a
                // estimativa da ração compatível (quando não é ambígua),
                // só pra tabela não ficar zerada; o status/badge continua
                // exigindo o vínculo explícito (ver statusRacaoParaLote()).
                $racaoSugerida = $this->racaoSugeridaParaLote($lote, $racoesFiltradas);

                if ($racaoSugerida) {
                    $consumoDia = (int) $racaoSugerida->consumo;
                    $base = intdiv($consumoDia, 3);
                    $resto = $consumoDia % 3;
                    $porcaoManha = $base + ($resto >= 1 ? 1 : 0);
                    $porcaoTarde = $base + ($resto >= 2 ? 1 : 0);
                    $porcaoNoite = $base;
                    $totalDia = $porcaoManha + $porcaoTarde + $porcaoNoite;
                } else {
                    $porcaoManha = $porcaoTarde = $porcaoNoite = $totalDia = 0;
                }

                return (object) [
                    'lote_id' => $lote->id,
                    'nome_lote' => $lote->nome_lote,
                    'tipo_animal' => $lote->tipo_animal ?? '—',
                    'quantidade_animais' => $quantidadeAnimais,
                    'racao_id' => null,
                    'hora_manha' => $racaoSugerida ? '08:00' : '—', 'peso_manha' => $porcaoManha,
                    'hora_tarde' => $racaoSugerida ? '14:00' : '—', 'peso_tarde' => $porcaoTarde,
                    'hora_noite' => $racaoSugerida ? '20:00' : '—', 'peso_noite' => $porcaoNoite,
                    'total_dia' => $totalDia,
                    'tem_racao' => false,
                    'status_class' => 'status-warning',
                    'status_texto' => 'Sem ração vinculada',
                    'icon' => 'fa-link-slash',
                    'detalhes' => $this->montarDetalhesLote($lote, null, $racoes, $quantidadeAnimais, null, $racaoPorLote),
                ];
            }

            $consumoDia = (int) $racao->consumo;
            $base = intdiv($consumoDia, 3);
            $resto = $consumoDia % 3;
            $porcaoManha = $base + ($resto >= 1 ? 1 : 0);
            $porcaoTarde = $base + ($resto >= 2 ? 1 : 0);
            $porcaoNoite = $base;
            $totalDia = $porcaoManha + $porcaoTarde + $porcaoNoite;

            [$statusClass, $statusTexto, $icon] = $this->statusRacaoParaLote($racao);

            return (object) [
                'lote_id' => $lote->id,
                'nome_lote' => $lote->nome_lote,
                'tipo_animal' => $lote->tipo_animal ?? '—',
                'quantidade_animais' => $quantidadeAnimais,
                'racao_id' => $racao->id,
                'hora_manha' => '08:00', 'peso_manha' => $porcaoManha,
                'hora_tarde' => '14:00', 'peso_tarde' => $porcaoTarde,
                'hora_noite' => '20:00', 'peso_noite' => $porcaoNoite,
                'total_dia' => $totalDia,
                'tem_racao' => true,
                'status_class' => $statusClass,
                'status_texto' => $statusTexto,
                'icon' => $icon,
                'detalhes' => $this->montarDetalhesLote($lote, $racao, $racoes, $quantidadeAnimais, [
                    'manha' => $porcaoManha, 'tarde' => $porcaoTarde, 'noite' => $porcaoNoite, 'total' => $totalDia,
                ], $racaoPorLote),
            ];
        });
    }

    /**
     * Payload dinâmico embutido em cada linha da tabela (data-detalhes,
     * ver cadastroplano.blade.php) e lido pelo JS ao abrir o modal de
     * detalhes/vincular ração — nada fixo, tudo calculado por lote.
     */
    private function montarDetalhesLote($lote, $racao, $racoes, $quantidadeAnimais, ?array $porcoes = null, ?Collection $racaoPorLote = null)
    {
        // Rações já vinculadas a OUTRO lote não entram na lista de opções
        // pra vincular — cada ração só deve alimentar o cálculo de
        // consumo/estoque de um lote por vez. A ração já vinculada a ESTE
        // lote continua na lista (pra aparecer pré-selecionada no <select>).
        $racaoPorLote ??= $this->mapaRacaoPorLote();
        $idsEmUsoPorOutros = $racaoPorLote->except($lote->id)->values();

        return [
            'lote_id' => $lote->id,
            'nome_lote' => $lote->nome_lote,
            'tipo_animal' => $lote->tipo_animal,
            'sexo' => $lote->sexo,
            'finalidade' => $lote->finalidade,
            'quantidade_animais' => $quantidadeAnimais,
            'vinculada' => (bool) $racao,
            'racao' => $racao ? [
                'id' => $racao->id,
                'nome_racao' => $racao->nome_racao,
                'fornecedor' => $racao->fornecedor,
                'valor_kg' => $racao->valor_kg,
                'consumo_total_dia' => $porcoes['total'],
                'consumo_por_animal' => $quantidadeAnimais > 0 ? round($porcoes['total'] / $quantidadeAnimais, 2) : null,
                'distribuicao' => [
                    ['periodo' => 'Manhã', 'horario' => '08:00', 'quantidade' => $porcoes['manha']],
                    ['periodo' => 'Tarde', 'horario' => '14:00', 'quantidade' => $porcoes['tarde']],
                    ['periodo' => 'Noite', 'horario' => '20:00', 'quantidade' => $porcoes['noite']],
                ],
                'estoque_disponivel' => (int) $racao->estoque,
                'dias_restantes_estoque' => $racao->diasRestantesEstoque(),
                'status_estoque' => $racao->statusEstoque(),
            ] : null,
            'racoes_compativeis' => $this->racoesCompativeisComLote($lote, $racoes)
                ->reject(fn ($r) => $idsEmUsoPorOutros->contains($r->id))
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'nome_racao' => $r->nome_racao,
                    'estoque' => (int) $r->estoque,
                    'consumo' => (int) $r->consumo,
                ])->values(),
        ];
    }

    /**
     * Card "Conversão Alimentar por Lote": peso médio atual, quantidade de
     * animais, e o consumo diário (kg/dia) cadastrado na ração vinculada ao
     * lote — mais a variação de "Consumo por Animal"/"Consumo por kg Vivo"
     * no período escolhido vs o período anterior de mesma duração, a partir
     * do histórico real gravado em lote_consumo_snapshots. Sem base de
     * comparação ainda (poucos dias de histórico ou nada mudou), a variação
     * vem null — nunca um valor inventado. $diasPeriodo é o mesmo filtro de
     * 7/30/90 dias usado pelo Comparativo/Evolução da seção.
     *
     * Também traz a Conversão Alimentar REAL (kg de ração / kg de peso
     * ganho) da última pesagem registrada em DesempenhoLote — null quando o
     * lote ainda não tem pesagem suficiente pra calcular um ganho.
     */
    private function montarConversaoAlimentarAutomatica($lotesDados, $racoes, $racaoId = null, int $diasPeriodo = 7)
    {
        $racoesFiltradas = $racaoId
            ? $racoes->where('id', (int) $racaoId)->values()
            : $racoes;

        $loteIds = $lotesDados->pluck('id');
        $snapshotsAtual = $this->snapshotsFiltrados($loteIds, $racaoId, today()->subDays($diasPeriodo - 1), today())->groupBy('lote_id');
        $snapshotsAnterior = $this->snapshotsFiltrados($loteIds, $racaoId, today()->subDays(($diasPeriodo * 2) - 1), today()->subDays($diasPeriodo))->groupBy('lote_id');

        // Última pesagem registrada por lote (App\Models\DesempenhoLote) —
        // só entra na conta quando ganho_peso_kg > 0 (ver
        // PlanoController::registrarPesagem: sem duas pesagens em sequência
        // positiva não existe FCR real pra mostrar).
        $ultimaPesagemPorLote = DesempenhoLote::whereIn('lote_id', $loteIds)
            ->orderByDesc('data_registro')
            ->get()
            ->groupBy('lote_id')
            ->map(fn ($grupo) => $grupo->first());

        // Média diária do período, já convertida pra "por animal"/"por kg
        // vivo" — usa a quantidade de animais/peso vivo de CADA dia
        // gravado no snapshot (não o valor atual do lote), pra a variação
        // refletir de verdade o que mudou dia a dia.
        $mediaPorAnimalPeriodo = function (?Collection $snapshotsDoLote) {
            if (!$snapshotsDoLote || $snapshotsDoLote->isEmpty()) {
                return null;
            }

            $porDia = $snapshotsDoLote->map(fn ($s) => $s->quantidade_animais > 0 ? $s->consumo_kg / $s->quantidade_animais : null)
                ->filter(fn ($v) => $v !== null);

            return $porDia->isNotEmpty() ? $porDia->avg() : null;
        };

        $pesoVivoAtualPorLote = $lotesDados->mapWithKeys(fn ($l) => [$l->id => (float) ($l->peso_medio ?? 0) * (int) ($l->quantidade_animais ?? 0)]);

        $mediaPorKgVivoPeriodo = function (?Collection $snapshotsDoLote, $loteId) use ($pesoVivoAtualPorLote) {
            $pesoVivo = $pesoVivoAtualPorLote[$loteId] ?? 0;
            if (!$snapshotsDoLote || $snapshotsDoLote->isEmpty() || $pesoVivo <= 0) {
                return null;
            }

            return round(((float) $snapshotsDoLote->avg('consumo_kg')) / $pesoVivo, 3);
        };

        return $lotesDados->map(function ($lote) use ($racoesFiltradas, $snapshotsAtual, $snapshotsAnterior, $mediaPorAnimalPeriodo, $mediaPorKgVivoPeriodo, $ultimaPesagemPorLote) {
            $racao = $this->racaoVinculadaAoLote($lote, $racoesFiltradas);
            $consumoDiario = $racao ? (int) $racao->consumo : 0;
            $quantidadeAnimais = (int) ($lote->quantidade_animais ?? 0);
            $pesoMedio = (float) ($lote->peso_medio ?? 0);
            $pesoVivoTotal = $pesoMedio * $quantidadeAnimais;

            $porAnimalAtual = $mediaPorAnimalPeriodo($snapshotsAtual->get($lote->id));
            $porAnimalAnterior = $mediaPorAnimalPeriodo($snapshotsAnterior->get($lote->id));
            $porKgVivoAtual = $mediaPorKgVivoPeriodo($snapshotsAtual->get($lote->id), $lote->id);
            $porKgVivoAnterior = $mediaPorKgVivoPeriodo($snapshotsAnterior->get($lote->id), $lote->id);

            [$statusClass, $statusTexto] = $this->statusRacaoParaLote($racao);

            $ultimaPesagem = $ultimaPesagemPorLote->get($lote->id);
            $temConversaoReal = $ultimaPesagem && $ultimaPesagem->eficiencia_alimentar > 0;

            return (object) [
                'id'                  => $lote->id,
                'nome_lote'           => $lote->nome_lote,
                'tipo_animal'         => $lote->tipo_animal ?? '—',
                'peso_medio'          => $pesoMedio,
                'quantidade_animais'  => $quantidadeAnimais,
                'consumo_diario'      => $consumoDiario,
                'consumo_por_animal'  => $quantidadeAnimais > 0 ? round($consumoDiario / $quantidadeAnimais, 2) : null,
                'consumo_por_kg_vivo' => $pesoVivoTotal > 0 ? round($consumoDiario / $pesoVivoTotal, 3) : null,
                'variacao_por_animal'  => $this->variacaoPercentual($porAnimalAtual, $porAnimalAnterior),
                'variacao_por_kg_vivo' => $this->variacaoPercentual($porKgVivoAtual, $porKgVivoAnterior),
                'status_class'        => $statusClass,
                'status_texto'        => $statusTexto,

                // Conversão Alimentar REAL (App\Models\DesempenhoLote).
                'tem_pesagem'          => (bool) $ultimaPesagem,
                'data_ultima_pesagem'  => $ultimaPesagem?->data_registro,
                'ganho_peso_kg'        => $ultimaPesagem?->ganho_peso_kg,
                'conversao_real'       => $temConversaoReal ? $ultimaPesagem->eficiencia_alimentar : null,
            ];
        });
    }

    /**
     * "Comparativo entre Lotes": melhor/pior Conversão Alimentar REAL do
     * momento (menor/maior kg de ração por kg ganho — quanto menor, melhor)
     * e a média entre os lotes que já têm pesagem suficiente. Sem nenhum
     * lote com conversão real ainda, volta tudo null — a tela pede pra
     * registrar a primeira pesagem em vez de inventar um número.
     */
    private function montarComparativoConversao(Collection $conversoes): array
    {
        $comConversaoReal = $conversoes->filter(fn ($c) => $c->conversao_real !== null);

        if ($comConversaoReal->isEmpty()) {
            return ['temDados' => false, 'melhor' => null, 'pior' => null, 'media' => null];
        }

        return [
            'temDados' => true,
            'melhor' => $comConversaoReal->sortBy('conversao_real')->first(),
            'pior' => $comConversaoReal->sortByDesc('conversao_real')->first(),
            'media' => round($comConversaoReal->avg('conversao_real'), 2),
        ];
    }

    /**
     * "Evolução da Conversão Alimentar": uma linha por lote (até 6, os com
     * mais pesagens registradas — mesmo limite/paleta do radar de
     * Relatórios), plotada por ORDEM de pesagem (1ª, 2ª, 3ª...) em vez de
     * data — lotes pesados em dias diferentes não têm um eixo de tempo em
     * comum, então comparar "a pesagem mais recente de cada um" é o que
     * realmente é comparável entre lotes.
     */
    private function montarEvolucaoConversao(Collection $lotesDados): array
    {
        $historico = DesempenhoLote::whereIn('lote_id', $lotesDados->pluck('id'))
            ->where('eficiencia_alimentar', '>', 0)
            ->orderBy('data_registro')
            ->get()
            ->groupBy('lote_id');

        if ($historico->isEmpty()) {
            return ['labels' => [], 'series' => [], 'temDados' => false];
        }

        $nomesPorLoteId = $lotesDados->pluck('nome_lote', 'id');
        $lotesComHistorico = $historico->sortByDesc(fn ($grupo) => $grupo->count())->take(6);
        $maxPontos = $lotesComHistorico->max(fn ($grupo) => $grupo->count());

        $labels = collect(range(1, $maxPontos))->map(fn ($n) => "Pesagem {$n}")->all();
        $series = $lotesComHistorico->map(fn ($grupo, $loteId) => [
            'label' => $nomesPorLoteId[$loteId] ?? "Lote #{$loteId}",
            'data' => $grupo->pluck('eficiencia_alimentar')->map(fn ($v) => round((float) $v, 2))->values()->all(),
        ])->values()->all();

        return ['labels' => $labels, 'series' => $series, 'temDados' => true];
    }

    /**
     * Grava (ou atualiza) o snapshot de HOJE de cada lote — chamado sempre
     * que a tela é aberta, com o conjunto COMPLETO (sem filtro de ração) de
     * lotes do usuário, senão um filtro ativo na tela corromperia o
     * histórico dos lotes que ficaram de fora dele. Visitas repetidas no
     * mesmo dia apenas atualizam a linha (updateOrCreate) — não duplicam,
     * e refletem o estado mais recente até a virada do dia "congelar" o
     * valor.
     */
    private function capturarSnapshotsDeHoje(Collection $lotesDashboardCompleto): void
    {
        $hoje = today()->toDateString();

        foreach ($lotesDashboardCompleto as $lote) {
            LoteConsumoSnapshot::updateOrCreate(
                ['lote_id' => $lote->lote_id, 'data' => $hoje],
                [
                    'racao_id' => $lote->racao_id,
                    'quantidade_animais' => $lote->quantidade_animais,
                    'consumo_kg' => $lote->total_dia,
                    'status_class' => $lote->status_class,
                ]
            );
        }
    }

    /** Snapshots dos lotes em escopo, num intervalo de datas, respeitando o filtro de Ração da tela. */
    private function snapshotsFiltrados(Collection $loteIds, $racaoId, Carbon $inicio, Carbon $fim): Collection
    {
        return LoteConsumoSnapshot::whereIn('lote_id', $loteIds)
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->when($racaoId, fn ($q) => $q->where('racao_id', (int) $racaoId))
            ->get();
    }

    /** Mesma semântica do CadastroController/RelatorioController::variacaoPercentual(). */
    private function variacaoPercentual(?float $atual, ?float $anterior): ?float
    {
        if ($atual === null || $anterior === null) {
            return null;
        }

        if ($anterior <= 0) {
            return $atual > 0 ? null : 0.0;
        }

        return round((($atual - $anterior) / $anterior) * 100, 1);
    }

    /**
     * Gera os dados do Dashboard e Gráficos: o card "Consumo por Lote" e a
     * cobertura de rações continuam "ao vivo" (refletem o vínculo AGORA),
     * mas "Últimos 7 Dias", "Resumo Mensal", os 5 cards do topo e a
     * distribuição por ração agora vêm do histórico real gravado em
     * lote_consumo_snapshots (ver capturarSnapshotsDeHoje) — dias
     * anteriores à existência desse histórico aparecem com 0 kg, não um
     * valor inventado.
     */
    private function getDashboardData($lotesDados, $racoes, $racaoId = null, $mesFiltro = null)
    {
        $lotesDashboard = $this->montarLotesDashboardAutomatico($lotesDados, $racoes, $racaoId);
        $loteIds = $lotesDados->pluck('id');

        $lotesComRacao = $lotesDashboard->where('tem_racao', true)->count();
        $lotesSemRacao = $lotesDashboard->count() - $lotesComRacao;
        $coberturaPercentual = $lotesDashboard->count() > 0
            ? (int) round($lotesComRacao / $lotesDashboard->count() * 100)
            : null;
        $loteMaiorConsumo = $lotesDashboard->where('tem_racao', true)->sortByDesc('total_dia')->first();
        $totalDiario = round((float) $lotesDashboard->sum('total_dia'), 1);
        $custoDiario = round($totalDiario * self::PRECO_MEDIO_KG, 2);

        // Janela usada pelo gráfico "Últimos 7 Dias".
        $snapshots7Dias = $this->snapshotsFiltrados($loteIds, $racaoId, today()->subDays(6), today());
        $ultimos7Dias = collect(range(6, 0))->map(function ($i) use ($snapshots7Dias) {
            $dia = today()->subDays($i);
            $doDia = $snapshots7Dias->filter(fn ($s) => $s->data->isSameDay($dia));
            $consumo = (int) $doDia->sum('consumo_kg');
            $animais = (int) $doDia->sum('quantidade_animais');
            $comRegistro = $doDia->count();
            $saudaveis = $doDia->where('status_class', 'status-success')->count();

            return (object) [
                'label' => $dia->isToday()
                    ? 'Hoje ' . $dia->format('d')
                    : ucfirst($dia->locale('pt_BR')->isoFormat('ddd')) . ' ' . $dia->format('d'),
                'consumo' => $consumo,
                'custo' => round($consumo * self::PRECO_MEDIO_KG, 2),
                'por_animal' => $animais > 0 ? round($consumo / $animais, 3) : 0,
                'eficiencia' => $comRegistro > 0 ? (int) round($saudaveis / $comRegistro * 100) : 0,
            ];
        });

        $labels7Dias = $ultimos7Dias->pluck('label')->values();
        $valores7Dias = $ultimos7Dias->pluck('consumo')->values();
        $totalSemana = number_format($valores7Dias->sum(), 0, ',', '.');

        // "Resumo Mensal": mês escolhido no seletor (ou o corrente).
        $referenciaMes = null;
        if ($mesFiltro) {
            try {
                $referenciaMes = Carbon::createFromFormat('Y-m', $mesFiltro)->startOfMonth();
            } catch (\Exception $e) {
                $referenciaMes = null;
            }
        }
        $referenciaMes = $referenciaMes ?? now();
        $mesAnoAtual = $referenciaMes->locale('pt_BR')->translatedFormat('F/Y'); // Ex: "Julho/2026"

        $inicioMes = $referenciaMes->copy()->startOfMonth();
        $fimMes = $referenciaMes->copy()->endOfMonth();

        $snapshotsMes = $this->snapshotsFiltrados($loteIds, $racaoId, $inicioMes, $fimMes);

        $diasNoMes = $inicioMes->daysInMonth;

        $totalRacaoMesNum = (float) $snapshotsMes->sum('consumo_kg');
        $custoTotalMesNum = round($totalRacaoMesNum * self::PRECO_MEDIO_KG, 2);
        $mediaDiariaMesNum = round($totalRacaoMesNum / $diasNoMes, 1);

        // Maior/menor consumo do mês: dias reais a partir dos snapshots —
        // não mais um valor "constante" repetido.
        $porDiaMes = $snapshotsMes->groupBy(fn ($s) => $s->data->format('Y-m-d'))
            ->map(fn ($g) => (float) $g->sum('consumo_kg'));

        if ($porDiaMes->isNotEmpty()) {
            $diaMaior = $porDiaMes->sortDesc()->keys()->first();
            $diaMenor = $porDiaMes->sort()->keys()->first();
            $maiorConsumo = 'Dia ' . Carbon::parse($diaMaior)->format('d') . ' (' . number_format($porDiaMes[$diaMaior], 0, ',', '.') . ' kg)';
            $menorConsumo = 'Dia ' . Carbon::parse($diaMenor)->format('d') . ' (' . number_format($porDiaMes[$diaMenor], 0, ',', '.') . ' kg)';
        } else {
            $maiorConsumo = 'Sem histórico ainda';
            $menorConsumo = 'Sem histórico ainda';
        }

        // Semanas do mês, pro gráfico de linha do "Resumo Mensal".
        $labelsMensal = [];
        $valoresMensal = [];
        $semanasNoMes = (int) ceil($diasNoMes / 7);
        for ($i = 0; $i < $semanasNoMes; $i++) {
            $inicioSemana = $inicioMes->copy()->addDays($i * 7);
            $fimSemana = $inicioSemana->copy()->addDays(6);
            if ($fimSemana->gt($fimMes)) {
                $fimSemana = $fimMes->copy();
            }

            $totalSemanaMes = 0.0;
            foreach ($porDiaMes as $data => $kg) {
                if (Carbon::parse($data)->between($inicioSemana, $fimSemana)) {
                    $totalSemanaMes += $kg;
                }
            }

            $labelsMensal[] = 'Semana ' . ($i + 1);
            $valoresMensal[] = round($totalSemanaMes, 1);
        }

        // Distribuição de ração no mês selecionado.
        $racaoNomesPorId = $racoes->pluck('nome_racao', 'id');
        $distribuicaoPorRacaoId = $snapshotsMes->whereNotNull('racao_id')->groupBy('racao_id')
            ->map(fn ($g) => (float) $g->sum('consumo_kg'))
            ->sortDesc();
        $distribuicaoRacoes = $distribuicaoPorRacaoId->isNotEmpty()
            ? $distribuicaoPorRacaoId->map(fn ($kg, $idRacao) => [
                'nome' => $racaoNomesPorId[$idRacao] ?? 'Ração removida',
                'kg' => round($kg, 1),
                'percentual' => round($kg / $totalRacaoMesNum * 100, 1),
            ])->values()
            : collect();

        return [
            'lotesDashboard'  => $lotesDashboard,
            'ultimos7Dias'    => $ultimos7Dias,
            'labels7Dias'     => json_encode($labels7Dias),
            'valores7Dias'    => json_encode($valores7Dias),
            'totalSemana'     => $totalSemana,

            'mesReferencia'   => $referenciaMes->format('Y-m'),
            'mesAnoAtual'     => ucfirst($mesAnoAtual),
            'totalRacaoMes'   => number_format($totalRacaoMesNum, 0, ',', '.'),
            'custoTotalMes'   => number_format($custoTotalMesNum, 2, ',', '.'),
            'mediaDiariaMes'  => number_format($mediaDiariaMesNum, 0, ',', '.'),
            'maiorConsumo'    => $maiorConsumo,
            'menorConsumo'    => $menorConsumo,
            'labelsMensal'    => json_encode($labelsMensal),
            'valoresMensal'   => json_encode($valoresMensal),
            'temHistoricoMes' => $porDiaMes->isNotEmpty(),

            'totalDiario'         => $totalDiario,
            'custoDiario'         => $custoDiario,
            'lotesComRacao'       => $lotesComRacao,
            'lotesSemRacao'       => $lotesSemRacao,
            'coberturaPercentual' => $coberturaPercentual,
            'loteMaiorConsumo'    => $loteMaiorConsumo,

            'distribuicaoRacoes' => $distribuicaoRacoes,
        ];
    }

    // Mantém só os primeiros N lotes no card "Consumo por Lote - Plano
    // Diário" e guarda o total real para o botão "Consultar Lotes" saber
    // se ainda há mais lotes a carregar.
    private function limitarLotesDashboard(array $dash)
    {
        $dash['lotesDashboardTotal'] = $dash['lotesDashboard']->count();
        $dash['lotesDashboard'] = $dash['lotesDashboard']
            ->take(self::LOTES_DASHBOARD_INICIAL)
            ->values();

        return $dash;
    }

    /**
     * Traduz os filtros ativos (Fazenda/Lote/Ração) em rótulos legíveis
     * para os "chips" do modal de Filtros Avançados — mesmo padrão do
     * RacaoController/EstoqueController/CochoController.
     */
    private function descreverFiltros(array $filtros, $fazendas, $lotes, $racaos): array
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

        if (!empty($filtros['racao_id'])) {
            $nome = optional($racaos->firstWhere('id', (int) $filtros['racao_id']))->nome_racao;
            $itens[] = ['chave' => 'racao_id', 'label' => 'Ração: ' . ($nome ?? '—')];
        }

        return $itens;
    }

    // listar() e cadastrar() renderizam exatamente a mesma tela. Os
    // filtros (Fazenda/Lote/Ração) escopam só o que o dashboard mostra —
    // o modal de Filtros Avançados sempre lista TODOS os lotes/rações do
    // usuário (via $todosLotes/$racaos), senão o filtro se
    // auto-restringiria.
    private function renderizarTela(Request $request)
    {
        $filtros = $request->only(['fazenda_id', 'lote_id', 'racao_id']);

        $todosLotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $racaos = $this->racoesDoUsuario()->with('fazenda')->orderBy('nome_racao')->get();
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        // Captura o snapshot de hoje com o conjunto COMPLETO de lotes (sem
        // nenhum filtro da tela aplicado) — precisa rodar antes de filtrar
        // $lotes, senão um filtro de Ração ativo corromperia o histórico
        // dos lotes que ficaram de fora dele.
        $this->capturarSnapshotsDeHoje($this->montarLotesDashboardAutomatico($todosLotes, $racaos));

        $lotes = $todosLotes
            ->when(!empty($filtros['fazenda_id']), fn ($c) => $c->where('fazenda_id', (int) $filtros['fazenda_id']))
            ->when(!empty($filtros['lote_id']), fn ($c) => $c->where('id', (int) $filtros['lote_id']))
            ->values();

        $racaoIdFiltro = $filtros['racao_id'] ?? null;
        $mesFiltro = $request->query('mes');

        $dash = $this->limitarLotesDashboard($this->getDashboardData($lotes, $racaos, $racaoIdFiltro, $mesFiltro));
        $filtrosAplicados = $this->descreverFiltros($filtros, $fazendas, $todosLotes, $racaos);

        // Últimos 12 meses (incluindo o atual) pro seletor do card "Resumo
        // Mensal" — antes o <select> só tinha uma opção fixa (o mês atual)
        // e trocar não fazia nada.
        $mesesDisponiveis = collect(range(0, 11))->map(function ($i) {
            $data = now()->subMonths($i);

            return ['valor' => $data->format('Y-m'), 'label' => ucfirst($data->locale('pt_BR')->translatedFormat('F/Y'))];
        });
        $mesSelecionado = $dash['mesReferencia'] ?? now()->format('Y-m');

        // Período da seção de Conversão Alimentar (7/30/90 dias) — só essa
        // seção usa esse filtro; o resto da tela (Consumo por Lote, Resumo
        // Mensal) continua com seus próprios períodos fixos.
        $periodoConversao = in_array((int) $request->query('periodo_conversao'), [7, 30, 90], true)
            ? (int) $request->query('periodo_conversao')
            : 7;

        $conversoesAlimentares = $this->montarConversaoAlimentarAutomatica($lotes, $racaos, $racaoIdFiltro, $periodoConversao);
        $conversoesAlimentaresTotal = $conversoesAlimentares->count();
        $conversoesAlimentaresExibidas = $conversoesAlimentares->take(self::CONVERSOES_INICIAL)->values();
        $comparativoConversao = $this->montarComparativoConversao($conversoesAlimentares);
        $evolucaoConversao = $this->montarEvolucaoConversao($lotes);
        $statCardsTopo = $this->montarStatCardsTopo($dash, $lotes->count());

        return view('cadastroplano', array_merge(
            compact(
                'lotes',
                'todosLotes',
                'racaos',
                'fazendas',
                'cadastroLogado',
                'filtros',
                'filtrosAplicados',
                'conversoesAlimentaresExibidas',
                'conversoesAlimentaresTotal',
                'comparativoConversao',
                'evolucaoConversao',
                'periodoConversao',
                'statCardsTopo',
                'mesesDisponiveis',
                'mesSelecionado'
            ),
            $dash
        ));
    }

    /**
     * Faixa de indicadores no topo da tela: consumo/custo/eficiência de HOJE
     * (histórico real de lote_consumo_snapshots, com variação vs ontem) e o
     * total de lotes em escopo — mesmo componente .stat-card do Relatório
     * Avançado, pra dar uma visão geral antes de descer pras tabelas/cards.
     */
    private function montarStatCardsTopo(array $dash, int $totalLotes): array
    {
        $ultimos7Dias = $dash['ultimos7Dias'] ?? collect();
        $hoje = $ultimos7Dias->last();
        $ontem = $ultimos7Dias->count() > 1 ? $ultimos7Dias->get($ultimos7Dias->count() - 2) : null;

        return [
            'consumoHoje' => [
                'valor' => $hoje->consumo ?? 0,
                'variacao' => $ontem ? $this->variacaoPercentual((float) ($hoje->consumo ?? 0), (float) $ontem->consumo) : null,
            ],
            'custoHoje' => [
                'valor' => $hoje->custo ?? 0,
                'variacao' => $ontem ? $this->variacaoPercentual((float) ($hoje->custo ?? 0), (float) $ontem->custo) : null,
            ],
            'lotesAtivos' => [
                'valor' => $totalLotes,
                'comRacao' => $dash['lotesComRacao'] ?? 0,
            ],
            'eficienciaMedia' => [
                'valor' => $hoje->eficiencia ?? 0,
                'variacao' => $ontem ? $this->variacaoPercentual((float) ($hoje->eficiencia ?? 0), (float) $ontem->eficiencia) : null,
            ],
        ];
    }

    // LISTAR
    public function listar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        return $this->renderizarTela($request);
    }

    // TELA CADASTRAR
    public function cadastrar(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        return $this->renderizarTela($request);
    }

    // CONSULTAR LOTES: retorna em JSON (HTML já renderizado) os lotes do
    // card "Consumo por Lote - Plano Diário" que ficaram de fora da carga
    // inicial — respeitando os mesmos filtros (Fazenda/Lote/Ração) da
    // página.
    public function lotesConsumoJson(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $filtros = $request->only(['fazenda_id', 'lote_id', 'racao_id']);

        $lotes = $this->lotesDoUsuario()
            ->when(!empty($filtros['fazenda_id']), fn ($q) => $q->where('fazenda_id', $filtros['fazenda_id']))
            ->when(!empty($filtros['lote_id']), fn ($q) => $q->where('id', $filtros['lote_id']))
            ->get();

        $racoes = $this->racoesDoUsuario()->get();
        $lotesDashboard = $this->montarLotesDashboardAutomatico($lotes, $racoes, $filtros['racao_id'] ?? null);

        $restante = $lotesDashboard
            ->slice(self::LOTES_DASHBOARD_INICIAL)
            ->values();

        return response()->json([
            'success' => true,
            'html'    => view('partials.lote-consumo-linhas', ['lotesDashboard' => $restante])->render(),
        ]);
    }

    // CONSULTAR MAIS CONVERSÕES: retorna em JSON (HTML já renderizado) os
    // cards de "Conversão Alimentar por Lote" que ficaram de fora da carga
    // inicial — respeitando os mesmos filtros (Fazenda/Lote/Ração).
    public function conversoesAlimentaresJson(Request $request)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $filtros = $request->only(['fazenda_id', 'lote_id', 'racao_id']);

        $lotes = $this->lotesDoUsuario()
            ->when(!empty($filtros['fazenda_id']), fn ($q) => $q->where('fazenda_id', $filtros['fazenda_id']))
            ->when(!empty($filtros['lote_id']), fn ($q) => $q->where('id', $filtros['lote_id']))
            ->get();

        $racoes = $this->racoesDoUsuario()->get();
        $periodoConversao = in_array((int) $request->query('periodo_conversao'), [7, 30, 90], true)
            ? (int) $request->query('periodo_conversao')
            : 7;
        $conversoes = $this->montarConversaoAlimentarAutomatica($lotes, $racoes, $filtros['racao_id'] ?? null, $periodoConversao);

        $restante = $conversoes->slice(self::CONVERSOES_INICIAL)->values();

        return response()->json([
            'success' => true,
            'html'    => view('partials.conversao-alimentar-cards', ['conversoes' => $restante])->render(),
        ]);
    }

    // VINCULAR RAÇÃO: liga (ou troca, ou remove) o vínculo explícito entre
    // um lote e uma ração, a partir do modal de detalhes da tela de Plano
    // Alimentar. Um único form serve os três casos — vincular, trocar e
    // "— Nenhuma —" (remover o vínculo).
    public function vincularRacao(Request $request, $loteId)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $lote = $this->lotesDoUsuario()->findOrFail($loteId);
        $racoes = $this->racoesDoUsuario()->get();
        $compativeis = $this->racoesCompativeisComLote($lote, $racoes);

        $racaoId = $request->input('racao_id');

        if ($racaoId !== null && $racaoId !== '' && !$compativeis->pluck('id')->contains((int) $racaoId)) {
            return back()
                ->withErrors(['racao_id' => 'Selecione uma ração compatível com este lote (mesma fazenda, tipo e sexo).'])
                ->withInput();
        }

        // Mesma regra aplicada na lista de opções do modal (ver
        // montarDetalhesLote): uma ração já vinculada a OUTRO lote não pode
        // ser escolhida de novo aqui — nunca confia só no que o JS deixou
        // de mostrar, valida de novo no servidor.
        if ($racaoId !== null && $racaoId !== '') {
            $jaVinculadaAOutroLote = $this->mapaRacaoPorLote()
                ->except($lote->id)
                ->contains((int) $racaoId);

            if ($jaVinculadaAOutroLote) {
                return back()
                    ->withErrors(['racao_id' => 'Esta ração já está vinculada a outro lote.'])
                    ->withInput();
            }
        }

        $lote->update(['racao_id' => $racaoId ?: null]);

        return back()->with('success', $racaoId
            ? 'Ração vinculada ao lote com sucesso!'
            : 'Vínculo de ração removido do lote.');
    }

    // REGISTRAR PESAGEM: checkpoint de peso médio do lote, usado pra
    // calcular a Conversão Alimentar REAL (kg de ração / kg de peso ganho)
    // desde a pesagem anterior. Reaproveita a tabela "desempenho_lotes"
    // (ver App\Models\DesempenhoLote) — sem pesagem anterior registrada,
    // esta primeira só grava a baseline (ganho/conversão ficam zerados: sem
    // duas pesagens não existe "ganho" real pra medir, e a tela nunca
    // inventa um número pra isso).
    public function registrarPesagem(Request $request, $loteId)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $lote = $this->lotesDoUsuario()->findOrFail($loteId);

        $validado = $request->validate([
            'peso_medio_kg' => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'data' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $dataRegistro = Carbon::parse($validado['data'])->startOfDay();

        $ultimaPesagem = DesempenhoLote::where('lote_id', $lote->id)
            ->orderByDesc('data_registro')
            ->first();

        // A conversão é calculada como um delta entre duas pesagens em
        // ordem — uma data anterior à última já registrada corromperia essa
        // sequência, então o formulário não aceita "voltar no tempo".
        if ($ultimaPesagem && $dataRegistro->lte($ultimaPesagem->data_registro)) {
            return back()
                ->withErrors(['data' => 'A data precisa ser posterior à última pesagem registrada (' . $ultimaPesagem->data_registro->format('d/m/Y') . ').'])
                ->withInput();
        }

        $pesoNovo = (float) $validado['peso_medio_kg'];
        $quantidadeAnimais = (int) ($lote->quantidade_animais ?? 0);

        $ganhoPesoKg = 0.0;
        $consumoKg = 0.0;
        $custoRs = 0.0;
        $eficienciaAlimentar = 0.0;

        if ($ultimaPesagem) {
            $pesoAnterior = (float) ($lote->peso_medio ?? 0);

            if ($pesoAnterior > 0 && $quantidadeAnimais > 0) {
                $ganhoPesoKg = round(($pesoNovo - $pesoAnterior) * $quantidadeAnimais, 2);
            }

            $consumoKg = (float) LoteConsumoSnapshot::where('lote_id', $lote->id)
                ->whereBetween('data', [$ultimaPesagem->data_registro->toDateString(), $dataRegistro->toDateString()])
                ->sum('consumo_kg');
            $custoRs = round($consumoKg * self::PRECO_MEDIO_KG, 2);

            // FCR (kg ração / kg ganho) só faz sentido com ganho positivo —
            // perda de peso ou estagnação ficam visíveis pelo próprio
            // ganho_peso_kg (podendo ser zero/negativo), sem uma "conversão"
            // fabricada em cima disso.
            if ($ganhoPesoKg > 0) {
                $eficienciaAlimentar = round($consumoKg / $ganhoPesoKg, 2);
            }
        }

        DesempenhoLote::updateOrCreate(
            ['lote_id' => $lote->id, 'data_registro' => $dataRegistro->toDateString()],
            [
                'consumo_kg' => $consumoKg,
                'custo_rs' => $custoRs,
                'ganho_peso_kg' => $ganhoPesoKg,
                'eficiencia_alimentar' => $eficienciaAlimentar,
            ]
        );

        $lote->update(['peso_medio' => $pesoNovo]);

        return back()->with('success', $ultimaPesagem
            ? 'Pesagem registrada! Conversão alimentar do período atualizada.'
            : 'Primeira pesagem registrada — a conversão alimentar aparece a partir da próxima.');
    }
}
