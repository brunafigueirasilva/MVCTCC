<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Uma leitura recebida de um sensor IoT (histórico — nunca sobrescrito).
 * Criada só pelo App\Services\CochoIotService.
 */
class LeituraIot extends Model
{
    protected $table = 'leituras_iot';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'distancia_cm' => 'float',
            'peso_kg' => 'float',
            'presenca_animal' => 'boolean',
            'distancia_presenca_cm' => 'float',
            'nivel_percentual' => 'float',
            'quantidade_kg' => 'float',
            'variacao_kg' => 'float',
            'data_hora' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function cocho()
    {
        return $this->belongsTo(Cocho::class);
    }

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

    public function dadosApi(): array
    {
        return [
            'id' => $this->id,
            'cocho_id' => $this->cocho_id,
            'sensor_id' => $this->sensor_id,
            'sensor_codigo' => $this->relationLoaded('sensor') ? $this->sensor?->codigo : null,
            'tipo_sensor' => $this->tipo_sensor,
            'distancia_cm' => $this->distancia_cm,
            'peso_kg' => $this->peso_kg,
            'presenca_animal' => $this->presenca_animal,
            'distancia_presenca_cm' => $this->distancia_presenca_cm,
            'nivel_percentual' => $this->nivel_percentual,
            'quantidade_kg' => $this->quantidade_kg,
            'variacao_kg' => $this->variacao_kg,
            'data_hora' => $this->data_hora?->format('Y-m-d H:i:s'),
        ];
    }
}
