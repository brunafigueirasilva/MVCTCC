<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcessibilidadePreferencia extends Model
{
    use HasFactory;

    protected $table = 'acessibilidade_preferencias';

    protected $fillable = [
        'usuario_id',
        'tema',
        'tamanho_fonte',
        'zoom',
        'voz_nome',
        'voz_velocidade',
    ];

    protected function casts(): array
    {
        return [
            'tamanho_fonte' => 'integer',
            'zoom' => 'float',
            'voz_velocidade' => 'float',
        ];
    }
}
