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
        Schema::table('lotes', function (Blueprint $table) {
            // Marca este lote como um lote de isolamento (criado por
            // "Registrar Doença") e aponta pro lote que os animais vieram —
            // é o único lugar pra onde eles podem ser devolvidos depois.
            // Nulo em lotes "normais" (nunca vieram de uma separação).
            $table->foreignId('lote_origem_id')->nullable()->after('fazenda_id')
                ->constrained('lotes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lote_origem_id');
        });
    }
};
