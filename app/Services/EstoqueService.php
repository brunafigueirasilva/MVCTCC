<?php

namespace App\Services;

use App\Models\Cocho;
use App\Models\CochoConsumo;
use App\Models\EstoqueMovimentacao;
use App\Models\Racao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Único ponto do sistema que altera racaos.estoque. Toda alteração:
 *  1. trava a linha da ração (SELECT ... FOR UPDATE) — duas saídas
 *     simultâneas nunca leem o mesmo saldo;
 *  2. valida (quantidade > 0, nunca estoque negativo);
 *  3. grava a movimentação com saldo anterior/posterior;
 *  4. atualiza o estoque da ração;
 * tudo numa transação: se qualquer passo falhar, nada é gravado.
 *
 * Quem chama já precisa ter validado que a ração pertence ao usuário.
 */
class EstoqueService
{
    /** Entrada de ração (compra/reposição). */
    public function entrada(Racao $racao, float $quantidade, array $contexto = []): EstoqueMovimentacao
    {
        return $this->movimentar($racao, EstoqueMovimentacao::TIPO_ENTRADA, $quantidade, $contexto);
    }

    /** Saída de ração — recusa (422) se não houver estoque suficiente. */
    public function saida(Racao $racao, float $quantidade, array $contexto = []): EstoqueMovimentacao
    {
        return $this->movimentar($racao, EstoqueMovimentacao::TIPO_SAIDA, $quantidade, $contexto);
    }

    /**
     * Ajuste para um saldo absoluto (ex.: edição do estoque na tela de
     * Rações ou cadastro inicial). Não grava nada se o saldo não mudou.
     */
    public function ajustarPara(Racao $racao, float $novoSaldo, array $contexto = []): ?EstoqueMovimentacao
    {
        $novoSaldo = round($novoSaldo, 2);

        if ($novoSaldo < 0) {
            throw ValidationException::withMessages(['estoque' => 'O estoque não pode ser negativo.']);
        }

        return DB::transaction(function () use ($racao, $novoSaldo, $contexto) {
            $travada = Racao::whereKey($racao->id)->lockForUpdate()->firstOrFail();
            $saldoAnterior = round((float) $travada->estoque, 2);

            if (abs($novoSaldo - $saldoAnterior) < 0.005) {
                return null;
            }

            return $this->registrar($travada, EstoqueMovimentacao::TIPO_AJUSTE, abs($novoSaldo - $saldoAnterior), $saldoAnterior, $novoSaldo, $contexto);
        });
    }

    /**
     * Abastecimento de cochos: saída da ração + aumento do nível de cada
     * cocho, numa única transação. $porCocho = [cocho_id => kg aplicados].
     */
    public function abastecerCochos(Racao $racao, array $porCocho, array $contexto = []): array
    {
        $porCocho = array_filter(array_map(fn ($kg) => round((float) $kg, 2), $porCocho), fn ($kg) => $kg > 0);
        $total = round(array_sum($porCocho), 2);

        if ($total <= 0) {
            throw ValidationException::withMessages(['quantidade' => 'Informe uma quantidade maior que zero.']);
        }

        return DB::transaction(function () use ($racao, $porCocho, $total, $contexto) {
            $movimentacoes = [];

            foreach ($porCocho as $cochoId => $kg) {
                $cocho = Cocho::whereKey($cochoId)->lockForUpdate()->firstOrFail();
                $espacoLivre = round(max(0, $cocho->capacidade - $cocho->nivel_atual), 2);

                if ($kg > $espacoLivre + 0.005) {
                    throw ValidationException::withMessages([
                        'quantidade' => "Não cabem " . $this->kg($kg) . " no cocho \"{$cocho->nome_cocho}\" — o espaço livre é de " . $this->kg($espacoLivre) . '.',
                    ]);
                }

                $cocho->update(['nivel_atual' => round($cocho->nivel_atual + $kg, 2)]);

                $movimentacoes[] = $this->saida($racao, $kg, $contexto + [
                    'origem' => 'abastecimento_cocho',
                    'cocho_id' => $cocho->id,
                    'lote_id' => $cocho->lote_id,
                ]);
            }

            return $movimentacoes;
        });
    }

    /**
     * Consumo registrado num cocho: diminui o nível do cocho e grava o
     * histórico (cocho_consumos), numa transação com o cocho travado. Não
     * mexe no armazém — essa ração já saiu do estoque no abastecimento.
     * Ração do registro: a do lote; sem ela, a do último abastecimento.
     */
    public function registrarConsumoCocho(Cocho $cocho, float $quantidade, array $contexto = []): CochoConsumo
    {
        $quantidade = round($quantidade, 2);

        if ($quantidade <= 0) {
            throw ValidationException::withMessages(['quantidade' => 'Informe uma quantidade maior que zero.']);
        }

        return DB::transaction(function () use ($cocho, $quantidade, $contexto) {
            $travado = Cocho::whereKey($cocho->id)->with('lote')->lockForUpdate()->firstOrFail();
            $nivelAnterior = round((float) $travado->nivel_atual, 2);

            if ($quantidade > $nivelAnterior + 0.005) {
                throw ValidationException::withMessages([
                    'quantidade' => "O cocho \"{$travado->nome_cocho}\" tem só " . $this->kg($nivelAnterior) . ' de ração — o consumo não pode ser maior que isso.',
                ]);
            }

            $nivelPosterior = round(max(0, $nivelAnterior - $quantidade), 2);
            $travado->update(['nivel_atual' => $nivelPosterior]);

            $racaoId = $travado->lote?->racao_id
                ?? EstoqueMovimentacao::where('cocho_id', $travado->id)->where('origem', 'abastecimento_cocho')->latest('data_movimentacao')->value('racao_id');
            $racao = $racaoId ? Racao::find($racaoId) : null;

            return CochoConsumo::create([
                'fazenda_id' => $travado->lote->fazenda_id,
                'lote_id' => $travado->lote_id,
                'cocho_id' => $travado->id,
                'racao_id' => $racao?->id,
                'cadastro_id' => $contexto['cadastro_id'] ?? session('usuario_id'),
                'origem' => $contexto['origem'] ?? CochoConsumo::ORIGEM_MANUAL,
                'quantidade' => $quantidade,
                'nivel_anterior' => $nivelAnterior,
                'nivel_posterior' => $nivelPosterior,
                'valor_unitario' => $racao?->valor_kg,
                'observacao' => $contexto['observacao'] ?? null,
                'data_consumo' => $contexto['data_consumo'] ?? now(),
            ]);
        });
    }

    private function movimentar(Racao $racao, string $tipo, float $quantidade, array $contexto): EstoqueMovimentacao
    {
        $quantidade = round($quantidade, 2);

        if ($quantidade <= 0) {
            throw ValidationException::withMessages(['quantidade' => 'Informe uma quantidade maior que zero.']);
        }

        return DB::transaction(function () use ($racao, $tipo, $quantidade, $contexto) {
            $travada = Racao::whereKey($racao->id)->lockForUpdate()->firstOrFail();
            $saldoAnterior = round((float) $travada->estoque, 2);
            $saldoPosterior = $tipo === EstoqueMovimentacao::TIPO_ENTRADA
                ? round($saldoAnterior + $quantidade, 2)
                : round($saldoAnterior - $quantidade, 2);

            if ($saldoPosterior < 0) {
                throw ValidationException::withMessages([
                    'quantidade' => 'Não há ração suficiente no estoque: há só ' . $this->kg($saldoAnterior) . " de \"{$travada->nome_racao}\". Reduza a quantidade ou registre uma entrada de ração.",
                ]);
            }

            return $this->registrar($travada, $tipo, $quantidade, $saldoAnterior, $saldoPosterior, $contexto);
        });
    }

    private function registrar(Racao $racao, string $tipo, float $quantidade, float $saldoAnterior, float $saldoPosterior, array $contexto): EstoqueMovimentacao
    {
        $movimentacao = EstoqueMovimentacao::create([
            'fazenda_id' => $racao->fazenda_id,
            'racao_id' => $racao->id,
            'cadastro_id' => $contexto['cadastro_id'] ?? session('usuario_id'),
            'lote_id' => $contexto['lote_id'] ?? null,
            'cocho_id' => $contexto['cocho_id'] ?? null,
            'tipo' => $tipo,
            'origem' => $contexto['origem'] ?? 'manual',
            'quantidade' => $quantidade,
            'saldo_anterior' => $saldoAnterior,
            'saldo_posterior' => $saldoPosterior,
            'valor_unitario' => $contexto['valor_unitario'] ?? $racao->valor_kg,
            'observacao' => $contexto['observacao'] ?? null,
            'data_movimentacao' => $contexto['data_movimentacao'] ?? now(),
        ]);

        $racao->update(['estoque' => $saldoPosterior]);

        return $movimentacao;
    }

    private function kg(float $valor): string
    {
        return number_format($valor, 2, ',', '.') . ' kg';
    }
}
