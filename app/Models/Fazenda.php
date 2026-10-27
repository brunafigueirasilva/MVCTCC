<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fazenda extends Model
{
    // cadastro_id fica FORA do $fillable de propósito: o dono é sempre o
    // usuário autenticado, atribuído pela relação
    // ($cadastro->fazendas()->create(...)) — nunca por um campo do request.
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
    ];

    protected function casts(): array
    {
        return [
            'area' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function cadastro()
    {
        return $this->belongsTo(Cadastro::class, 'cadastro_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'fazenda_id');
    }

    /** Cochos da fazenda (Fazenda → Lote → Cocho). */
    public function cochos()
    {
        return $this->hasManyThrough(Cocho::class, Lote::class, 'fazenda_id', 'lote_id');
    }

    public function racoes()
    {
        return $this->hasMany(Racao::class, 'fazenda_id');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class, 'fazenda_id');
    }

    public function movimentacoesEstoque()
    {
        return $this->hasMany(EstoqueMovimentacao::class, 'fazenda_id');
    }

    /** Escopo das fazendas de um usuário — base de toda checagem de posse. */
    public function scopeDoUsuario($query, ?int $cadastroId)
    {
        return $query->where('cadastro_id', $cadastroId);
    }
}
