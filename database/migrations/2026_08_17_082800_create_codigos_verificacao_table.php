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
        Schema::create('codigos_verificacao', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            // Guarda o hash do código (bcrypt), nunca o valor em texto puro —
            // mesmo padrão usado para a senha do próprio cadastro.
            $table->string('codigo_hash');
            // 'redefinicao_senha' (esqueci minha senha) ou 'alteracao_senha'
            // (troca de senha a partir do perfil, já logado).
            $table->string('proposito');
            // Só usado em 'alteracao_senha': guarda o hash da nova senha
            // escolhida, para só gravá-la no cadastro depois que o código
            // for confirmado.
            $table->text('payload')->nullable();
            $table->timestamp('expira_em');
            $table->timestamp('usado_em')->nullable();
            $table->timestamps();

            $table->index(['email', 'proposito']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigos_verificacao');
    }
};
