<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabela usada por App\Models\AcessibilidadePreferencia e
     * AcessibilidadeController (show/store) para sincronizar entre
     * dispositivos as preferências de acessibilidade do usuário logado
     * (tema, tamanho da fonte, zoom, voz). Faltava por completo no banco —
     * essa migration fecha a lacuna.
     *
     * "tamanho_fonte" guarda a escala em % (80-150, mesma faixa do controle
     * "Tamanho do Texto" em partials/acessibilidade.blade.php) e "zoom" a
     * escala decimal do "Zoom do Sistema" (0.5-1.3) — não uma escala fixa
     * 0-3, pra bater exatamente com o que a tela realmente envia.
     */
    public function up(): void
    {
        Schema::create('acessibilidade_preferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('cadastros')->cascadeOnDelete();
            $table->string('tema', 20)->default('light');
            $table->unsignedTinyInteger('tamanho_fonte')->default(100);
            $table->decimal('zoom', 3, 2)->default(1.00);
            $table->string('voz_nome')->nullable();
            $table->decimal('voz_velocidade', 3, 2)->default(1.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acessibilidade_preferencias');
    }
};
