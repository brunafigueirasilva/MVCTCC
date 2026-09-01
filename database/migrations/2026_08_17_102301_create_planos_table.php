<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta migration não existia no repositório — a tabela `planos` já
     * existia no banco (criada fora do controle de versão). A estrutura
     * abaixo replica exatamente o que já está em produção/desenvolvimento
     * (confirmado via SHOW CREATE TABLE), incluindo o fato de
     * `racao_id`/`lote_id` serem `varchar` sem chave estrangeira — não é
     * o ideal (deveriam ser `foreignId`), mas mudar o tipo agora exigiria
     * uma migration de alteração separada com backfill dos dados
     * existentes, então esta migration só fecha a lacuna de versionamento
     * sem alterar o que já está gravado.
     */
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('racao_id');
            $table->string('lote_id');
            $table->decimal('quantidade', 10, 2);
            $table->dateTime('horario');
            $table->string('consumo_ideal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
