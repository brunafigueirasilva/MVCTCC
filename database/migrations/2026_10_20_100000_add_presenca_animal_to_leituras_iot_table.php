<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Presença de animal no cocho: o controlador do cocho (ESP32 com balança
 * HX711 + HC-SR04) usa o ultrassônico para detectar um animal comendo, não
 * para medir o nível da ração. A leitura da balança passa a levar também:
 *  - presenca_animal: true = animal detectado na frente do cocho;
 *  - distancia_presenca_cm: distância medida pelo HC-SR04 nesse instante.
 * Ficam separadas de distancia_cm, que continua sendo o nível físico medido
 * pelo sensor de distância do cocho (tipo "distancia").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leituras_iot', function (Blueprint $table) {
            $table->boolean('presenca_animal')->nullable()->after('peso_kg');
            $table->decimal('distancia_presenca_cm', 8, 2)->nullable()->after('presenca_animal');
        });
    }

    public function down(): void
    {
        Schema::table('leituras_iot', function (Blueprint $table) {
            $table->dropColumn(['presenca_animal', 'distancia_presenca_cm']);
        });
    }
};
