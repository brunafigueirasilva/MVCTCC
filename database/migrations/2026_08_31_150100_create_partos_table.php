<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animais')->cascadeOnDelete();
            $table->date('data_prevista')->nullable();
            // Fica nula até o parto ser marcado como "Realizado".
            $table->date('data_parto')->nullable();
            // Só preenchido quando data_parto é preenchida (parto real).
            $table->string('tipo_parto')->nullable();
            $table->unsignedInteger('quantidade_bezerros')->default(0);
            // NULL = status calculado a partir das datas (Previsto/Próximo/
            // Atrasado) — ver Parto::getStatusAttribute(). Só grava um
            // valor explícito quando vira um fato que não depende mais do
            // relógio: Realizado, Cancelado ou Perdido.
            $table->string('status')->nullable();
            $table->string('observacoes')->nullable();
            $table->foreignId('responsavel_id')->nullable()
                ->constrained('cadastros')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partos');
    }
};
