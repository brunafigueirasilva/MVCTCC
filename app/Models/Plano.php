<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'planos';

    protected $fillable = [
        'lote_id',
        'racao_id',
        'quantidade',
        'horario',
        'consumo_ideal'
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function racao()
    {
        return $this->belongsTo(Racao::class, 'racao_id');
    }
}