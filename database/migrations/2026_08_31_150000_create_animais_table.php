<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained()->cascadeOnDelete();
            $table->string('nome');
            // Brinco/tag — identificação física do animal, opcional (nem
            // toda fazenda usa brinco numerado).
            $table->string('identificador')->nullable();
            $table->string('sexo');
            $table->string('raca')->nullable();
            $table->date('data_nascimento');
            $table->decimal('peso', 8, 2)->nullable();
            $table->string('status')->default('Ativo');

            // Campos "cache" atualizados pelo AnimalController a cada
            // evento de parto — evitam recalcular via query pesada toda
            // vez que a listagem/dashboard precisa desses valores (mesmo
            // espírito de Lote::quantidade_animais).
            $table->string('status_reprodutivo')->default('Vazia');
            $table->date('data_ultimo_parto')->nullable();
            $table->unsignedInteger('qtd_partos')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animais');
    }
};
