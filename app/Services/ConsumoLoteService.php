<?php

namespace App\Services;

use App\Models\CochoConsumo;
use App\Models\DesempenhoLote;
use App\Models\EstoqueMovimentacao;
use App\Models\LeituraIot;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use Carbon\CarbonInterface;

/**
 * Quanto um lote comeu num período — da fonte MAIS CONFIÁVEL disponível,
 * sempre dizendo qual foi (o produtor precisa saber se o número é medido
 * ou estimado):
 *
 *  1. 'sensor'    — consumo MEDIDO pelas balanças/sensores dos cochos
 *                   (todos os cochos do lote já enviavam leituras no início
 *                   do período). É o único consumo de fato "real".
 *  2. 'fornecido' — ração COLOCADA nos cochos do lote (abastecimentos
 *                   registrados no estoque). Real, mas inclui o que sobrou
 *                   no cocho — aproxima o consumo por cima.
 *  3. 'plano'     — consumo PLANEJADO (histórico diário do plano alimentar,
 *                   só dos dias com ração vinculada). É uma estimativa.
 *
 * Período: (início, fim] — o dia de início NÃO entra (ele já foi contado no
 * período anterior, que termina nele). Custo: preço cadastrado da ração
 * (no abastecimento, o preço gravado no momento da saída) — nunca um preço
 * médio fixo. Sem nenhuma fonte, kg = 0 e fonte = null.
 */
class ConsumoLoteService
{
    public function __construct(private CochoIotService $iot)
    {
    }

    /** @return array{kg: float, custo: float, fonte: ?string} */
    public function noPeriodo(Lote $lote, CarbonInterface $inicio, CarbonInterface $fim): array
    {
        $depoisDe = $inicio->copy()->endOfDay();
        $ate = $fim->copy()->endOfDay();

        return $this->medidoPorSensores($lote, $depoisDe, $ate)
            ?? $this->registradoNosCochos($lote, $depoisDe, $ate)
            ?? $this->fornecidoAosCochos($lote, $depoisDe, $ate)
            ?? $this->planejado($lote, $depoisDe, $ate)
            ?? ['kg' => 0.0, 'custo' => 0.0, 'fonte' => null];
    }

    private function medidoPorSensores(Lote $lote, CarbonInterface $depoisDe, CarbonInterface $ate): ?array
    {
        $cochos = $lote->cochos()->get();

        if ($cochos->isEmpty()) {
            return null;
        }

        // Só vale como "medido" se TODOS os cochos do lote já tinham leitura
        // no início do período — com parte dos cochos sem sensor, o número
        // ficaria menor que o consumo verdadeiro.
        foreach ($cochos as $cocho) {
            $cobertoDesdeOInicio = LeituraIot::where('cocho_id', $cocho->id)
                ->where('data_hora', '<=', $depoisDe)
                ->whereNotNull('quantidade_kg')
                ->exists();

            if (!$cobertoDesdeOInicio) {
                return null;
            }
        }

        $kg = round($cochos->sum(fn ($cocho) => $this->iot->consumoDesde($cocho, $depoisDe, $ate)), 2);
        $precoKg = (float) ($lote->racao?->valor_kg ?? 0);

        return ['kg' => $kg, 'custo' => round($kg * $precoKg, 2), 'fonte' => DesempenhoLote::FONTE_SENSOR];
    }

    /** Consumo informado pelo produtor nos cochos do lote (cocho_consumos). */
    private function registradoNosCochos(Lote $lote, CarbonInterface $depoisDe, CarbonInterface $ate): ?array
    {
        $consumos = CochoConsumo::where('lote_id', $lote->id)
            ->where('data_consumo', '>', $depoisDe)
            ->where('data_consumo', '<=', $ate)
            ->get(['quantidade', 'valor_unitario']);

        if ($consumos->isEmpty()) {
            return null;
        }

        return [
            'kg' => round((float) $consumos->sum('quantidade'), 2),
            'custo' => round((float) $consumos->sum(fn ($c) => $c->quantidade * ($c->valor_unitario ?? 0)), 2),
            'fonte' => DesempenhoLote::FONTE_REGISTRADO,
        ];
    }

    private function fornecidoAosCochos(Lote $lote, CarbonInterface $depoisDe, CarbonInterface $ate): ?array
    {
        $abastecimentos = EstoqueMovimentacao::where('lote_id', $lote->id)
            ->where('tipo', EstoqueMovimentacao::TIPO_SAIDA)
            ->where('origem', 'abastecimento_cocho')
            ->where('data_movimentacao', '>', $depoisDe)
            ->where('data_movimentacao', '<=', $ate)
            ->get(['quantidade', 'valor_unitario']);

        if ($abastecimentos->isEmpty()) {
            return null;
        }

        return [
            'kg' => round((float) $abastecimentos->sum('quantidade'), 2),
            'custo' => round((float) $abastecimentos->sum(fn ($m) => $m->quantidade * ($m->valor_unitario ?? 0)), 2),
            'fonte' => DesempenhoLote::FONTE_FORNECIDO,
        ];
    }

    private function planejado(Lote $lote, CarbonInterface $depoisDe, CarbonInterface $ate): ?array
    {
        $snapshots = LoteConsumoSnapshot::with('racao:id,valor_kg')
            ->where('lote_id', $lote->id)
            ->whereNotNull('racao_id')
            ->where('consumo_kg', '>', 0)
            ->whereDate('data', '>', $depoisDe->toDateString())
            ->whereDate('data', '<=', $ate->toDateString())
            ->get();

        if ($snapshots->isEmpty()) {
            return null;
        }

        return [
            'kg' => round((float) $snapshots->sum('consumo_kg'), 2),
            'custo' => round((float) $snapshots->sum(fn ($s) => $s->consumo_kg * (float) ($s->racao?->valor_kg ?? 0)), 2),
            'fonte' => DesempenhoLote::FONTE_PLANO,
        ];
    }
}
