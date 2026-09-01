<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O cadastro de fazenda passa a usar consulta de CEP (cidade/estado
     * preenchidos automaticamente) em vez de um campo único de texto livre
     * ("localizacao"). A coluna antiga é mantida para não perder o histórico
     * já cadastrado nem quebrar nada que ainda dependa dela.
     */
    public function up(): void
    {
        Schema::table('fazendas', function (Blueprint $table) {
            $table->string('cep', 9)->nullable()->after('localizacao');
            $table->string('cidade')->nullable()->after('cep');
            $table->string('estado', 2)->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('fazendas', function (Blueprint $table) {
            $table->dropColumn(['cep', 'cidade', 'estado']);
        });
    }
};
