<?php

namespace App\Console\Commands;

use App\Models\Cocho;
use App\Models\LeituraIot;
use App\Models\Lote;
use App\Models\DesempenhoLote;
use App\Models\LoteConsumoSnapshot;
use App\Models\LoteOcorrencia;
use App\Models\Plano;
use App\Models\Sensor;
use App\Services\CochoIotService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Dados IoT simulados para um lote sem ESP32 (demonstração dos gráficos):
 * cada cocho ganha uma balança e um sensor de distância "SIM-..." (com
 * presença de animal) e leituras dos últimos dias —
 * animais comendo mais de dia que de noite, reposição até encher às 06h
 * e às 16h. O consumo diário segue a ração do lote (racaos.consumo)
 * dividida pelos cochos. Também preenche os dias sem snapshot de consumo
 * (Plano Alimentar / Relatório) com o total simulado e, se o lote ainda
 * não tiver, cria plano alimentar, pesagens semanais (Conversão Alimentar
 * real) e duas ocorrências de óbito no passado.
 *
 *   php artisan iot:simular 37            → refaz o histórico simulado
 *   php artisan iot:simular 37 --ao-vivo  → depois segue mandando leituras
 *
 * Só mexe em sensores com código "SIM-": rodar de novo apaga e recria
 * apenas as leituras simuladas.
 */
class SimularIotLote extends Command
{
    protected $signature = 'iot:simular {lote : ID do lote}
        {--dias=30 : Dias de histórico}
        {--ao-vivo : Depois do histórico, continua gerando leituras}
        {--intervalo=3 : Segundos entre leituras no modo ao vivo}
        {--acelerar=30 : No modo ao vivo, quantas vezes mais rápido os animais comem (para os gráficos se mexerem)}';

    protected $description = 'Gera leituras IoT simuladas (balança) para os cochos de um lote';

    private const HORARIOS_REPOSICAO = [6, 16];

    // Calibração do sensor de distância simulado (cocho vazio / cheio).
    private const DISTANCIA_VAZIO_CM = 60;
    private const DISTANCIA_CHEIO_CM = 10;

    public function handle(CochoIotService $iot): int
    {
        $lote = Lote::with(['cochos', 'racao'])->find($this->argument('lote'));
        if (!$lote || $lote->cochos->isEmpty()) {
            $this->error('Lote não encontrado ou sem cochos.');
            return self::FAILURE;
        }

        $dias = max(1, min(30, (int) $this->option('dias')));
        $consumoDiarioLote = (float) ($lote->racao?->consumo ?: $lote->quantidade_animais * 7);
        $porCocho = $consumoDiarioLote / $lote->cochos->count();

        $sensores = $lote->cochos->mapWithKeys(fn (Cocho $c) => [$c->id => $this->sensorSimulado($lote, $c)]);
        $consumoPorDia = [];

        foreach ($lote->cochos as $cocho) {
            $consumoPorDia = $this->historicoDoCocho($cocho, $sensores[$cocho->id], $dias, $porCocho, $consumoPorDia, max(1, (float) $this->option('acelerar')));
        }

        foreach ($consumoPorDia as $data => $kg) {
            LoteConsumoSnapshot::firstOrCreate(
                ['lote_id' => $lote->id, 'data' => $data],
                ['racao_id' => $lote->racao_id, 'quantidade_animais' => $lote->quantidade_animais, 'consumo_kg' => (int) round($kg), 'status_class' => 'status-success'],
            );
        }

        $this->planoSimulado($lote, $consumoDiarioLote);
        $this->pesagensSimuladas($lote, $consumoPorDia);
        $this->ocorrenciasSimuladas($lote);

        $this->info("{$lote->nome_lote}: {$lote->cochos->count()} cochos com {$dias} dias de leituras simuladas.");

        if (!$this->option('ao-vivo')) {
            return self::SUCCESS;
        }

        $intervalo = max(1, (int) $this->option('intervalo'));
        $acelerar = max(1, (float) $this->option('acelerar'));
        $this->info("Ao vivo: uma leitura por cocho a cada {$intervalo}s (Ctrl+C para parar).");

        while (true) {
            sleep($intervalo);
            foreach ($lote->cochos as $cocho) {
                $cocho->refresh();
                // Acelerado o cocho esvazia em poucas horas: repõe nos
                // horários do trato ou quando baixa de 25%.
                $capacidade = (float) $cocho->capacidade;
                $repor = (now()->minute === 0 && in_array(now()->hour, self::HORARIOS_REPOSICAO, true))
                    || (float) $cocho->nivel_atual < $capacidade * 0.25;
                $peso = $repor
                    ? $capacidade
                    : $this->proximoPeso((float) $cocho->nivel_atual, $capacidade, $porCocho * $acelerar, $intervalo / 60, now());
                // Oscilação da célula de carga (abaixo do ruído, não conta como consumo).
                $peso = round(max(0, min($capacidade, $peso + mt_rand(-8, 8) / 100)), 2);
                $comeu = $peso < (float) $cocho->nivel_atual - (float) config('iot.ruido_kg', 0.2);
                $iot->registrarLeitura($cocho, $sensores[$cocho->id]['balanca'], ['peso_kg' => $peso]);
                $iot->registrarLeitura($cocho, $sensores[$cocho->id]['distancia'], [
                    'distancia_cm' => $this->distanciaPorPeso($peso, (float) $cocho->capacidade),
                    'presenca_animal' => $comeu,
                    'distancia_presenca_cm' => $comeu ? mt_rand(30, 80) : null,
                ]);
            }
        }
    }

    private function planoSimulado(Lote $lote, float $consumoDiarioLote): void
    {
        if (!$lote->racao_id || Plano::where('lote_id', $lote->id)->exists()) {
            return;
        }

        $plano = new Plano([
            'lote_id' => $lote->id,
            'racao_id' => $lote->racao_id,
            'quantidade_recomendada' => round($consumoDiarioLote / max(1, $lote->quantidade_animais), 2),
            'frequencia_diaria' => count(self::HORARIOS_REPOSICAO),
            'data_inicio' => Carbon::today()->subDays(30)->toDateString(),
            'observacoes' => 'Trato às 06h e às 16h (dados simulados).',
            'ativo' => true,
        ]);
        $plano->fazenda_id = $lote->fazenda_id;
        $plano->save();
    }

    /**
     * Pesagem a cada 7 dias terminando hoje no peso médio atual do lote,
     * com ganho de ~1,1–1,4 kg/animal/dia. Consumo do período = consumo
     * simulado pelos sensores, então a conversão sai como na tela real.
     *
     * @param  array<string, float>  $consumoPorDia
     */
    private function pesagensSimuladas(Lote $lote, array $consumoPorDia): void
    {
        if (DesempenhoLote::where('lote_id', $lote->id)->exists() || $lote->peso_medio <= 0) {
            return;
        }

        $datas = collect(range(4, 0))->map(fn ($semanas) => Carbon::today()->subDays($semanas * 7));
        $ganhos = $datas->map(fn () => mt_rand(110, 140) / 100 * 7);
        $peso = (float) $lote->peso_medio - $ganhos->slice(1)->sum();
        $anterior = null;
        $valorKg = (float) ($lote->racao?->valor_kg ?? 0);

        foreach ($datas as $i => $data) {
            if ($anterior) {
                $peso += $ganhos[$i];
            }
            $consumo = $anterior
                ? collect($consumoPorDia)->filter(fn ($kg, $dia) => $dia > $anterior->toDateString() && $dia <= $data->toDateString())->sum()
                : 0;
            $ganhoTotal = $anterior ? round($ganhos[$i] * $lote->quantidade_animais, 2) : 0;

            DesempenhoLote::create([
                'lote_id' => $lote->id,
                'data_registro' => $data->toDateString(),
                'peso_medio_kg' => round($peso, 2),
                'quantidade_animais' => $lote->quantidade_animais,
                'consumo_kg' => round($consumo, 2),
                'fonte_consumo' => $anterior ? DesempenhoLote::FONTE_SENSOR : null,
                'custo_rs' => round($consumo * $valorKg, 2),
                'ganho_peso_kg' => $ganhoTotal,
                'eficiencia_alimentar' => $ganhoTotal > 0 ? round($consumo / $ganhoTotal, 2) : 0,
            ]);
            $anterior = $data;
        }
    }

    /** Dois óbitos no passado; a contagem atual do lote não muda (é o "depois"). */
    private function ocorrenciasSimuladas(Lote $lote): void
    {
        if (LoteOcorrencia::where('lote_id', $lote->id)->exists()) {
            return;
        }

        $atual = $lote->quantidade_animais;
        foreach ([[18, 'Acidente', 'Animal se feriu na cerca do piquete.'], [6, 'Causa natural', 'Encontrado morto pela manhã no curral.']] as $n => [$diasAtras, $causa, $obs]) {
            $depois = $atual + 1 - $n;
            LoteOcorrencia::create([
                'lote_id' => $lote->id,
                'tipo' => 'morte',
                'causa' => $causa,
                'quantidade' => 1,
                'quantidade_antes' => $depois + 1,
                'quantidade_depois' => $depois,
                'data_ocorrencia' => Carbon::today()->subDays($diasAtras)->toDateString(),
                'observacao' => $obs . ' (dado simulado)',
            ]);
        }
    }

    /** @return array{balanca: Sensor, distancia: Sensor} */
    private function sensorSimulado(Lote $lote, Cocho $cocho): array
    {
        $sufixo = $lote->id . '-' . str_pad((string) $cocho->numero, 2, '0', STR_PAD_LEFT);

        return [
            'balanca' => Sensor::updateOrCreate(
                ['cocho_id' => $cocho->id, 'tipo' => Sensor::TIPO_BALANCA],
                ['codigo' => 'SIM-BAL-' . $sufixo, 'ativo' => true],
            ),
            'distancia' => Sensor::updateOrCreate(
                ['cocho_id' => $cocho->id, 'tipo' => Sensor::TIPO_DISTANCIA],
                ['codigo' => 'SIM-DIST-' . $sufixo, 'ativo' => true, 'distancia_vazio_cm' => self::DISTANCIA_VAZIO_CM, 'distancia_cheio_cm' => self::DISTANCIA_CHEIO_CM],
            ),
        ];
    }

    /** Distância até a ração (cm) para um peso, com ruído do ultrassom. */
    private function distanciaPorPeso(float $peso, float $capacidade): float
    {
        $cheio = $capacidade > 0 ? $peso / $capacidade : 0;
        $distancia = self::DISTANCIA_VAZIO_CM - (self::DISTANCIA_VAZIO_CM - self::DISTANCIA_CHEIO_CM) * $cheio;

        return round(max(self::DISTANCIA_CHEIO_CM, min(self::DISTANCIA_VAZIO_CM, $distancia + mt_rand(-5, 5) / 10)), 2);
    }

    /** Grava as leituras do período e devolve o consumo acumulado por dia. */
    /** @param  array{balanca: Sensor, distancia: Sensor}  $sensores */
    private function historicoDoCocho(Cocho $cocho, array $sensores, int $dias, float $porCocho, array $consumoPorDia, float $acelerar = 1): array
    {
        $capacidade = (float) $cocho->capacidade;
        $agora = now()->startOfMinute();
        $inicio = Carbon::today()->subDays($dias - 1);

        ['balanca' => $sensor, 'distancia' => $distancia] = $sensores;
        LeituraIot::whereIn('sensor_id', [$sensor->id, $distancia->id])->delete();

        $quantidade = $capacidade;
        $linhas = [];
        $registrar = function (Carbon $quando, float $nova) use (&$linhas, &$quantidade, &$consumoPorDia, $cocho, $sensor, $distancia, $capacidade) {
            $nova = round(max(0, min($capacidade, $nova)), 2);
            $variacao = round($nova - $quantidade, 2);
            if ($variacao < 0) {
                $dia = $quando->toDateString();
                $consumoPorDia[$dia] = ($consumoPorDia[$dia] ?? 0) - $variacao;
            }
            $quantidade = $nova;
            $linhas[] = [
                'cocho_id' => $cocho->id,
                'sensor_id' => $sensor->id,
                'tipo_sensor' => Sensor::TIPO_BALANCA,
                'distancia_cm' => null,
                'presenca_animal' => null,
                'distancia_presenca_cm' => null,
                'peso_kg' => $nova,
                'nivel_percentual' => $capacidade > 0 ? round($nova / $capacidade * 100, 2) : null,
                'quantidade_kg' => $nova,
                'variacao_kg' => $variacao,
                'data_hora' => $quando->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            // Sensor de distância lê junto, e vê animal quando houve consumo.
            $cm = $this->distanciaPorPeso($nova, $capacidade);
            $linhas[] = [
                'cocho_id' => $cocho->id,
                'sensor_id' => $distancia->id,
                'tipo_sensor' => Sensor::TIPO_DISTANCIA,
                'distancia_cm' => $cm,
                'presenca_animal' => $variacao < 0,
                'distancia_presenca_cm' => $variacao < 0 ? mt_rand(30, 80) : null,
                'peso_kg' => null,
                'nivel_percentual' => $distancia->percentualPorDistancia($cm),
                'quantidade_kg' => null,
                'variacao_kg' => null,
                'data_hora' => $quando->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        };

        // Mais detalhe perto de agora: 30 min nos dias anteriores, 5 min
        // hoje e 10 s na última hora (período "1 hora" da tela). A última
        // hora já sai acelerada como o modo ao vivo, senão o gráfico de
        // 1 hora fica reto até o ao vivo encher a janela.
        $ultimaHora = $agora->copy()->subHour();
        for ($t = $inicio->copy(); $t->lessThanOrEqualTo($agora);) {
            $acelerado = $t->greaterThanOrEqualTo($ultimaHora);
            $passo = $acelerado ? 10 / 60 : ($t->isToday() ? 5 : 30);
            $ritmo = $acelerado ? $porCocho * $acelerar : $porCocho;

            if ($t->minute === 0 && $t->second === 0 && in_array($t->hour, self::HORARIOS_REPOSICAO, true)
                || ($acelerado && $quantidade < $capacidade * 0.25)) {
                $registrar($t->copy()->subSeconds(5), $this->proximoPeso($quantidade, $capacidade, $ritmo, $passo, $t));
                $registrar($t->copy(), $capacidade);
            } else {
                $peso = $this->proximoPeso($quantidade, $capacidade, $ritmo, $passo, $t);
                $registrar($t->copy(), $acelerado ? max(0, min($capacidade, $peso + mt_rand(-8, 8) / 100)) : $peso);
            }

            $t->addSeconds((int) round($passo * 60));
        }

        DB::transaction(function () use ($linhas, $cocho, $sensor, $distancia, $quantidade, $agora, $capacidade) {
            foreach (array_chunk($linhas, 500) as $lote) {
                LeituraIot::insert($lote);
            }
            $cocho->update(['nivel_atual' => $quantidade]);
            $sensor->forceFill(['ultima_leitura_em' => $agora, 'ultimo_valor' => $quantidade, 'ultimo_erro' => null, 'ultimo_erro_em' => null])->save();
            $distancia->forceFill(['ultima_leitura_em' => $agora, 'ultimo_valor' => $this->distanciaPorPeso($quantidade, $capacidade), 'ultimo_erro' => null, 'ultimo_erro_em' => null])->save();
        });

        return $consumoPorDia;
    }

    /**
     * Peso depois de $minutos de cocho: come mais de dia que de noite, em
     * bocadas de 0,4–1 kg (consumo de minuto a minuto ficaria abaixo do
     * ruído da balança e não apareceria nos gráficos).
     */
    private function proximoPeso(float $atual, float $capacidade, float $porCocho, float $minutos, Carbon $quando): float
    {
        $fatorHora = $quando->hour >= 6 && $quando->hour < 18 ? 1.6 : 0.4;
        $media = $porCocho / 1440 * $minutos * $fatorHora;
        $bocada = max($media * (mt_rand(70, 130) / 100), (40 + mt_rand(0, 60)) / 100);
        $consumo = mt_rand() / mt_getrandmax() < $media / $bocada ? $bocada : 0;

        return round(max(0, min($capacidade, $atual - $consumo)), 2);
    }
}
