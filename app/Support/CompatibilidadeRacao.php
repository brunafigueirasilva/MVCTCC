<?php

namespace App\Support;

use App\Models\Lote;
use App\Models\Racao;
use Illuminate\Support\Collection;

/**
 * Regra ÚNICA de compatibilidade ração × lote — usada pela lista de
 * opções do "vincular ração" (web e API), pelo endpoint
 * GET /api/lotes/{id}/racoes-compativeis e pelas validações de vínculo e
 * de plano alimentar. O front (web ou app) nunca decide sozinho: só mostra
 * o que esta regra devolve, e o servidor valida de novo ao salvar.
 *
 * Compatível quando:
 *  - é da MESMA fazenda do lote;
 *  - está ativa (status != 'inativa');
 *  - é para o MESMO tipo de animal (Suíno só com Suíno, Bovino só com
 *    Bovino...);
 *  - o sexo combina (ração "Misto" ou sem sexo serve a qualquer lote;
 *    ração só de Fêmea/Macho serve só a lote desse sexo — não a lote Misto);
 *  - a finalidade combina (sem finalidade = ração de uso geral).
 */
class CompatibilidadeRacao
{
    public const MENSAGEM = 'Esta ração não é compatível com o lote selecionado.';

    public static function compativel(Racao $racao, Lote $lote): bool
    {
        if ((int) $racao->fazenda_id !== (int) $lote->fazenda_id) {
            return false;
        }

        if (($racao->status ?? 'ativa') === 'inativa') {
            return false;
        }

        if ($racao->tipo !== $lote->tipo_animal) {
            return false;
        }

        if (!self::sexoCombina($racao->sexo, $lote->sexo)) {
            return false;
        }

        return self::combina($racao->finalidade, $lote->finalidade);
    }

    /**
     * Ração sem sexo ou "Misto" serve para qualquer lote. Ração de um sexo
     * só (Fêmea/Macho) serve apenas para lote daquele sexo — um lote Misto
     * tem machos E fêmeas, então só aceita ração Misto ou de uso geral.
     */
    private static function sexoCombina(?string $daRacao, ?string $doLote): bool
    {
        if (!$daRacao || $daRacao === 'Misto' || !$doLote) {
            return true;
        }

        return $daRacao === $doLote;
    }

    /**
     * Rações compatíveis, das mais adequadas para as menos: primeiro as que
     * batem exatamente sexo e finalidade do lote (ex.: lote Bovino Fêmea
     * Leite → "Ração Leite Bovino" Fêmea/Leite antes de uma de uso geral).
     */
    public static function filtrar(Lote $lote, Collection $racoes): Collection
    {
        return $racoes
            ->filter(fn (Racao $r) => self::compativel($r, $lote))
            ->sortBy([
                fn (Racao $a, Racao $b) => self::pontuacao($b, $lote) <=> self::pontuacao($a, $lote),
                fn (Racao $a, Racao $b) => strcasecmp((string) $a->nome_racao, (string) $b->nome_racao),
            ])
            ->values();
    }

    /** 2 = sexo e finalidade exatos; 1 = um dos dois; 0 = só genérica. */
    public static function pontuacao(Racao $racao, Lote $lote): int
    {
        return (int) ($racao->sexo && $racao->sexo === $lote->sexo)
            + (int) ($racao->finalidade && $racao->finalidade === $lote->finalidade);
    }

    /** Campo vazio combina com qualquer valor (ração de uso geral). */
    private static function combina(?string $daRacao, ?string $doLote): bool
    {
        if (!$daRacao || !$doLote) {
            return true;
        }

        return $daRacao === $doLote;
    }
}
