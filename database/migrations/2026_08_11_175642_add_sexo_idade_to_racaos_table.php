<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            // Pra qual sexo/faixa de idade essa ração foi formulada — evita
            // que uma ração pensada pra fêmeas em lactação, por exemplo,
            // acabe sendo usada num lote de machos ou de outra fase de
            // vida. Nullable só pra não quebrar rações já cadastradas
            // antes desse controle existir.
            $table->string('sexo')->nullable()->after('tipo');
            $table->string('idade')->nullable()->after('sexo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            $table->dropColumn(['sexo', 'idade']);
        });
    }
};
