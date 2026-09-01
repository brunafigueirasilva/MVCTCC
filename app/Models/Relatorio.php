<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $table = 'relatorios';

    protected $fillable = [
        'lote_id',
        'data_relatorio',
        'quantidade_animais',
        'peso_medio',
        'consumo_total',
        'observacoes',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
