<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guarda QUANDO o e-mail atualmente cadastrado foi verificado (código
     * de 6 dígitos, ver App\Models\CodigoVerificacao e
     * CadastroController::confirmarVerificacaoEmail()). Nulo = e-mail
     * ainda não verificado. Trocar o e-mail (CadastroController::
     * atualizarPerfil()) zera este campo — o "verificado" é sempre do
     * endereço atual, nunca herdado de um e-mail antigo.
     */
    public function up(): void
    {
        Schema::table('cadastros', function (Blueprint $table) {
            $table->timestamp('email_verificado_em')->nullable()->after('email_alterado_em');
        });
    }

    public function down(): void
    {
        Schema::table('cadastros', function (Blueprint $table) {
            $table->dropColumn('email_verificado_em');
        });
    }
};
