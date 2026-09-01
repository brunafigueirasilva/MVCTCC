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

    protected $fillable = [
        'lote_id',
        'data_registro',
        'consumo_kg',
        'custo_rs',
        'ganho_peso_kg',
        'eficiencia_alimentar',
    ];

    protected $casts = [
        'data_registro' => 'date',
        'consumo_kg' => 'float',
        'custo_rs' => 'float',
        'ganho_peso_kg' => 'float',
        'eficiencia_alimentar' => 'float',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }
}
