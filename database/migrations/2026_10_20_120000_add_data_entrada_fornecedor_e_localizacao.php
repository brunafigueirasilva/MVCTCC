<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Campos de cadastro pedidos de volta (todos opcionais):
 *  - lotes.data_entrada: quando o lote entrou na fazenda. Os lotes que já
 *    existem recebem a data de cadastro (created_at), que era o que o
 *    sistema usava no lugar dela desde 2026_10_06_150100.
 *  - racaos.fornecedor: de quem a ração é comprada.
 *  - cochos.localizacao: onde o cocho fica (ex.: "Piquete 3, lado norte").
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('lotes', 'data_entrada')) {
            Schema::table('lotes', fn (Blueprint $table) => $table->date('data_entrada')->nullable()->after('peso_medio'));
            DB::table('lotes')->whereNull('data_entrada')->update(['data_entrada' => DB::raw('DATE(created_at)')]);
        }

        if (!Schema::hasColumn('racaos', 'fornecedor')) {
            Schema::table('racaos', fn (Blueprint $table) => $table->string('fornecedor', 150)->nullable()->after('valor_kg'));
        }

        if (!Schema::hasColumn('cochos', 'localizacao')) {
            Schema::table('cochos', fn (Blueprint $table) => $table->string('localizacao', 150)->nullable()->after('nome_cocho'));
        }
    }

    public function down(): void
    {
        Schema::table('cochos', fn (Blueprint $table) => $table->dropColumn('localizacao'));
        Schema::table('racaos', fn (Blueprint $table) => $table->dropColumn('fornecedor'));
        Schema::table('lotes', fn (Blueprint $table) => $table->dropColumn('data_entrada'));
    }
};
