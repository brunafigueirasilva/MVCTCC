<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ordenha extends Model
{
    protected $fillable = [
        'lote_id',
        'data',
        'horario',
        'turno',
        'quantidade_litros',
        'responsavel_id',
        'observacoes',
    ];

    // "date:Y-m-d" mantém a data como "2026-10-05" no JSON da API (sem o
    // formato ISO com hora/fuso), igual às demais datas do sistema.
    protected $casts = [
        'data' => 'date:Y-m-d',
        'quantidade_litros' => 'float',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(Cadastro::class, 'responsavel_id');
    }
}
