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
        Schema::create('lote_ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained()->cascadeOnDelete();
            // "tipo" fica pronto pra outras ocorrências no futuro (ex.:
            // transferência, nascimento) — hoje só "morte" é usado.
            $table->string('tipo')->default('morte');
            $table->unsignedInteger('quantidade')->default(1);
            $table->unsignedInteger('quantidade_antes');
            $table->unsignedInteger('quantidade_depois');
            $table->date('data_ocorrencia');
            $table->string('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_ocorrencias');
    }
};
