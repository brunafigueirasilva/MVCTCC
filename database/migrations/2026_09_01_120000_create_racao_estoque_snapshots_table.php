<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Histórico diário do nível de estoque de cada ração — mesmo padrão de
     * lote_consumo_snapshots (Plano Alimentar) e desempenho_lotes
     * (Conversão Alimentar): não existe follow-up manual de "quanto tinha
     * de estoque em tal dia", então a única forma de mostrar uma evolução
     * real (sem inventar dado) é passar a capturar um snapshot por dia a
     * partir de agora, toda vez que a tela de Rações/Estoque é visitada
     * (ver RacaoController/EstoqueController::capturarSnapshotEstoqueDeHoje).
     */
    public function up(): void
    {
        Schema::create('racao_estoque_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racao_id')->constrained('racaos')->cascadeOnDelete();
            $table->date('data');
            $table->decimal('estoque_kg', 10, 2)->default(0);
            $table->unsignedTinyInteger('percentual_estoque')->nullable();
            $table->string('status_class', 20)->default('ok');
            $table->timestamps();

            $table->unique(['racao_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('racao_estoque_snapshots');
    }
};
