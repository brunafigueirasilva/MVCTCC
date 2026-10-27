<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Última trava no próprio banco (além da validação do Laravel e do
 * EstoqueService): nenhuma quantidade física fica negativa, mesmo que algum
 * código futuro grave direto na tabela.
 *
 *  - racaos: estoque >= 0, preço >= 0;
 *  - cochos: capacidade > 0 e 0 <= nível <= capacidade;
 *  - lotes: animais >= 0, peso médio >= 0;
 *  - movimentações de estoque, consumos de cocho e ordenhas: quantidade > 0;
 *    saldo depois da movimentação >= 0.
 *
 * CHECK só em MySQL/MariaDB (o SQLite dos testes não altera CHECK em tabela
 * existente; lá as regras continuam garantidas pelo backend).
 */
return new class extends Migration
{
    private const RESTRICOES = [
        'racaos' => [
            'racaos_estoque_nao_negativo' => 'estoque >= 0',
            'racaos_valor_kg_nao_negativo' => 'valor_kg IS NULL OR valor_kg >= 0',
        ],
        'cochos' => [
            'cochos_capacidade_positiva' => 'capacidade > 0',
            'cochos_nivel_na_capacidade' => 'nivel_atual >= 0 AND nivel_atual <= capacidade',
        ],
        'lotes' => [
            'lotes_animais_nao_negativo' => 'quantidade_animais >= 0',
            'lotes_peso_nao_negativo' => 'peso_medio >= 0',
        ],
        'estoque_movimentacoes' => [
            'estoque_mov_quantidade_positiva' => 'quantidade > 0',
            'estoque_mov_saldo_nao_negativo' => 'saldo_posterior >= 0',
        ],
        'cocho_consumos' => [
            'cocho_consumos_quantidade_positiva' => 'quantidade > 0',
        ],
        'ordenhas' => [
            'ordenhas_litros_positivos' => 'quantidade_litros > 0',
        ],
    ];

    private function bancoSuportado(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    public function up(): void
    {
        if (!$this->bancoSuportado()) {
            return;
        }

        // Dado que já viola a regra: não apagamos nada por conta própria — a
        // migration para e mostra onde está, para corrigir e rodar de novo.
        $problemas = [];
        foreach (self::RESTRICOES as $tabela => $regras) {
            foreach ($regras as $nome => $condicao) {
                $total = DB::table($tabela)->whereRaw("NOT ({$condicao})")->count();
                if ($total > 0) {
                    $problemas[] = "{$tabela}: {$total} registro(s) fora da regra \"{$condicao}\"";
                }
            }
        }

        if ($problemas) {
            throw new RuntimeException('Corrija estes dados antes de aplicar as restrições: ' . implode('; ', $problemas) . '.');
        }

        foreach (self::RESTRICOES as $tabela => $regras) {
            foreach ($regras as $nome => $condicao) {
                DB::statement("ALTER TABLE {$tabela} ADD CONSTRAINT {$nome} CHECK ({$condicao})");
            }
        }
    }

    public function down(): void
    {
        if (!$this->bancoSuportado()) {
            return;
        }

        foreach (self::RESTRICOES as $tabela => $regras) {
            foreach (array_keys($regras) as $nome) {
                DB::statement("ALTER TABLE {$tabela} DROP CONSTRAINT IF EXISTS {$nome}");
            }
        }
    }
};
