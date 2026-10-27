<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Monitoramento IoT dos cochos (Fazenda → Lote → Cocho → Sensor → Leitura):
 *
 *  - sensores: cada sensor físico instalado num cocho — no máximo um de
 *    distância (nível físico) e um de balança/célula de carga (peso) por
 *    cocho. "codigo" é o identificador que o ESP32 envia (ex.: DIST-001).
 *    Guarda a calibração do sensor de distância e o resumo da última
 *    leitura/erro (usado pelo status online/offline/erro sem varrer o
 *    histórico).
 *  - leituras_iot: histórico de TODAS as leituras (nunca sobrescrito), base
 *    dos gráficos e indicadores. "quantidade_kg" é a quantidade do cocho
 *    depois da leitura e "variacao_kg" a diferença para a quantidade
 *    anterior (negativa = consumo, positiva = reposição detectada).
 *
 * Nada é duplicado em "cochos": a quantidade atual continua em
 * cochos.nivel_atual (kg) — a mesma coluna que o abastecimento pelo estoque
 * já atualiza — e percentual/status são calculados.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cocho_id')->constrained('cochos')->cascadeOnDelete();
            $table->string('codigo', 50)->unique();
            // distancia | balanca
            $table->string('tipo', 20);
            // Calibração do sensor de distância: distância até o fundo com o
            // cocho vazio e até a ração com ele cheio (cm).
            $table->decimal('distancia_vazio_cm', 8, 2)->nullable();
            $table->decimal('distancia_cheio_cm', 8, 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->dateTime('ultima_leitura_em')->nullable();
            $table->decimal('ultimo_valor', 10, 2)->nullable();
            $table->string('ultimo_erro')->nullable();
            $table->dateTime('ultimo_erro_em')->nullable();
            $table->timestamps();

            $table->unique(['cocho_id', 'tipo']);
        });

        Schema::create('leituras_iot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cocho_id')->constrained('cochos')->cascadeOnDelete();
            $table->foreignId('sensor_id')->constrained('sensores')->cascadeOnDelete();
            $table->string('tipo_sensor', 20);
            $table->decimal('distancia_cm', 8, 2)->nullable();
            $table->decimal('peso_kg', 10, 2)->nullable();
            $table->decimal('nivel_percentual', 5, 2)->nullable();
            $table->decimal('quantidade_kg', 10, 2)->nullable();
            $table->decimal('variacao_kg', 10, 2)->nullable();
            $table->dateTime('data_hora');
            $table->timestamps();

            // O mesmo sensor não grava duas leituras no mesmo instante
            // (reenvio do ESP32 após falha de rede).
            $table->unique(['sensor_id', 'data_hora']);
            $table->index(['cocho_id', 'data_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leituras_iot');
        Schema::dropIfExists('sensores');
    }
};
