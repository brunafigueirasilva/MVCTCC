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
        Schema::table('lote_ocorrencias', function (Blueprint $table) {
            // Nome da doença (ou o texto digitado quando o usuário escolhe
            // "Outra") — só preenchido quando tipo = 'doenca'.
            $table->string('doenca')->nullable()->after('tipo');

            // Pra onde os animais doentes foram movidos, quando aplicável.
            // O lote de destino não é apagado junto com o histórico, então
            // isso fica null se o lote de destino for excluído depois.
            $table->foreignId('lote_destino_id')->nullable()->after('quantidade_depois')
                ->constrained('lotes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lote_ocorrencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lote_destino_id');
            $table->dropColumn('doenca');
        });
    }
};
