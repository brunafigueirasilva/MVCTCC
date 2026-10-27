<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campo livre e OPCIONAL (ex.: "Nelore", "Holandês", "Duroc") — a raça
     * varia demais entre espécies pra virar uma lista fixa como tipo_animal/
     * finalidade/idade_media, então fica texto livre em vez de um enum.
     * Continua tudo por LOTE: não é um cadastro por animal individual.
     */
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->string('raca', 100)->nullable()->after('tipo_animal');
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn('raca');
        });
    }
};
