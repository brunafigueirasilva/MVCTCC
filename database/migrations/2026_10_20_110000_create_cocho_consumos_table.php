<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consumo registrado nos cochos (sem sensor): o produtor informa quanto os
 * animais comeram, ou baixa o "Nível Atual" na edição do cocho. Cada
 * registro diminui cochos.nivel_atual e fica no histórico — antes a edição
 * do nível mudava a quantidade do cocho sem deixar rastro (e um aumento
 * "criava" ração que nunca tinha saído do armazém).
 *
 * Não mexe no estoque do armazém: essa ração já saiu dele quando o cocho
 * foi abastecido (estoque_movimentacoes, origem abastecimento_cocho).
 * Histórico preservado: apagar cocho/lote/ração só desvincula (SET NULL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cocho_consumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fazenda_id')->constrained('fazendas')->cascadeOnDelete();
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
            $table->foreignId('cocho_id')->nullable()->constrained('cochos')->nullOnDelete();
            $table->foreignId('racao_id')->nullable()->constrained('racaos')->nullOnDelete();
            $table->foreignId('cadastro_id')->nullable()->constrained('cadastros')->nullOnDelete();
            // manual (Registrar consumo) | ajuste_nivel (nível baixado na edição)
            $table->string('origem', 30)->default('manual');
            $table->decimal('quantidade', 10, 2);
            $table->decimal('nivel_anterior', 10, 2);
            $table->decimal('nivel_posterior', 10, 2);
            $table->decimal('valor_unitario', 10, 2)->nullable();
            $table->string('observacao')->nullable();
            $table->dateTime('data_consumo');
            $table->timestamps();

            $table->index(['lote_id', 'data_consumo']);
            $table->index(['fazenda_id', 'data_consumo']);
            $table->index(['cocho_id', 'data_consumo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cocho_consumos');
    }
};
