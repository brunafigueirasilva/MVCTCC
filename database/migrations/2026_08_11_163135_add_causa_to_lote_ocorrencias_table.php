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
            // Causa da morte (Doença, Corte, Acidente etc.) — só preenchida
            // quando tipo = 'morte'. Quando a causa é "Doença" e o lote é
            // de isolamento, a doença específica continua indo na coluna
            // "doenca" já existente, pra abater do estoque daquela doença.
            $table->string('causa')->nullable()->after('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lote_ocorrencias', function (Blueprint $table) {
            $table->dropColumn('causa');
        });
    }
};
