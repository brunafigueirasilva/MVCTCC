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
            // Nullable porque rações já cadastradas antes desta migração não
            // têm fazenda associada; o formulário passa a exigir o campo daqui
            // para frente (RacaoController::add/update).
            $table->foreignId('fazenda_id')->nullable()->after('id')
                ->constrained('fazendas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racaos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fazenda_id');
        });
    }
};
