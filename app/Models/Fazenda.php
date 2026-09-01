<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Fazenda extends Model
{
    protected $fillable = [
        'nome_fazenda',
        'proprietario',
        'localizacao',
        'cep',
        'cidade',
        'estado',
        'latitude',
        'longitude',
        'area',
        'cadastro_id'
    ];

    public function lotes()
{
    return $this->hasMany(Lote::class, 'fazenda_id');
}

    public function racoes()
    {
        return $this->hasMany(Racao::class, 'fazenda_id');
    }

    public function cadastro()
    {
        return $this->belongsTo(Cadastro::class, 'cadastro_id');
    }

}