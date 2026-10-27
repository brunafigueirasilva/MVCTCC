<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A Conversão Alimentar (kg ração / kg ganho) comparava o peso da
     * pesagem atual com Lote::peso_medio — um campo editável livremente na
     * tela de Gestão de Lotes (fora do fluxo de Pesagem). Editar o peso do
     * lote por lá corrompia o "peso anterior" usado no próximo cálculo, sem
     * deixar rastro. Estas colunas guardam o peso e a quantidade de animais
     * REALMENTE registrados em CADA pesagem, para que o histórico vire a
     * única fonte de verdade (ver PlanoController::registrarPesagem()).
     */
    public function up(): void
    {
        Schema::table('desempenho_lotes', function (Blueprint $table) {
            $table->decimal('peso_medio_kg', 10, 2)->default(0)->after('lote_id');
            $table->unsignedInteger('quantidade_animais')->default(0)->after('peso_medio_kg');
        });
    }

    public function down(): void
    {
        Schema::table('desempenho_lotes', function (Blueprint $table) {
            $table->dropColumn(['peso_medio_kg', 'quantidade_animais']);
        });
    }
};
