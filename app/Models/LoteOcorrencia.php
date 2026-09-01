<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteOcorrencia extends Model
{
    protected $fillable = [
        'lote_id',
        'tipo',
        'causa',
        'doenca',
        'quantidade',
        'quantidade_litros',
        'quantidade_antes',
        'quantidade_depois',
        'lote_destino_id',
        'data_ocorrencia',
        'observacao',
    ];

    protected $casts = [
        'data_ocorrencia' => 'date',
        'quantidade_litros' => 'float',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    /** O lote pra onde os animais doentes foram movidos (tipo = 'doenca'). */
    public function loteDestino()
    {
        return $this->belongsTo(Lote::class, 'lote_destino_id');
    }
}
