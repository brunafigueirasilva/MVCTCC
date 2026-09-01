<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lactacao extends Model
{
    // Sem isso, o Eloquent tentava usar "lactacaos" (plural em inglês) em
    // vez de "lactacoes" — mesmo problema que já apareceu nas migrations
    // com o FK "constrained()" sem tabela explícita.
    protected $table = 'lactacoes';

    protected $fillable = [
        'animal_id',
        'parto_id',
        'numero_lactacao',
        'data_inicio',
        'data_fim',
        'producao_total',
        'media_diaria',
        'maior_producao',
        'menor_producao',
        'status',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'producao_total' => 'float',
        'media_diaria' => 'float',
        'maior_producao' => 'float',
        'menor_producao' => 'float',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function parto()
    {
        return $this->belongsTo(Parto::class);
    }

    public function ordenhas()
    {
        return $this->hasMany(Ordenha::class)->latest('data')->latest('horario');
    }
}
