<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Registro periódico de pesagem por lote — usado para calcular a Conversão
 * Alimentar REAL (kg de ração / kg de peso ganho) entre duas pesagens.
 * Reaproveita a tabela "desempenho_lotes", criada numa migration anterior
 * para um recurso que nunca chegou a existir (ver comentário da migration
 * 2026_08_25_091000_create_desempenho_lotes_table.php) — os campos batiam
 * exatamente com o que a Conversão Alimentar precisava, então em vez de
 * criar uma tabela nova ela foi revivida aqui.
 */
class DesempenhoLote extends Model
{
    protected $table = 'desempenho_lotes';

    /** De onde veio o consumo usado na conversão (ver App\Services\ConsumoLoteService). */
    public const FONTE_SENSOR = 'sensor';
    public const FONTE_REGISTRADO = 'registrado';
    public const FONTE_FORNECIDO = 'fornecido';
    public const FONTE_PLANO = 'plano';

    /** Texto curto mostrado ao produtor ao lado da conversão. */
    public const ROTULOS_FONTE = [
        self::FONTE_SENSOR => 'Real — consumo medido pelos sensores dos cochos',
        self::FONTE_REGISTRADO => 'Real — consumo registrado nos cochos',
        self::FONTE_FORNECIDO => 'Aproximada — ração colocada nos cochos (inclui sobras)',
        self::FONTE_PLANO => 'Estimativa — consumo planejado no plano alimentar',
    ];

    protected $fillable = [
        'lote_id',
        'data_registro',
        'peso_medio_kg',
        'quantidade_animais',
        'consumo_kg',
        'fonte_consumo',
        'custo_rs',
        'ganho_peso_kg',
        'eficiencia_alimentar',
    ];

    protected $casts = [
        'data_registro' => 'date',
        'peso_medio_kg' => 'float',
        'quantidade_animais' => 'integer',
        'consumo_kg' => 'float',
        'custo_rs' => 'float',
        'ganho_peso_kg' => 'float',
        'eficiencia_alimentar' => 'float',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    /** "Real" só quando o consumo foi medido pelos sensores. */
    public function conversaoEhReal(): bool
    {
        return $this->fonte_consumo === self::FONTE_SENSOR;
    }

    public function rotuloFonteConsumo(): ?string
    {
        return self::ROTULOS_FONTE[$this->fonte_consumo] ?? null;
    }
}
