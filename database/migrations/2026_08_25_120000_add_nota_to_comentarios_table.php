<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Nota de 1 a 5 estrelas, adicionada junto com o redesenho da página
     * "Comentários" (cards de satisfação/total/nota média + avaliação por
     * estrelas). Nullable porque comentários já existentes não têm nota.
     */
    public function up(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->unsignedTinyInteger('nota')->nullable()->after('mensagem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropColumn('nota');
        });
    }
};
