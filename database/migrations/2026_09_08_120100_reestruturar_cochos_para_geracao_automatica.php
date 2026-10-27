<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cochos passam a ser criados/removidos automaticamente a partir de
     * "Quantidade de Cochos" no Lote (ver LoteController::sincronizarCochos()),
     * em vez de um cadastro manual por cocho:
     *   - "numero": posição do cocho dentro do LOTE (1, 2, 3...) — é o que
     *     permite aumentar/diminuir a quantidade depois sem embaralhar ou
     *     duplicar os já existentes.
     *   - "capacidade" ganha um DEFAULT (0) — um cocho recém-criado
     *     automaticamente ainda não tem capacidade configurada; o status
     *     "Indefinido" (CochoController::statusAbastecimento()) já trata
     *     capacidade 0 como "ainda não configurado", então nenhuma lógica
     *     nova precisa entender esse estado.
     *   - "sensor" vira opcional — não tem como o sistema inventar um
     *     código de sensor sozinho; o usuário preenche depois, editando o
     *     cocho específico.
     */
    public function up(): void
    {
        Schema::table('cochos', function (Blueprint $table) {
            $table->unsignedInteger('numero')->nullable()->after('lote_id');
            $table->decimal('capacidade', 10, 2)->default(0)->change();
            $table->string('sensor')->nullable()->change();
        });

        // Backfill dos cochos já cadastrados manualmente: numera por lote na
        // ordem em que foram criados e já renomeia pro novo padrão
        // "<nome do lote>-01", "<nome do lote>-02"... — todo cocho do
        // sistema passa a seguir a mesma identificação, não só os novos.
        $cochosPorLote = DB::table('cochos')->orderBy('id')->get()->groupBy('lote_id');

        foreach ($cochosPorLote as $loteId => $cochos) {
            $lote = DB::table('lotes')->where('id', $loteId)->first();
            if (!$lote) {
                continue;
            }

            foreach ($cochos->values() as $indice => $cocho) {
                $numero = $indice + 1;

                DB::table('cochos')->where('id', $cocho->id)->update([
                    'numero' => $numero,
                    'nome_cocho' => $lote->nome_lote . '-' . str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('cochos', function (Blueprint $table) {
            $table->dropColumn('numero');
            $table->decimal('capacidade', 10, 2)->default(null)->change();
            $table->string('sensor')->nullable(false)->change();
        });
    }
};
