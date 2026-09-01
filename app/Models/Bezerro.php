<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bezerro extends Model
{
    protected $fillable = [
        'parto_id',
        'mae_id',
        'sexo',
        'data_nascimento',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function parto()
    {
        return $this->belongsTo(Parto::class);
    }

    /** A vaca (Animal) que deu à luz este bezerro. */
    public function mae()
    {
        return $this->belongsTo(Animal::class, 'mae_id');
    }
}
