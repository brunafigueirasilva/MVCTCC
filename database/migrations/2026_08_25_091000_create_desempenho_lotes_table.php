<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabela consultada por App\Models\Lote::getTabelaDetalhada() /
     * getKpisGlobais() / getConsumoDiarioGrafico(), usadas pela API de
     * dashboard (LoteController::getDashboardData()). Faltava por completo
     * no banco — as consultas já tratam ausência de linhas (IFNULL/SUM),
     * então a tabela pode nascer vazia sem quebrar o endpoint.
     */
    public function up(): void
    {
        Schema::create('desempenho_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->date('data_registro');
            $table->decimal('consumo_kg', 10, 2)->default(0);
            $table->decimal('custo_rs', 10, 2)->default(0);
            $table->decimal('ganho_peso_kg', 10, 2)->default(0);
            $table->decimal('desperdicio_porcentagem', 5, 2)->default(0);
            $table->decimal('eficiencia_alimentar', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['lote_id', 'data_registro']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desempenho_lotes');
    }
};
