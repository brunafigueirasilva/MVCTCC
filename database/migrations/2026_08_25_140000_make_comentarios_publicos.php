<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A página de Comentários saiu do menu interno (autenticado) e virou a
     * página pública "Avaliações", acessada pela landing page
     * (projetovideo.blade.php) — visitantes sem conta também podem avaliar.
     * cadastro_id vira opcional (preenchido só quando quem avalia está
     * logado) e "nome" guarda o nome digitado por quem não está.
     */
    public function up(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropForeign(['cadastro_id']);
        });

        Schema::table('comentarios', function (Blueprint $table) {
            $table->foreignId('cadastro_id')->nullable()->change();
            $table->string('nome', 150)->nullable()->after('cadastro_id');
            $table->foreign('cadastro_id')->references('id')->on('cadastros')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropForeign(['cadastro_id']);
            $table->dropColumn('nome');
        });

        Schema::table('comentarios', function (Blueprint $table) {
            $table->foreignId('cadastro_id')->nullable(false)->change();
            $table->foreign('cadastro_id')->references('id')->on('cadastros')->cascadeOnDelete();
        });
    }
};
