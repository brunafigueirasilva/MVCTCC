<?php

namespace App\Services;

use App\Models\Cocho;
use App\Models\EstoqueMovimentacao;
use App\Models\LeituraIot;
use App\Models\Racao;
use App\Models\Sensor;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Único ponto do sistema que trata dados IoT dos cochos:
 *
 *  - registrarLeitura(): grava a leitura no histórico (leituras_iot) e
 *    atualiza o cocho, numa transação com o cocho travado:
 *      · balança  → a quantidade do cocho (cochos.nivel_atual) passa a ser
 *        o peso lido; a diferença para a quantidade anterior fica em
 *        variacao_kg (negativa = consumo, positiva = reposição).
 *      · distância → calcula o nível físico pela calibração do sensor; se o
 *        cocho NÃO tem balança, a quantidade é estimada pelo nível. Com
 *        balança, ela é a referência de quantidade e a distância fica como
 *        leitura do nível físico.
 *  - salvarSensores(): cadastra/troca os códigos dos sensores do cocho sem
 *    perder o histórico.
 *  - resumo() / historico(): estado atual, indicadores e séries dos
 *    gráficos — tudo calculado a partir do banco (leituras + movimentações
 *    de estoque), nada fixo.
 *
 * O abastecimento pelo estoque continua no EstoqueService (saída da ração +
 * nivel_atual do cocho); as leituras seguintes da balança partem desse
 * novo nível, então reposição registrada e consumo nunca se misturam.
 */
class CochoIotService
{
    /**
     * @param  array{distancia_cm?: float|null, peso_kg?: float|null, presenca_animal?: bool|null, distancia_presenca_cm?: float|null, data_hora?: CarbonInterface|null}  $dados
     * @return array{leitura: LeituraIot, duplicada: bool}
     */
    public function registrarLeitura(Cocho $cocho, Sensor $sensor, array $dados): array
    {
        $dataHora = isset($dados['data_hora']) ? Carbon::parse($dados['data_hora']) : now();
        $dataHora = $dataHora->copy()->startOfSecond();

        return DB::transaction(function () use ($cocho, $sensor, $dados, $dataHora) {
            // Trava o cocho primeiro: leituras do mesmo cocho são processadas
            // uma de cada vez (a verificação de duplicidade abaixo fica segura).
            $cocho = Cocho::whereKey($cocho->id)->lockForUpdate()->firstOrFail();
            $sensor = Sensor::whereKey($sensor->id)->lockForUpdate()->firstOrFail();

            $existente = LeituraIot::where('sensor_id', $sensor->id)->where('data_hora', $dataHora)->first();
            if ($existente) {
                // Reenvio do mesmo pacote: não duplica nem altera o cocho.
                return ['leitura' => $existente, 'duplicada' => true];
            }

            $quantidadeAnterior = round((float) $cocho->nivel_atual, 2);
            $capacidade = (float) $cocho->capacidade;

            $distancia = null;
            $peso = null;
            $percentual = null;
            $quantidade = null;

            if ($sensor->tipo === Sensor::TIPO_BALANCA) {
                $peso = round((float) $dados['peso_kg'], 2);
                // Dentro da tolerância da célula de carga, limita à capacidade.
                $quantidade = $capacidade > 0 ? min($peso, $capacidade) : $peso;
                $percentual = $capacidade > 0 ? round($quantidade / $capacidade * 100, 2) : null;
            } else {
                $distancia = round((float) $dados['distancia_cm'], 2);
                $percentual = $sensor->percentualPorDistancia($distancia);

                $temBalancaAtiva = Sensor::where('cocho_id', $cocho->id)
                    ->where('tipo', Sensor::TIPO_BALANCA)
                    ->where('ativo', true)
                    ->exists();

                if (!$temBalancaAtiva && $percentual !== null && $capacidade > 0) {
                    $quantidade = round($capacidade * $percentual / 100, 2);
                }
            }

            // Leitura atrasada (data_hora anterior a outra já gravada): entra
            // no histórico, mas não volta o cocho para um estado antigo.
            $maisRecente = !LeituraIot::where('cocho_id', $cocho->id)
                ->whereNotNull('quantidade_kg')
                ->where('data_hora', '>', $dataHora)
                ->exists();

            $variacao = null;
            if ($quantidade !== null) {
                $quantidade = round($quantidade, 2);
                if ($maisRecente) {
                    $variacao = round($quantidade - $quantidadeAnterior, 2);
                    $cocho->update(['nivel_atual' => $quantidade]);
                }
            }

            $leitura = LeituraIot::create([
                'cocho_id' => $cocho->id,
                'sensor_id' => $sensor->id,
                'tipo_sensor' => $sensor->tipo,
                'distancia_cm' => $distancia,
                'peso_kg' => $peso,
                'presenca_animal' => isset($dados['presenca_animal']) ? (bool) $dados['presenca_animal'] : null,
                'distancia_presenca_cm' => isset($dados['distancia_presenca_cm']) ? round((float) $dados['distancia_presenca_cm'], 2) : null,
                'nivel_percentual' => $percentual,
                'quantidade_kg' => $quantidade,
                'variacao_kg' => $variacao,
                'data_hora' => $dataHora,
            ]);

            if (!$sensor->ultima_leitura_em || $dataHora->greaterThanOrEqualTo($sensor->ultima_leitura_em)) {
                $sensor->forceFill([
                    'ultima_leitura_em' => $dataHora,
                    'ultimo_valor' => $peso ?? $distancia,
                ])->save();
            }

            return ['leitura' => $leitura, 'duplicada' => false];
        });
    }

    /** Leitura recusada de um sensor identificado: vira status "erro". */
    public function registrarErro(Sensor $sensor, string $mensagem): void
    {
        $sensor->forceFill([
            'ultimo_erro' => mb_substr($mensagem, 0, 255),
            'ultimo_erro_em' => now(),
        ])->save();
    }

    /**
     * Cadastra/atualiza os sensores do cocho. Código vazio desativa o
     * sensor (o histórico fica); código novo no mesmo tipo troca o sensor
     * físico mantendo o histórico do cocho.
     *
     * @param  array{sensor_distancia?: ?string, sensor_balanca?: ?string, distancia_vazio_cm?: ?float, distancia_cheio_cm?: ?float}  $dados
     */
    public function salvarSensores(Cocho $cocho, array $dados): void
    {
        $codigos = [
            Sensor::TIPO_DISTANCIA => trim((string) ($dados['sensor_distancia'] ?? '')),
            Sensor::TIPO_BALANCA => trim((string) ($dados['sensor_balanca'] ?? '')),
        ];

        if ($codigos[Sensor::TIPO_DISTANCIA] !== '' && strcasecmp($codigos[Sensor::TIPO_DISTANCIA], $codigos[Sensor::TIPO_BALANCA]) === 0) {
            throw ValidationException::withMessages(['sensor_balanca' => 'Os sensores de distância e de balança precisam de códigos diferentes.']);
        }

        DB::transaction(function () use ($cocho, $codigos, $dados) {
            foreach ($codigos as $tipo => $codigo) {
                $sensor = Sensor::where('cocho_id', $cocho->id)->where('tipo', $tipo)->first();

                if ($codigo === '') {
                    $sensor?->update(['ativo' => false]);
                    continue;
                }

                $emUso = Sensor::where('codigo', $codigo)->when($sensor, fn ($q) => $q->whereKeyNot($sensor->id))->exists();
                if ($emUso) {
                    throw ValidationException::withMessages([
                        $tipo === Sensor::TIPO_DISTANCIA ? 'sensor_distancia' : 'sensor_balanca' => "O código de sensor \"{$codigo}\" já está em uso em outro cocho.",
                    ]);
                }

                $atributos = ['codigo' => $codigo, 'ativo' => true];
                if ($tipo === Sensor::TIPO_DISTANCIA) {
                    $atributos['distancia_vazio_cm'] = $dados['distancia_vazio_cm'] ?? null;
                    $atributos['distancia_cheio_cm'] = $dados['distancia_cheio_cm'] ?? null;
                }

                if ($sensor) {
                    if ($sensor->codigo !== $codigo) {
                        // Sensor físico trocado: zera o resumo da última leitura.
                        $sensor->forceFill(['ultima_leitura_em' => null, 'ultimo_valor' => null, 'ultimo_erro' => null, 'ultimo_erro_em' => null]);
                    }
                    $sensor->fill($atributos)->save();
                } else {
                    Sensor::create($atributos + ['cocho_id' => $cocho->id, 'tipo' => $tipo]);
                }
            }
        });
    }

    /** Estado atual do cocho + indicadores + alertas (GET /cochos/{id}/iot). */
    public function resumo(Cocho $cocho): array
    {
        // racao_id junto: armazem() usa a ração do lote (sem ela o app nunca via
        // a ração vinculada no card IoT).
        $cocho->loadMissing(['lote:id,nome_lote,fazenda_id,racao_id', 'lote.fazenda:id,nome_fazenda', 'sensores']);

        $distancia = $cocho->sensores->firstWhere('tipo', Sensor::TIPO_DISTANCIA);
        $balanca = $cocho->sensores->firstWhere('tipo', Sensor::TIPO_BALANCA);
        $capacidade = (float) $cocho->capacidade;
        $quantidade = round((float) $cocho->nivel_atual, 2);
        $percentual = $capacidade > 0 ? round(min(100, max(0, $quantidade / $capacidade * 100)), 2) : 0.0;

        $ultimaLeitura = $cocho->leiturasIot()->with('sensor:id,codigo')->latest('data_hora')->latest('id')->first();
        $ultimaDistancia = $distancia ? $cocho->leiturasIot()->where('sensor_id', $distancia->id)->latest('data_hora')->first() : null;
        $ultimaPresenca = $cocho->leiturasIot()->whereNotNull('presenca_animal')->latest('data_hora')->latest('id')->first();
        $ultimaMovimentacao = $cocho->movimentacoes()->where('origem', 'abastecimento_cocho')->latest('data_movimentacao')->first();

        $ultimaAtualizacao = collect([$ultimaLeitura?->data_hora, $ultimaMovimentacao?->data_movimentacao])->filter()->max();

        // Quantidade MEDIDA pelos sensores (balança, ou distância sem
        // balança): 0 enquanto não existir leitura — nunca um valor estimado.
        $ultimaQuantidadeSensor = $cocho->leiturasIot()->whereNotNull('quantidade_kg')->latest('data_hora')->latest('id')->first();
        $quantidadeSensor = $ultimaQuantidadeSensor ? round((float) $ultimaQuantidadeSensor->quantidade_kg, 2) : 0.0;

        return [
            'cocho_id' => $cocho->id,
            'nome_cocho' => $cocho->nome_cocho,
            'lote' => $cocho->lote ? ['id' => $cocho->lote->id, 'nome_lote' => $cocho->lote->nome_lote] : null,
            'fazenda' => $cocho->lote?->fazenda ? ['id' => $cocho->lote->fazenda->id, 'nome_fazenda' => $cocho->lote->fazenda->nome_fazenda] : null,

            'capacidade_total' => $capacidade,
            'quantidade_total' => $capacidade,
            'quantidade_atual' => $quantidade,
            'espaco_livre_kg' => round(max(0, $capacidade - $quantidade), 2),
            'nivel_percentual' => $percentual,
            'percentual_nivel' => $percentual,
            'status_abastecimento' => $cocho->statusAbastecimento(),

            // Últimas leituras físicas — 0 = ainda não existe leitura.
            'distancia_cm' => $distancia?->ultimo_valor ?? 0.0,
            'peso_kg' => $balanca?->ultimo_valor ?? 0.0,
            'nivel_sensor_percentual' => $ultimaDistancia?->nivel_percentual ?? 0.0,
            // Presença de animal (HC-SR04 do controlador da balança): última
            // leitura que trouxe essa informação; null = nunca informada.
            'presenca_animal' => $ultimaPresenca?->presenca_animal,
            'presenca_data_hora' => $ultimaPresenca?->data_hora?->format('Y-m-d H:i:s'),

            'sensor_distancia_id' => $distancia?->id,
            'sensor_balanca_id' => $balanca?->id,
            'sensores' => $cocho->sensores->map(fn (Sensor $s) => $s->dadosApi())->values(),
            'status_sensor' => $cocho->statusSensor(),
            'minutos_offline' => (int) config('iot.minutos_offline'),

            'data_hora_leitura' => $ultimaLeitura?->data_hora?->format('Y-m-d H:i:s'),
            'ultima_atualizacao' => $ultimaAtualizacao?->format('Y-m-d H:i:s'),

            // Cocho → quantidade disponível agora, pelo sensor.
            'cocho_sensor' => [
                'quantidade_kg' => $quantidadeSensor,
                'capacidade_kg' => $capacidade,
                'percentual' => $capacidade > 0 ? round(min(100, $quantidadeSensor / $capacidade * 100), 2) : 0.0,
                'tem_leitura' => $ultimaQuantidadeSensor !== null,
                'data_hora' => $ultimaQuantidadeSensor?->data_hora?->format('Y-m-d H:i:s'),
            ],
            // Armazém → quantidade total disponível na fazenda do cocho.
            'armazem' => $this->armazem($cocho),

            'indicadores' => $this->indicadores($cocho, $quantidade, $ultimaLeitura, $ultimaMovimentacao),
            'alertas' => $this->alertas($cocho),
        ];
    }

    /**
     * Estoque do armazém da fazenda do cocho — mesma regra da tela "Nível de
     * Estoque" (Quantidade no Armazém): soma do estoque das rações e
     * ocupação sobre a capacidade (estoque_total) das que a têm cadastrada.
     * Inclui a ração vinculada ao lote do cocho, quando houver.
     */
    public function armazem(Cocho $cocho): array
    {
        $cocho->loadMissing(['lote.racao']);
        $fazendaId = $cocho->lote?->fazenda_id;
        $racoes = $fazendaId ? Racao::where('fazenda_id', $fazendaId)->get() : collect();

        $comCapacidade = $racoes->filter(fn (Racao $r) => (float) $r->estoque_total > 0);
        $capacidade = round((float) $comCapacidade->sum('estoque_total'), 2);
        $racaoLote = $cocho->lote?->racao;

        return [
            'fazenda_id' => $fazendaId,
            'quantidade_total_kg' => round((float) $racoes->sum('estoque'), 2),
            'capacidade_total_kg' => $capacidade,
            'percentual' => $capacidade > 0 ? (int) round(min(100, $comCapacidade->sum('estoque') / $capacidade * 100)) : null,
            'total_racoes' => $racoes->count(),
            'racao_lote' => $racaoLote ? [
                'id' => $racaoLote->id,
                'nome_racao' => $racaoLote->nome_racao,
                'estoque_kg' => round((float) $racaoLote->estoque, 2),
                'estoque_total_kg' => $racaoLote->estoque_total !== null ? (float) $racaoLote->estoque_total : null,
                'percentual' => $racaoLote->percentualEstoque(),
                'status_estoque' => $racaoLote->statusEstoque(),
                'dias_restantes' => $racaoLote->diasRestantesEstoque(),
            ] : null,
        ];
    }

    /**
     * Séries dos gráficos e últimas leituras do período (1h | hoje | 7d | 30d):
     * quantidade/nível no fim de cada intervalo, consumo e reposição
     * (detectada pela balança + abastecimentos registrados no estoque).
     */
    public function historico(Cocho $cocho, string $periodo = 'hoje', int $limiteLeituras = 50): array
    {
        [$inicio, $passo, $formato] = $this->janela($periodo);
        $fim = now();
        $capacidade = (float) $cocho->capacidade;
        $ruido = (float) config('iot.ruido_kg', 0.2);

        $leituras = $cocho->leiturasIot()
            ->where('data_hora', '>=', $inicio)
            ->orderBy('data_hora')->orderBy('id')
            ->get(['id', 'sensor_id', 'tipo_sensor', 'distancia_cm', 'peso_kg', 'presenca_animal', 'distancia_presenca_cm', 'nivel_percentual', 'quantidade_kg', 'variacao_kg', 'data_hora']);

        $abastecimentos = $cocho->movimentacoes()
            ->where('origem', 'abastecimento_cocho')
            ->where('data_movimentacao', '>=', $inicio)
            ->orderBy('data_movimentacao')
            ->get(['id', 'racao_id', 'quantidade', 'data_movimentacao']);

        // Quantidade conhecida antes da janela, para o gráfico já começar
        // do valor real (sem inventar nada quando não há leitura anterior).
        $quantidadeCorrente = $cocho->leiturasIot()
            ->where('data_hora', '<', $inicio)
            ->whereNotNull('quantidade_kg')
            ->latest('data_hora')
            ->value('quantidade_kg');
        $quantidadeCorrente = $quantidadeCorrente !== null ? (float) $quantidadeCorrente : null;
        $nivelFisicoCorrente = null;

        // Agrupa uma vez só por intervalo (índice = segundos desde o início
        // ÷ tamanho do intervalo), em vez de varrer tudo a cada intervalo.
        $segundosPasso = ($passo->d * 86400) + ($passo->h * 3600) + ($passo->i * 60) + $passo->s;
        $indice = fn (CarbonInterface $quando) => intdiv($quando->getTimestamp() - $inicio->getTimestamp(), $segundosPasso);
        $leiturasPorBucket = $leituras->groupBy(fn (LeituraIot $l) => $indice($l->data_hora));
        $abastecimentosPorBucket = $abastecimentos->groupBy(fn ($m) => $indice($m->data_movimentacao));

        $pontos = [];
        for ($i = 0, $cursor = $inicio->copy(); $cursor->lessThanOrEqualTo($fim); $i++, $cursor = $cursor->copy()->add($passo)) {
            $doBucket = $leiturasPorBucket->get($i, collect());
            $abastecidoBucket = $abastecimentosPorBucket->get($i, collect());

            $ultimaQuantidade = $doBucket->whereNotNull('quantidade_kg')->last();
            if ($ultimaQuantidade) {
                $quantidadeCorrente = $ultimaQuantidade->quantidade_kg;
            }

            $ultimaDistancia = $doBucket->where('tipo_sensor', Sensor::TIPO_DISTANCIA)->whereNotNull('nivel_percentual')->last();
            if ($ultimaDistancia) {
                $nivelFisicoCorrente = $ultimaDistancia->nivel_percentual;
            }

            $consumo = $doBucket->filter(fn ($l) => $l->variacao_kg !== null && $l->variacao_kg < -$ruido)->sum(fn ($l) => -$l->variacao_kg);
            $reposicaoSensor = $doBucket->filter(fn ($l) => $l->variacao_kg !== null && $l->variacao_kg > $ruido)->sum('variacao_kg');

            $pontos[] = [
                'inicio' => $cursor->format('Y-m-d H:i:s'),
                'rotulo' => $cursor->format($formato),
                'quantidade_kg' => $quantidadeCorrente !== null ? round($quantidadeCorrente, 2) : null,
                'nivel_percentual' => $quantidadeCorrente !== null && $capacidade > 0
                    ? round(min(100, $quantidadeCorrente / $capacidade * 100), 2)
                    : null,
                'nivel_fisico_percentual' => $nivelFisicoCorrente,
                'consumo_kg' => round($consumo, 2),
                'reposicao_kg' => round($reposicaoSensor + $abastecidoBucket->sum('quantidade'), 2),
                'leituras' => $doBucket->count(),
            ];
        }

        $ultimas = $cocho->leiturasIot()
            ->with('sensor:id,codigo')
            ->where('data_hora', '>=', $inicio)
            ->latest('data_hora')->latest('id')
            ->limit($limiteLeituras)
            ->get();

        return [
            'cocho_id' => $cocho->id,
            'periodo' => $periodo,
            'inicio' => $inicio->format('Y-m-d H:i:s'),
            'fim' => $fim->format('Y-m-d H:i:s'),
            'capacidade_total' => $capacidade,
            'pontos' => $pontos,
            'totais' => [
                'consumo_kg' => round(collect($pontos)->sum('consumo_kg'), 2),
                'reposicao_kg' => round(collect($pontos)->sum('reposicao_kg'), 2),
                'leituras' => $leituras->count(),
            ],
            'reposicoes' => $abastecimentos->map(fn ($m) => [
                'id' => $m->id,
                'racao_id' => $m->racao_id,
                'quantidade_kg' => (float) $m->quantidade,
                'data_hora' => $m->data_movimentacao->format('Y-m-d H:i:s'),
            ])->values(),
            'leituras' => $ultimas->map(fn (LeituraIot $l) => $l->dadosApi())->values(),
        ];
    }

    /** Consumo (kg) detectado pelas leituras desde $desde (até $ate, se informado). */
    public function consumoDesde(Cocho $cocho, CarbonInterface $desde, ?CarbonInterface $ate = null): float
    {
        $ruido = (float) config('iot.ruido_kg', 0.2);

        return round((float) $cocho->leiturasIot()
            ->where('data_hora', '>=', $desde)
            ->when($ate, fn ($q) => $q->where('data_hora', '<=', $ate))
            ->where('variacao_kg', '<', -$ruido)
            ->sum(DB::raw('-variacao_kg')), 2);
    }

    // ------------------------------------------------------------------

    private function indicadores(Cocho $cocho, float $quantidade, ?LeituraIot $ultimaLeitura, ?EstoqueMovimentacao $ultimaMovimentacao): array
    {
        $ruido = (float) config('iot.ruido_kg', 0.2);
        $desde7Dias = now()->subDays(7);
        $consumo7Dias = $this->consumoDesde($cocho, $desde7Dias);
        $consumoHoje = $this->consumoDesde($cocho, Carbon::today());

        // Consumo médio só com pelo menos 6 h de leituras na janela — com
        // menos que isso, extrapolar para "por dia" daria número sem base.
        $primeiraNaJanela = $cocho->leiturasIot()->where('data_hora', '>=', $desde7Dias)->whereNotNull('quantidade_kg')->min('data_hora');
        $horasCobertas = $primeiraNaJanela ? Carbon::parse($primeiraNaJanela)->diffInMinutes(now()) / 60 : 0;
        $consumoMedioDiario = $horasCobertas >= 6 ? round($consumo7Dias / ($horasCobertas / 24), 2) : null;

        $previsao = null;
        if ($consumoMedioDiario && $consumoMedioDiario > 0 && $quantidade > 0) {
            $horas = $quantidade / ($consumoMedioDiario / 24);
            $previsao = [
                'horas' => round($horas, 1),
                'data_hora' => now()->addMinutes((int) round($horas * 60))->format('Y-m-d H:i:s'),
            ];
        }

        $reposicaoSensor = $cocho->leiturasIot()->where('variacao_kg', '>', $ruido)->latest('data_hora')->first();
        $ultimaReposicao = collect([
            $ultimaMovimentacao ? ['origem' => 'abastecimento', 'quantidade_kg' => (float) $ultimaMovimentacao->quantidade, 'data_hora' => $ultimaMovimentacao->data_movimentacao] : null,
            $reposicaoSensor ? ['origem' => 'sensor', 'quantidade_kg' => (float) $reposicaoSensor->variacao_kg, 'data_hora' => $reposicaoSensor->data_hora] : null,
        ])->filter()->sortByDesc(fn ($r) => $r['data_hora']->getTimestamp())->first();

        return [
            'consumo_hoje_kg' => $consumoHoje,
            'consumo_7_dias_kg' => $consumo7Dias,
            'consumo_medio_diario_kg' => $consumoMedioDiario,
            'ultima_reposicao' => $ultimaReposicao ? [
                'origem' => $ultimaReposicao['origem'],
                'quantidade_kg' => round($ultimaReposicao['quantidade_kg'], 2),
                'data_hora' => $ultimaReposicao['data_hora']->format('Y-m-d H:i:s'),
            ] : null,
            'ultima_leitura' => $ultimaLeitura ? [
                'tipo_sensor' => $ultimaLeitura->tipo_sensor,
                'sensor_codigo' => $ultimaLeitura->sensor?->codigo,
                'data_hora' => $ultimaLeitura->data_hora->format('Y-m-d H:i:s'),
            ] : null,
            'minutos_desde_ultima_leitura' => $ultimaLeitura ? (int) $ultimaLeitura->data_hora->diffInMinutes(now()) : null,
            'tempo_desde_ultima_leitura' => $ultimaLeitura ? $ultimaLeitura->data_hora->locale('pt_BR')->diffForHumans() : null,
            'previsao_esvaziamento' => $previsao,
        ];
    }

    private function alertas(Cocho $cocho): array
    {
        $alertas = [];
        $ativos = $cocho->sensores->where('ativo', true);

        // Com todos os sensores sem comunicação, o nível é o da última
        // leitura — o alerta avisa que pode não ser o de agora.
        $semComunicacao = $ativos->isNotEmpty()
            && $ativos->every(fn (Sensor $s) => in_array($s->status(), [Sensor::STATUS_OFFLINE, Sensor::STATUS_ERRO], true));
        $ressalva = $semComunicacao ? ' (pela última leitura recebida — os sensores estão sem comunicação)' : '';

        if ($cocho->statusAbastecimento() === 'Vazio') {
            $alertas[] = ['tipo' => 'nivel_baixo', 'prioridade' => 'critico', 'mensagem' => 'Cocho vazio — nenhuma ração disponível' . $ressalva . '.'];
        } elseif ($cocho->statusAbastecimento() === 'Baixo') {
            $alertas[] = ['tipo' => 'nivel_baixo', 'prioridade' => 'baixo', 'mensagem' => 'Nível abaixo de ' . Cocho::PERCENTUAL_ALERTA . '% da capacidade' . $ressalva . ' — considere reabastecer.'];
        }

        if ($ativos->isEmpty()) {
            $alertas[] = ['tipo' => 'sem_sensor', 'prioridade' => 'info', 'mensagem' => 'Nenhum sensor IoT cadastrado neste cocho.'];
        }

        foreach ($ativos as $sensor) {
            $nome = ($sensor->tipo === Sensor::TIPO_BALANCA ? 'Balança' : 'Sensor de distância') . " {$sensor->codigo}";

            match ($sensor->status()) {
                Sensor::STATUS_OFFLINE => $alertas[] = ['tipo' => 'sensor_offline', 'prioridade' => 'baixo', 'mensagem' => "{$nome} sem comunicação — nenhuma leitura há mais de " . config('iot.minutos_offline') . ' min'
                    . ($sensor->ultima_leitura_em ? ' (última em ' . $sensor->ultima_leitura_em->format('d/m H:i') . ')' : '') . '. Verifique a energia e o Wi-Fi do ESP32.'],
                Sensor::STATUS_ERRO => $alertas[] = ['tipo' => 'sensor_erro', 'prioridade' => 'critico', 'mensagem' => "{$nome} com erro na leitura: {$sensor->ultimo_erro}"],
                Sensor::STATUS_SEM_LEITURA => $alertas[] = ['tipo' => 'sem_leitura', 'prioridade' => 'info', 'mensagem' => "{$nome} ainda não enviou nenhuma leitura."],
                default => null,
            };

            if (!$sensor->calibrado()) {
                $alertas[] = ['tipo' => 'sem_calibracao', 'prioridade' => 'baixo', 'mensagem' => "{$nome} sem calibração — informe a distância com o cocho vazio para calcular o nível."];
            }
        }

        return $alertas;
    }

    /** @return array{0: Carbon, 1: \DateInterval, 2: string} */
    private function janela(string $periodo): array
    {
        return match ($periodo) {
            // Última hora, de 30 em 30 s: cocho pequeno (protótipo) muda
            // pouco e rápido — no dia inteiro a curva some — e a tela ao
            // vivo vê o gráfico andar sem esperar fechar o minuto.
            '1h' => [now()->subHour()->startOfMinute(), new \DateInterval('PT30S'), 'H:i:s'],
            '7d' => [Carbon::today()->subDays(6), new \DateInterval('PT6H'), 'd/m H\h'],
            '30d' => [Carbon::today()->subDays(29), new \DateInterval('P1D'), 'd/m'],
            // Hoje: de 5 em 5 minutos (as leituras do ESP32 chegam a cada
            // poucos segundos — por hora, o dia virava 1 ou 2 barras).
            default => [Carbon::today(), new \DateInterval('PT5M'), 'H:i'],
        };
    }

    /** Períodos aceitos pelo histórico. */
    public const PERIODOS = ['1h', 'hoje', '7d', '30d'];
}
