<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            // Ração "vinculada" ao lote — antes disso era só um palpite
            // recalculado a cada carregamento da tela de Plano Alimentar
            // (mesma fazenda/tipo/sexo), sem nada salvo. Agora é um vínculo
            // explícito que o usuário escolhe (e pode trocar/remover) na
            // tela de Plano Alimentar. Sempre nasce nulo — mesmo quando só
            // existe uma ração compatível, o vínculo só é criado quando o
            // usuário escolhe manualmente na tela (nunca "adivinhado"
            // automaticamente), então "Sem ração vinculada" é o estado real
            // de qualquer lote até que isso aconteça.
            $table->foreignId('racao_id')->nullable()->after('fazenda_id')
                ->constrained('racaos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('racao_id');
        });
    }
};
