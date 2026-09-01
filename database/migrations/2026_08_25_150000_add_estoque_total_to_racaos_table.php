<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * "Estoque Total" = capacidade máxima de armazenamento cadastrada pra
     * ração (em kg), usada pra calcular o nível/porcentagem real de cada
     * ração na tela de Estoque (estoque atual / estoque total), em vez do
     * proxy por dias-até-acabar usado antes. Nullable porque rações já
     * cadastradas não têm esse valor ainda.
     */
    public function up(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            $table->unsignedInteger('estoque_total')->nullable()->after('estoque');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            $table->dropColumn('estoque_total');
        });
    }
};
