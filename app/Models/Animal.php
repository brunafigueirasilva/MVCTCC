<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = 'animais';

    protected $fillable = [
        'lote_id',
        'nome',
        'identificador',
        'sexo',
        'raca',
        'data_nascimento',
        'peso',
        'status',
        'status_reprodutivo',
        'data_ultimo_parto',
        'qtd_partos',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_ultimo_parto' => 'date',
        'peso' => 'float',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function partos()
    {
        return $this->hasMany(Parto::class)->latest('data_prevista')->latest('id');
    }

    public function lactacoes()
    {
        return $this->hasMany(Lactacao::class)->latest('numero_lactacao');
    }

    public function ordenhas()
    {
        return $this->hasMany(Ordenha::class)->latest('data')->latest('horario');
    }

    /** Bezerros que este animal deu à luz (ela é a mãe). */
    public function bezerros()
    {
        return $this->hasMany(Bezerro::class, 'mae_id');
    }

    /** Única lactação em aberto — null se o animal estiver em período seco
     *  ou nunca tiver tido um parto realizado. */
    public function lactacaoAtiva()
    {
        return $this->hasOne(Lactacao::class)->where('status', 'Ativa');
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento?->age;
    }
}
