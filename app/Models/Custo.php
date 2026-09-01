<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Custo extends Model
{
    protected $fillable = [
        'gasto_mensal',
        'custo_animal',
        'lucro',
        'gasto_racao',
        'receita'
    ];
}