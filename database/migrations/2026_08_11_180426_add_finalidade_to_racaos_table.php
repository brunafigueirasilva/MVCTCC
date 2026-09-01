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
            // Preenchida junto com sexo/idade a partir do lote escolhido no
            // cadastro — não é uma referência viva ao lote (ele pode ser
            // editado ou excluído depois), é só um retrato dos dados no
            // momento em que a ração foi cadastrada.
            $table->string('finalidade')->nullable()->after('idade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            $table->dropColumn('finalidade');
        });
    }
};
