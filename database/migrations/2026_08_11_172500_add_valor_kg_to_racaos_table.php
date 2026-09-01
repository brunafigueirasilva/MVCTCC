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
            // Preço por kg — nullable pra não quebrar rações já
            // cadastradas antes desse campo existir; o cadastro/edição
            // passa a exigir o valor daqui em diante.
            $table->decimal('valor_kg', 10, 2)->nullable()->after('consumo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            $table->dropColumn('valor_kg');
        });
    }
};
