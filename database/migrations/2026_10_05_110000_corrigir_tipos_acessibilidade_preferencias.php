<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alinha acessibilidade_preferencias ao que a migration de criação define e
 * o AcessibilidadeController valida: "zoom" é decimal (0.50–1.30) e
 * "tamanho_fonte" é a escala em % (padrão 100). Bancos criados fora das
 * migrations ficaram com zoom TINYINT — um zoom de 0.95 virava 1 ao salvar.
 * Em bancos já corretos, a alteração não muda nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acessibilidade_preferencias', function (Blueprint $table) {
            $table->unsignedTinyInteger('tamanho_fonte')->default(100)->change();
            $table->decimal('zoom', 3, 2)->default(1.00)->change();
        });
    }

    public function down(): void
    {
        // Sem volta: o tipo anterior (TINYINT) perdia os decimais do zoom.
    }
};
