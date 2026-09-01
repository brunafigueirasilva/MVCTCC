<?php

namespace App\Models;

use App\Support\AnimalRegras;
use Illuminate\Database\Eloquent\Model;

class Parto extends Model
{
    protected $fillable = [
        'animal_id',
        'data_prevista',
        'data_parto',
        'tipo_parto',
        'quantidade_bezerros',
        'status',
        'observacoes',
        'responsavel_id',
    ];

    protected $casts = [
        'data_prevista' => 'date',
        'data_parto' => 'date',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function bezerros()
    {
        return $this->hasMany(Bezerro::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(Cadastro::class, 'responsavel_id');
    }

    public function lactacao()
    {
        return $this->hasOne(Lactacao::class);
    }

    /**
     * A coluna "status" fica NULL enquanto o parto ainda não é um fato
     * consumado — nesse caso o status é calculado na leitura, a partir de
     * data_prevista (Previsto/Próximo/Atrasado), sem depender de nenhum
     * cron/scheduler (que não existe em nenhum lugar do projeto) para
     * "atualizar status vencidos". Só existe um valor gravado de verdade
     * quando vira um fato que não depende mais do relógio: Realizado,
     * Cancelado ou Perdido.
     */
    public function getStatusAttribute($value): string
    {
        return $value ?? AnimalRegras::statusPartoPrevisto(
            $this->attributes['data_prevista'] ?? null
        );
    }
}
