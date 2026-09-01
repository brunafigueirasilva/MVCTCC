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
            // Litros de leite produzidos — só preenchido quando tipo =
            // 'ordenha'. Não é um campo de contagem de animais (por isso
            // fica fora de quantidade/quantidade_antes/quantidade_depois,
            // que continuam representando cabeças do lote).
            $table->decimal('quantidade_litros', 8, 2)->nullable()->after('quantidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lote_ocorrencias', function (Blueprint $table) {
            $table->dropColumn('quantidade_litros');
        });
    }
};
