<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bezerros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parto_id')->constrained()->cascadeOnDelete();
            // Sem animal_id de propósito: o bezerro fica como registro
            // histórico do parto, sem virar um Animal rastreável por conta
            // própria (fora do escopo pedido — dá pra "promover" depois).
            $table->foreignId('mae_id')->constrained('animais')->cascadeOnDelete();
            $table->string('sexo');
            $table->date('data_nascimento');
            $table->string('status')->default('Vivo');
            $table->string('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bezerros');
    }
};
