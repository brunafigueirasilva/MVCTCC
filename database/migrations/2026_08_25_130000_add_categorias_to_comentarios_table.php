<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Avaliação opcional por categoria ("O que você achou?"), separada da
     * nota geral — todas nullable porque o usuário pode enviar só a nota
     * geral e o comentário, sem detalhar categorias.
     */
    public function up(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->unsignedTinyInteger('nota_facilidade')->nullable()->after('nota');
            $table->unsignedTinyInteger('nota_visual')->nullable()->after('nota_facilidade');
            $table->unsignedTinyInteger('nota_desempenho')->nullable()->after('nota_visual');
            $table->unsignedTinyInteger('nota_organizacao')->nullable()->after('nota_desempenho');
            $table->unsignedTinyInteger('nota_funcionalidades')->nullable()->after('nota_organizacao');
            $table->unsignedTinyInteger('nota_acessibilidade')->nullable()->after('nota_funcionalidades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropColumn([
                'nota_facilidade',
                'nota_visual',
                'nota_desempenho',
                'nota_organizacao',
                'nota_funcionalidades',
                'nota_acessibilidade',
            ]);
        });
    }
};
