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
        Schema::table('cadastros', function (Blueprint $table) {
            // Necessária para comprovar maioridade sempre que um CPF é informado/alterado.
            $table->date('data_nascimento')->nullable()->after('cpf');

            // Auditoria de segurança: quando a senha/e-mail/CPF mudaram pela última vez,
            // usado para exibir alertas e decidir quando reforçar a reautenticação.
            $table->timestamp('senha_alterada_em')->nullable()->after('senha');
            $table->timestamp('email_alterado_em')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cadastros', function (Blueprint $table) {
            $table->dropColumn(['data_nascimento', 'senha_alterada_em', 'email_alterado_em']);
        });
    }
};
