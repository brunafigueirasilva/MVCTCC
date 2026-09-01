<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteConsumoSnapshot extends Model
{
    protected $table = 'lote_consumo_snapshots';

    protected $fillable = [
        'lote_id',
        'racao_id',
        'data',
        'quantidade_animais',
        'consumo_kg',
        'status_class',
    ];

    protected $casts = [
        'data' => 'date',
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
