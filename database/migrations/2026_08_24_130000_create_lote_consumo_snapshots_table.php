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
        // Registro histórico do plano alimentar: como a tela de Plano
        // Alimentar não tem mais um "registrar alimentação" manual (calcula
        // tudo direto da ração vinculada, ver PlanoController), não sobrava
        // nenhum jeito de mostrar "últimos 7 dias" ou "vs mês anterior" com
        // dado real — sempre era o mesmo valor projetado repetido. Esta
        // tabela grava, uma vez por lote por dia (upsert a cada visita à
        // tela, ver PlanoController::capturarSnapshotsDeHoje()), o que foi
        // calculado NAQUELE dia, sem reintroduzir registro manual nenhum.
        Schema::create('lote_consumo_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->foreignId('racao_id')->nullable()->constrained('racaos')->nullOnDelete();
            $table->date('data');
            $table->unsignedInteger('quantidade_animais')->default(0);
            $table->unsignedInteger('consumo_kg')->default(0);
            $table->string('status_class', 30)->default('status-warning');
            $table->timestamps();

            // Um snapshot por lote por dia — visitas repetidas no mesmo dia
            // atualizam a linha existente (updateOrCreate), não duplicam.
            $table->unique(['lote_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_consumo_snapshots');
    }
};
