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
        'quantidade_antes',
        'quantidade_depois',
        'lote_destino_id',
        'data_ocorrencia',
        'observacao',
    ];

    // Produção de leite fica em "ordenhas" (por lote), não aqui. A data sai
    // como "2026-10-05" no JSON — no formato ISO com "Z" o app podia mostrar
    // o dia anterior por causa do fuso (UTC-3).
    protected $casts = [
        'data_ocorrencia' => 'date:Y-m-d',
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
