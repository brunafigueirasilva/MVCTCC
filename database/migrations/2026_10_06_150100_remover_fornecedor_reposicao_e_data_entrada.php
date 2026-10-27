<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos sem uso no dia a dia, retirados do cadastro (Web, API e app):
 *  - racaos.fornecedor e racaos.reposicao (data de reposição);
 *  - lotes.data_entrada (o "entrou no sistema" passa a ser created_at).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            foreach (['fornecedor', 'reposicao'] as $coluna) {
                if (Schema::hasColumn('racaos', $coluna)) {
                    $table->dropColumn($coluna);
                }
            }
        });

        if (Schema::hasColumn('lotes', 'data_entrada')) {
            Schema::table('lotes', fn (Blueprint $table) => $table->dropColumn('data_entrada'));
        }
    }

    public function down(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            if (!Schema::hasColumn('racaos', 'fornecedor')) {
                $table->string('fornecedor', 100)->nullable()->after('valor_kg');
            }
            if (!Schema::hasColumn('racaos', 'reposicao')) {
                $table->date('reposicao')->nullable()->after('valor_kg');
            }
        });

        if (!Schema::hasColumn('lotes', 'data_entrada')) {
            Schema::table('lotes', fn (Blueprint $table) => $table->date('data_entrada')->nullable()->after('peso_medio'));
        }
    }
};
