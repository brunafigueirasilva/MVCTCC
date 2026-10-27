<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sensor físico instalado num cocho: de distância (nível físico da ração)
 * ou balança/célula de carga (peso). "codigo" é o id que o ESP32 envia.
 * Os campos ultima_leitura_em/ultimo_valor/ultimo_erro* são atualizados só
 * pelo App\Services\CochoIotService a cada leitura recebida.
 */
class Sensor extends Model
{
    protected $table = 'sensores';

    public const TIPO_DISTANCIA = 'distancia';
    public const TIPO_BALANCA = 'balanca';
    public const TIPOS = [self::TIPO_DISTANCIA, self::TIPO_BALANCA];

    public const STATUS_ONLINE = 'online';
    public const STATUS_OFFLINE = 'offline';
    public const STATUS_SEM_LEITURA = 'sem_leitura';
    public const STATUS_ERRO = 'erro';

    protected $fillable = [
        'cocho_id',
        'codigo',
        'tipo',
        'distancia_vazio_cm',
        'distancia_cheio_cm',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'distancia_vazio_cm' => 'float',
            'distancia_cheio_cm' => 'float',
            'ativo' => 'boolean',
            'ultima_leitura_em' => 'datetime:Y-m-d H:i:s',
            'ultimo_valor' => 'float',
            'ultimo_erro_em' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function cocho()
    {
        return $this->belongsTo(Cocho::class);
    }

    public function leituras()
    {
        return $this->hasMany(LeituraIot::class, 'sensor_id');
    }

    /**
     * online: leitura válida dentro de config('iot.minutos_offline');
     * erro: o evento mais recente foi uma leitura recusada (ex.: peso
     * negativo) e é recente; offline: a última leitura ficou velha;
     * sem_leitura: nunca enviou nada.
     */
    public function status(): string
    {
        $limite = now()->subMinutes(config('iot.minutos_offline', 15));

        if ($this->ultimo_erro_em
            && $this->ultimo_erro_em->greaterThan($limite)
            && (!$this->ultima_leitura_em || $this->ultimo_erro_em->greaterThan($this->ultima_leitura_em))) {
            return self::STATUS_ERRO;
        }

        if (!$this->ultima_leitura_em) {
            return self::STATUS_SEM_LEITURA;
        }

        return $this->ultima_leitura_em->greaterThan($limite) ? self::STATUS_ONLINE : self::STATUS_OFFLINE;
    }

    public function calibrado(): bool
    {
        return $this->tipo !== self::TIPO_DISTANCIA
            || ($this->distancia_vazio_cm !== null && $this->distancia_vazio_cm > (float) ($this->distancia_cheio_cm ?? 0));
    }

    /**
     * Nível físico (0–100%) a partir da distância medida, pela calibração:
     * distância "vazio" = 0%, distância "cheio" = 100%. null sem calibração.
     */
    public function percentualPorDistancia(float $distanciaCm): ?float
    {
        if (!$this->calibrado()) {
            return null;
        }

        $cheio = (float) ($this->distancia_cheio_cm ?? 0);
        $percentual = ($this->distancia_vazio_cm - $distanciaCm) / ($this->distancia_vazio_cm - $cheio) * 100;

        return round(max(0, min(100, $percentual)), 2);
    }

    public function dadosApi(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'tipo' => $this->tipo,
            'ativo' => $this->ativo,
            'distancia_vazio_cm' => $this->distancia_vazio_cm,
            'distancia_cheio_cm' => $this->distancia_cheio_cm,
            'calibrado' => $this->calibrado(),
            'status' => $this->status(),
            'ultimo_valor' => $this->ultimo_valor,
            'ultima_leitura_em' => $this->ultima_leitura_em?->format('Y-m-d H:i:s'),
            'ultimo_erro' => $this->ultimo_erro,
            'ultimo_erro_em' => $this->ultimo_erro_em?->format('Y-m-d H:i:s'),
        ];
    }
}
