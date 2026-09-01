<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animais')->cascadeOnDelete();
            $table->foreignId('lactacao_id')->constrained('lactacoes')->cascadeOnDelete();
            $table->date('data');
            $table->time('horario');
            $table->string('turno');
            $table->decimal('quantidade_litros', 8, 2);
            $table->foreignId('responsavel_id')->nullable()
                ->constrained('cadastros')->nullOnDelete();
            $table->string('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenhas');
    }
};
