<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * De onde veio o consumo usado na conversão alimentar de cada pesagem:
 * 'sensor' (medido nos cochos), 'fornecido' (ração colocada nos cochos) ou
 * 'plano' (consumo planejado — estimativa). Ver App\Services\ConsumoLoteService.
 *
 * As pesagens que já existem foram calculadas pelo histórico do plano
 * alimentar, então ficam marcadas como 'plano' (estimativa) — é o que elas
 * realmente são; antes a tela as chamava de "conversão real".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('desempenho_lotes', function (Blueprint $table) {
            $table->string('fonte_consumo', 20)->nullable()->after('consumo_kg');
        });

        DB::table('desempenho_lotes')->where('consumo_kg', '>', 0)->update(['fonte_consumo' => 'plano']);
    }

    public function down(): void
    {
        Schema::table('desempenho_lotes', function (Blueprint $table) {
            $table->dropColumn('fonte_consumo');
        });
    }
};
