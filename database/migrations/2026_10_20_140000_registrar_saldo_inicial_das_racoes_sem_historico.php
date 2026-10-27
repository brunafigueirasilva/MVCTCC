<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rações cadastradas antes do histórico de estoque existir têm saldo em
 * racaos.estoque sem nenhuma movimentação que o explique — o relatório de
 * estoque (entradas/saídas) e a tela de movimentações não batiam com o
 * saldo. Cada uma ganha UMA entrada de abertura com o saldo atual (mesma
 * origem do cadastro de ração: 'cadastro_racao'), datada do cadastro dela.
 * Nenhum saldo muda; só o histórico passa a explicar o que já existia.
 */
return new class extends Migration
{
    public function up(): void
    {
        $semHistorico = DB::table('racaos')
            ->where('estoque', '>', 0)
            ->whereNotNull('fazenda_id')
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('estoque_movimentacoes')->whereColumn('estoque_movimentacoes.racao_id', 'racaos.id'))
            ->get(['id', 'fazenda_id', 'estoque', 'valor_kg', 'created_at']);

        foreach ($semHistorico as $racao) {
            DB::table('estoque_movimentacoes')->insert([
                'fazenda_id' => $racao->fazenda_id,
                'racao_id' => $racao->id,
                'tipo' => 'entrada',
                'origem' => 'cadastro_racao',
                'quantidade' => $racao->estoque,
                'saldo_anterior' => 0,
                'saldo_posterior' => $racao->estoque,
                'valor_unitario' => $racao->valor_kg,
                'observacao' => 'Saldo existente antes do histórico de estoque.',
                'data_movimentacao' => $racao->created_at ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('estoque_movimentacoes')
            ->where('origem', 'cadastro_racao')
            ->where('observacao', 'Saldo existente antes do histórico de estoque.')
            ->delete();
    }
};
