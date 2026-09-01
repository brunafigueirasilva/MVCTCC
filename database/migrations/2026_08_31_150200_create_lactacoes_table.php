<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lactacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animais')->cascadeOnDelete();
            $table->foreignId('parto_id')->constrained()->cascadeOnDelete();
            // Incrementado automaticamente por animal (1º parto = 1, 2º = 2...).
            $table->unsignedInteger('numero_lactacao');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->decimal('producao_total', 10, 2)->default(0);
            $table->decimal('media_diaria', 8, 2)->default(0);
            $table->decimal('maior_producao', 8, 2)->nullable();
            $table->decimal('menor_producao', 8, 2)->nullable();
            $table->string('status')->default('Ativa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lactacoes');
    }
};
