<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolveFazendaSelecionada;
use Illuminate\Http\Request;
use App\Models\Cadastro;
use App\Models\DesempenhoLote;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use App\Models\Plano;
use App\Models\Racao;
use App\Services\ConsumoLoteService;
use App\Support\CompatibilidadeRacao;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlanoController extends Controller
{
    /** Períodos (dias) da Conversão Alimentar — os mesmos chips na web e no app. */
    public const PERIODOS_CONVERSAO = [7, 15, 30, 60, 90];

    use ResolveFazendaSelecionada;

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

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).

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
        // Regra única (também usada pela API e pelas validações): mesma
        // fazenda, ração ativa, mesmo tipo de animal, sexo e finalidade
        // compatíveis — ver App\Support\CompatibilidadeRacao.
        return CompatibilidadeRacao::filtrar($lote, collect($racoes));
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

    /** Plano ATIVO de cada lote (planos.ativo), por lote_id. */
    private function planosAtivosPorLote(Collection $loteIds): Collection
    {
        return Plano::whereIn('lote_id', $loteIds)->where('ativo', true)->latest('data_inicio')->get()
            ->unique('lote_id')->keyBy('lote_id');
    }

    /**
     * Consumo diário PLANEJADO de um lote — a MESMA regra da API usada pelo
     * app (Plano::consumoDiarioPrevisto): kg por animal por dia do plano
     * ativo × animais do lote hoje. Antes a web usava sempre o número fixo
     * "consumo (kg/dia)" da ração, que ignora quantos animais o lote tem
     * (o app e a web mostravam consumos diferentes para o mesmo lote).
     * Lote vinculado sem plano por animal (vínculos antigos) cai no valor da
     * ração, sinalizado em 'fonte' para a tela explicar.
     *
     * @return array{total: float, por_animal: ?float, fonte: string}
     */
    private function consumoPlanejadoDoLote($lote, Racao $racao, ?Plano $plano): array
    {
        $animais = (int) ($lote->quantidade_animais ?? 0);

        if ($plano && (int) $plano->racao_id === (int) $racao->id && $plano->quantidade_recomendada > 0) {
            return [
                'total' => round($plano->quantidade_recomendada * $animais, 1),
                'por_animal' => (float) $plano->quantidade_recomendada,
                'fonte' => 'por_animal',
            ];
        }

        $total = round((float) $racao->consumo, 1);

        return ['total' => $total, 'por_animal' => $animais > 0 ? round($total / $animais, 2) : null, 'fonte' => 'racao'];
    }

    /** Divide o total do dia em manhã/tarde/noite que somam exatamente o total. */
    private function dividirEmTratos(float $total): array
    {
        $manha = round($total / 3, 1);
        $tarde = round($total / 3, 1);

        return [$manha, $tarde, round($total - $manha - $tarde, 1)];
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

        $planosAtivos = $this->planosAtivosPorLote($lotesDados->pluck('id'));

        return $lotesDados->map(function ($lote) use ($racoesFiltradas, $racoes, $planosAtivos) {
            $racao = $this->racaoVinculadaAoLote($lote, $racoesFiltradas);
            $quantidadeAnimais = (int) ($lote->quantidade_animais ?? 0);

            if (!$racao) {
                // Sem vínculo manual — a coluna de consumo ainda mostra a
                // estimativa da ração compatível (quando não é ambígua),
                // só pra tabela não ficar zerada; o status/badge continua
                // exigindo o vínculo explícito (ver statusRacaoParaLote()).
                // Essa sugestão NUNCA entra no histórico de consumo.
                $racaoSugerida = $this->racaoSugeridaParaLote($lote, $racoesFiltradas);

                if ($racaoSugerida) {
                    $totalDia = round((float) $racaoSugerida->consumo, 1);
                    [$porcaoManha, $porcaoTarde, $porcaoNoite] = $this->dividirEmTratos($totalDia);
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
                    'detalhes' => $this->montarDetalhesLote($lote, null, $racoes, $quantidadeAnimais, null),
                ];
            }

            $planejado = $this->consumoPlanejadoDoLote($lote, $racao, $planosAtivos->get($lote->id));
            $totalDia = $planejado['total'];
            [$porcaoManha, $porcaoTarde, $porcaoNoite] = $this->dividirEmTratos($totalDia);

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
                    'por_animal' => $planejado['por_animal'], 'fonte' => $planejado['fonte'],
                ]),
            ];
        });
    }

    /**
     * Payload dinâmico embutido em cada linha da tabela (data-detalhes,
     * ver cadastroplano.blade.php) e lido pelo JS ao abrir o modal de
     * detalhes/vincular ração — nada fixo, tudo calculado por lote.
     */
    private function montarDetalhesLote($lote, $racao, $racoes, $quantidadeAnimais, ?array $porcoes = null)
    {

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
                'valor_kg' => $racao->valor_kg,
                'consumo_total_dia' => $porcoes['total'],
                'consumo_por_animal' => $porcoes['por_animal'] ?? ($quantidadeAnimais > 0 ? round($porcoes['total'] / $quantidadeAnimais, 2) : null),
                // 'por_animal' = kg/animal/dia do plano × animais (regra do app);
                // 'racao' = vínculo antigo, sem quantidade por animal definida.
                'fonte_consumo' => $porcoes['fonte'] ?? 'racao',
                'distribuicao' => [
                    ['periodo' => 'Manhã', 'horario' => '08:00', 'quantidade' => $porcoes['manha']],
                    ['periodo' => 'Tarde', 'horario' => '14:00', 'quantidade' => $porcoes['tarde']],
                    ['periodo' => 'Noite', 'horario' => '20:00', 'quantidade' => $porcoes['noite']],
                ],
                'estoque_disponivel' => (int) $racao->estoque,
                'dias_restantes_estoque' => $racao->diasRestantesEstoque(),
                'status_estoque' => $racao->statusEstoque(),
            ] : null,
            // Todas as compatíveis (a mesma ração pode alimentar vários lotes).
            'racoes_compativeis' => $this->racoesCompativeisComLote($lote, $racoes)
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

        $planosAtivos = $this->planosAtivosPorLote($loteIds);

        return $lotesDados->map(function ($lote) use ($racoesFiltradas, $snapshotsAtual, $snapshotsAnterior, $mediaPorAnimalPeriodo, $mediaPorKgVivoPeriodo, $ultimaPesagemPorLote, $planosAtivos) {
            $racao = $this->racaoVinculadaAoLote($lote, $racoesFiltradas);
            $consumoDiario = $racao ? $this->consumoPlanejadoDoLote($lote, $racao, $planosAtivos->get($lote->id))['total'] : 0;
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
                'racao_fornecida_kg'   => $ultimaPesagem?->consumo_kg,
                'conversao_real'       => $temConversaoReal ? $ultimaPesagem->eficiencia_alimentar : null,
                // De onde veio o consumo dessa conversão: 'sensor' (medido —
                // a única "real" de fato), 'fornecido' (ração colocada nos
                // cochos) ou 'plano' (estimativa). Ver ConsumoLoteService.
                'fonte_consumo'        => $temConversaoReal ? $ultimaPesagem->fonte_consumo : null,
                'rotulo_fonte_consumo' => $temConversaoReal ? $ultimaPesagem->rotuloFonteConsumo() : null,
                'conversao_eh_medida'  => $temConversaoReal && $ultimaPesagem->conversaoEhReal(),
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
            // whereDate (em vez de updateOrCreate com 'data' => 'Y-m-d'): o
            // cast "date" grava "Y-m-d 00:00:00", e só o DATE do MySQL
            // ignora a hora na comparação — em outros bancos (ex.: SQLite
            // dos testes) a linha de hoje não era achada e o INSERT
            // duplicado estourava a chave única (lote_id, data).
            $snapshot = LoteConsumoSnapshot::where('lote_id', $lote->lote_id)->whereDate('data', $hoje)->first()
                ?? new LoteConsumoSnapshot(['lote_id' => $lote->lote_id, 'data' => $hoje]);

            // Lote sem ração vinculada grava consumo ZERO: a tabela do plano
            // mostra uma sugestão pra ajudar a escolher, mas ela não é o plano
            // do lote — antes entrava no histórico (e daí em Relatórios e na
            // conversão alimentar) como se fosse consumo do lote.
            $snapshot->fill([
                'racao_id' => $lote->racao_id,
                'quantidade_animais' => $lote->quantidade_animais,
                'consumo_kg' => $lote->tem_racao ? $lote->total_dia : 0,
                'status_class' => $lote->status_class,
            ])->save();
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

    /**
     * Ponto de entrada usado pelo comando agendado (ver
     * App\Console\Commands\CapturarSnapshotsPlanoAlimentar) — grava o
     * snapshot de HOJE de TODOS os lotes de TODOS os usuários, sem depender
     * de sessão nem de alguém abrir a tela de Plano Alimentar naquele dia.
     * Sem isso, um dia sem visita à tela virava um buraco no histórico de
     * consumo ("Últimos 7 Dias", "Resumo Mensal" e a soma de consumo entre
     * duas pesagens usada na Conversão Alimentar real).
     */
    public function capturarSnapshotsGlobal(): void
    {
        $todosLotes = Lote::all();
        $racoes = Racao::all();

        $this->capturarSnapshotsDeHoje($this->montarLotesDashboardAutomatico($todosLotes, $racoes));
    }

    /**
     * Mesma ideia de capturarSnapshotsGlobal(), mas escopada só às fazendas
     * do usuário DA SESSÃO ATUAL — usada em requisições normais (Dashboard,
     * ver CadastroController::dashboard()), onde rodar a varredura GLOBAL
     * (todos os usuários do sistema) a cada carregamento de página seria um
     * desperdício e um acoplamento desnecessário entre contas. A varredura
     * verdadeiramente global fica só para o comando agendado.
     */
    public function capturarSnapshotsDoUsuario(): void
    {
        $todosLotes = $this->lotesDoUsuario()->get();
        $racoes = $this->racoesDoUsuario()->get();

        $this->capturarSnapshotsDeHoje($this->montarLotesDashboardAutomatico($todosLotes, $racoes));
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

        // Custo pelo preço cadastrado de CADA ração (o mesmo cálculo da API
        // usada pelo app) — antes era um preço médio fixo de R$ 3,50/kg.
        $precoPorRacao = $racoes->pluck('valor_kg', 'id');
        $custoSnapshot = fn ($s) => (float) $s->consumo_kg * (float) ($precoPorRacao[$s->racao_id] ?? 0);

        // Só lotes com ração VINCULADA entram no plano diário — a sugestão
        // mostrada nos lotes sem vínculo não é plano de ninguém.
        $lotesVinculados = $lotesDashboard->where('tem_racao', true);
        $totalDiario = round((float) $lotesVinculados->sum('total_dia'), 1);
        $custoDiario = round((float) $lotesVinculados->sum(fn ($l) => $l->total_dia * (float) ($precoPorRacao[$l->racao_id] ?? 0)), 2);

        // Janela usada pelo gráfico "Últimos 7 Dias".
        $snapshots7Dias = $this->snapshotsFiltrados($loteIds, $racaoId, today()->subDays(6), today());
        $ultimos7Dias = collect(range(6, 0))->map(function ($i) use ($snapshots7Dias, $custoSnapshot) {
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
                'custo' => round((float) $doDia->sum($custoSnapshot), 2),
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
        $custoTotalMesNum = round((float) $snapshotsMes->sum($custoSnapshot), 2);
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

            // Mesmos números acima, sem formatação de tela: os campos de
            // texto ("4.440", "11.199,00") e os gráficos em JSON-string são
            // para a Blade — no app, "4.440" virava 4,44 kg. A API (GET
            // /api/plano) lê estes dois blocos.
            'consumo7Dias' => [
                'labels' => $labels7Dias->all(),
                'valores_kg' => $valores7Dias->map(fn ($kg) => (float) $kg)->all(),
                'total_kg' => round((float) $valores7Dias->sum(), 1),
            ],
            'resumoMensal' => [
                'mes' => $referenciaMes->format('Y-m'),
                'labels' => $labelsMensal,
                'valores_kg' => $valoresMensal,
                'total_kg' => round($totalRacaoMesNum, 1),
                'custo_total' => $custoTotalMesNum,
                'media_diaria_kg' => $mediaDiariaMesNum,
            ],
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
     * Traduz os filtros ativos (Lote/Ração) em rótulos legíveis para os
     * "chips" do modal de Filtros Avançados — mesmo padrão do
     * RacaoController/EstoqueController/CochoController. A Fazenda não
     * entra mais aqui: virou a Unidade Produtiva selecionada no topo da
     * tela (mesmo padrão do Dashboard), não um filtro removível.
     */
    private function descreverFiltros(array $filtros, $fazendas, $lotes, $racaos): array
    {
        $itens = [];

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

    // listar() e cadastrar() renderizam exatamente a mesma tela.
    //
    // Unidade Produtiva: mesmo padrão/comportamento do Dashboard
    // (CadastroController::dashboard) — a fazenda escolhida na barra do
    // topo passa a ser o limite de tudo que a tela mostra e calcula (Lote,
    // Plano Alimentar, Ração, consumo). Sem fazenda selecionada (e mais de
    // uma cadastrada), a tela não roda nenhum cálculo em cima de dados de
    // outra unidade — ela nem entra na lista de opções do filtro de Lote —
    // e mostra o estado inicial pedindo pra escolher uma. Com uma única
    // fazenda cadastrada, ela já vem selecionada, sem exigir escolha manual.
    private function renderizarTela(Request $request)
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        // Captura o snapshot de HOJE com o conjunto COMPLETO de lotes do
        // usuário (todas as fazendas) — é uma tarefa de integridade de
        // histórico independente da Unidade Produtiva escolhida agora na
        // tela; escopar isso à seleção atual criaria buracos no consumo
        // das demais fazendas sempre que a tela for aberta com outra
        // selecionada (ver também o comando agendado
        // plano:capturar-snapshots, que cobre os dias sem ninguém abrir a
        // tela nenhuma vez).
        $todosLotesDoUsuario = $this->lotesDoUsuario()->with('fazenda')->get();
        $todasRacoesDoUsuario = $this->racoesDoUsuario()->get();
        $this->capturarSnapshotsDeHoje($this->montarLotesDashboardAutomatico($todosLotesDoUsuario, $todasRacoesDoUsuario));

        // Mesma fazenda selecionada (persistida em sessão) do Dashboard —
        // se já foi escolhida por lá, essa tela já abre nela.
        $fazendaSelecionadaId = $this->resolverFazendaSelecionadaComSessao($request, $fazendas);

        // Sem Unidade Produtiva selecionada, $todosLotes/$racaos ficam
        // vazios (não "de todas as fazendas") — nenhuma tabela, gráfico ou
        // filtro da tela mistura lote/ração de uma unidade que não é a
        // escolhida.
        $todosLotes = $fazendaSelecionadaId
            ? $todosLotesDoUsuario->where('fazenda_id', (int) $fazendaSelecionadaId)->sortBy('nome_lote')->values()
            : collect();
        $racaos = $fazendaSelecionadaId
            ? $todasRacoesDoUsuario->where('fazenda_id', (int) $fazendaSelecionadaId)->sortBy('nome_racao')->values()
            : collect();

        $filtros = $request->only(['lote_id', 'racao_id']);

        $lotes = $todosLotes
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

        // Período da seção de Conversão Alimentar (7/15/30/60/90 dias) — só essa
        // seção usa esse filtro; o resto da tela (Consumo por Lote, Resumo
        // Mensal) continua com seus próprios períodos fixos.
        $periodoConversao = in_array((int) $request->query('periodo_conversao'), self::PERIODOS_CONVERSAO, true)
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
                'fazendaSelecionadaId',
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
        return $this->renderizarTela($request);
    }

    // TELA CADASTRAR
    public function cadastrar(Request $request)
    {
        return $this->renderizarTela($request);
    }

    // CONSULTAR LOTES: retorna em JSON (HTML já renderizado) os lotes do
    // card "Consumo por Lote - Plano Diário" que ficaram de fora da carga
    // inicial — respeitando os mesmos filtros (Fazenda/Lote/Ração) da
    // página.
    public function lotesConsumoJson(Request $request)
    {
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
        $filtros = $request->only(['fazenda_id', 'lote_id', 'racao_id']);

        $lotes = $this->lotesDoUsuario()
            ->when(!empty($filtros['fazenda_id']), fn ($q) => $q->where('fazenda_id', $filtros['fazenda_id']))
            ->when(!empty($filtros['lote_id']), fn ($q) => $q->where('id', $filtros['lote_id']))
            ->get();

        $racoes = $this->racoesDoUsuario()->get();
        $periodoConversao = in_array((int) $request->query('periodo_conversao'), self::PERIODOS_CONVERSAO, true)
            ? (int) $request->query('periodo_conversao')
            : 7;
        $conversoes = $this->montarConversaoAlimentarAutomatica($lotes, $racoes, $filtros['racao_id'] ?? null, $periodoConversao);

        $restante = $conversoes->slice(self::CONVERSOES_INICIAL)->values();

        return response()->json([
            'success' => true,
            'html'    => view('partials.conversao-alimentar-cards', ['conversoes' => $restante])->render(),
        ]);
    }

    /**
     * "Voltar para a página atual" mais confiável que back(): back() usa a
     * última URL vista via GET guardada na sessão — mas os fetch() de
     * "Consultar Lotes"/"Consultar mais Conversões" (chamadas AJAX sem o
     * cabeçalho X-Requested-With que o Laravel checa pra NÃO contar uma
     * requisição como "página vista") também atualizam essa mesma URL.
     * Resultado: registrar uma pesagem ou vincular uma ração logo depois de
     * usar um desses botões "Consultar..." mandava de volta pro endpoint
     * JSON puro (a tela nunca reaparecia — só o JSON cru). $destino vem de
     * um campo oculto preenchido no navegador com a URL real da tela no
     * momento em que o modal foi aberto (ver cadastroplano.blade.php); só é
     * aceito se apontar para este mesmo site, nunca para um domínio externo.
     */
    private function voltarParaTela(Request $request)
    {
        $destino = $request->input('pagina_atual');

        if ($destino && str_starts_with($destino, url('/'))) {
            return redirect($destino);
        }

        return redirect()->route('plano.listar');
    }

    // VINCULAR RAÇÃO: liga (ou troca, ou remove) o vínculo explícito entre
    // um lote e uma ração, a partir do modal de detalhes da tela de Plano
    // Alimentar. Um único form serve os três casos — vincular, trocar e
    // "— Nenhuma —" (remover o vínculo).
    public function vincularRacao(Request $request, $loteId)
    {
        $lote = $this->lotesDoUsuario()->findOrFail($loteId);
        $racoes = $this->racoesDoUsuario()->get();
        $compativeis = $this->racoesCompativeisComLote($lote, $racoes);

        $racaoId = $request->input('racao_id');

        if ($racaoId !== null && $racaoId !== '' && !$compativeis->pluck('id')->contains((int) $racaoId)) {
            return $this->voltarParaTela($request)
                ->withErrors(['racao_id' => CompatibilidadeRacao::MENSAGEM])
                ->withInput();
        }

        // A mesma ração PODE alimentar vários lotes (como na fazenda de
        // verdade e como a API do app já permitia). A antiga trava "uma ração
        // por lote" existia porque o consumo era um número fixo da ração e
        // seria contado em dobro; agora cada lote tem o próprio consumo (kg
        // por animal × animais) e os dias de estoque somam os lotes que usam
        // a ração (Racao::consumoDiarioReferencia).

        $dados = $request->validate([
            'quantidade_por_animal' => ['nullable', 'numeric', 'gt:0', 'max:999'],
        ], [
            'quantidade_por_animal.gt' => 'Informe quanto cada animal deve comer por dia (maior que zero).',
            'quantidade_por_animal.max' => 'A quantidade por animal parece alta demais — confira o valor (kg por animal por dia).',
        ]);

        // Vincular = plano ativo do lote (o MESMO registro "planos" que o app
        // cria pela API): ração + kg por animal por dia. Antes a web só
        // gravava lotes.racao_id e o consumo ficava preso ao número fixo da
        // ração; agora o consumo do lote acompanha a quantidade de animais.
        if (!$racaoId) {
            DB::transaction(fn () => Plano::encerrarDoLote($lote));

            return $this->voltarParaTela($request)->with('success', 'Vínculo de ração removido do lote. O plano alimentar dele foi encerrado.');
        }

        $plano = DB::transaction(fn () => Plano::ativarParaLote(
            $lote,
            $racoes->firstWhere('id', (int) $racaoId),
            isset($dados['quantidade_por_animal']) ? (float) $dados['quantidade_por_animal'] : null
        ));

        return $this->voltarParaTela($request)->with('success', 'Plano salvo: ' . number_format($plano->quantidade_recomendada, 2, ',', '.')
            . ' kg por animal por dia → ' . number_format($plano->consumoDiarioPrevisto(), 1, ',', '.') . " kg/dia para o lote {$lote->nome_lote}.");
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
            return $this->voltarParaTela($request)
                ->withErrors(['data' => 'A data precisa ser posterior à última pesagem registrada (' . $ultimaPesagem->data_registro->format('d/m/Y') . ').'])
                ->withInput();
        }

        $pesoNovo = (float) $validado['peso_medio_kg'];
        $quantidadeAnimaisAtual = (int) ($lote->quantidade_animais ?? 0);

        $ganhoPesoKg = 0.0;
        $consumoKg = 0.0;
        $custoRs = 0.0;
        $fonteConsumo = null;
        $eficienciaAlimentar = 0.0;

        if ($ultimaPesagem) {
            // Fonte da verdade é o PRÓPRIO histórico de pesagens, nunca o
            // Lote.peso_medio atual — esse campo pode ter sido reeditado
            // livremente pela tela de Gestão de Lotes (fora do fluxo de
            // Pesagem), o que corromperia o "peso anterior" sem deixar
            // rastro. Lotes com pesagem antiga (antes destas colunas
            // existirem) caem no fallback do peso do próprio Lote, só
            // dessa vez.
            $pesoAnterior = (float) ($ultimaPesagem->peso_medio_kg ?: $lote->peso_medio ?? 0);
            $quantidadeAnimaisAnterior = (int) ($ultimaPesagem->quantidade_animais ?: $quantidadeAnimaisAtual);

            // Ganho total do lote no período: usa a MÉDIA entre a
            // quantidade de animais de cada ponta, pra o cálculo continuar
            // correto mesmo quando nasceram, morreram ou saíram animais do
            // lote entre as duas pesagens (sem isso, um lote que perdeu
            // metade dos animais no meio do período teria o ganho total
            // subestimado pela metade).
            $quantidadeMedia = ($quantidadeAnimaisAnterior + $quantidadeAnimaisAtual) / 2;

            if ($pesoAnterior > 0 && $quantidadeMedia > 0) {
                $ganhoPesoKg = round(($pesoNovo - $pesoAnterior) * $quantidadeMedia, 2);
            }

            // Consumo do período (última pesagem, esta pesagem] da fonte mais
            // confiável: sensores → ração colocada nos cochos → plano. Antes
            // somava sempre o PLANO (estimativa) e contava o dia da pesagem
            // anterior duas vezes; o custo usava um preço fixo de R$ 3,50.
            $consumo = app(ConsumoLoteService::class)->noPeriodo($lote, $ultimaPesagem->data_registro, $dataRegistro);
            $consumoKg = $consumo['kg'];
            $custoRs = $consumo['custo'];
            $fonteConsumo = $consumo['fonte'];

            // FCR (kg ração / kg ganho) só faz sentido com ganho positivo —
            // perda de peso ou estagnação ficam visíveis pelo próprio
            // ganho_peso_kg (podendo ser zero/negativo), sem uma "conversão"
            // fabricada em cima disso.
            if ($ganhoPesoKg > 0) {
                $eficienciaAlimentar = round($consumoKg / $ganhoPesoKg, 2);
            }
        }

        // whereDate pelo mesmo motivo de capturarSnapshotsDeHoje().
        $desempenho = DesempenhoLote::where('lote_id', $lote->id)->whereDate('data_registro', $dataRegistro->toDateString())->first()
            ?? new DesempenhoLote(['lote_id' => $lote->id, 'data_registro' => $dataRegistro->toDateString()]);

        $desempenho->fill([
            'peso_medio_kg' => $pesoNovo,
            'quantidade_animais' => $quantidadeAnimaisAtual,
            'consumo_kg' => $consumoKg,
            'fonte_consumo' => $fonteConsumo,
            'custo_rs' => $custoRs,
            'ganho_peso_kg' => $ganhoPesoKg,
            'eficiencia_alimentar' => $eficienciaAlimentar,
        ])->save();

        $lote->update(['peso_medio' => $pesoNovo]);

        // Mensagem diz o que aconteceu e, quando falta algo, o que fazer.
        if (!$ultimaPesagem) {
            $mensagem = 'Primeira pesagem registrada. A conversão alimentar aparece a partir da próxima pesagem.';
        } elseif ($fonteConsumo === null) {
            $mensagem = 'Pesagem registrada, mas não há consumo registrado no período — abasteça os cochos do lote '
                . '(ou vincule uma ração no plano) para calcular a conversão alimentar.';
        } elseif ($ganhoPesoKg <= 0) {
            $mensagem = 'Pesagem registrada. O lote não ganhou peso no período, então não há conversão alimentar para calcular.';
        } else {
            $mensagem = 'Pesagem registrada! Conversão alimentar do período: '
                . number_format($eficienciaAlimentar, 2, ',', '.') . ' kg de ração por kg ganho ('
                . mb_strtolower(DesempenhoLote::ROTULOS_FONTE[$fonteConsumo]) . ').';
        }

        return $this->voltarParaTela($request)->with('success', $mensagem);
    }
}
