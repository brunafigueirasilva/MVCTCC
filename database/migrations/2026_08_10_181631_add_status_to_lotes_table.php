<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            // Todo lote existente já cadastrado continua "Ativo" — só passa
            // a poder ser marcado como Inativo/Encerrado a partir de agora.
            $table->string('status')->default('Ativo')->after('data_entrada');
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
