<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ordenha extends Model
{
    protected $fillable = [
        'animal_id',
        'lactacao_id',
        'data',
        'horario',
        'turno',
        'quantidade_litros',
        'responsavel_id',
        'observacoes',
    ];

    protected $casts = [
        'data' => 'date',
        'quantidade_litros' => 'float',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function lactacao()
    {
        return $this->belongsTo(Lactacao::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(Cadastro::class, 'responsavel_id');
    }
}
