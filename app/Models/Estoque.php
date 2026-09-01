<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

    // Define explicitamente o nome da tabela no banco de dados (opcional, se o padrão for 'estoques')
    protected $table = 'estoques';

    /**
     * Os atributos que podem ser atribuídos em massa.
     * Devem bater exatamente com o que você valida no Controller.
     */
    protected $fillable = [
        'racao_id',
        'quantidade',
        'capacidade',
        'fornecedor',
    ];

    /**
     * Relacionamento: Um registro de estoque pertence a uma Ração.
     * Isso permite usar o Estoque::with('racao')->get() que está no seu index().
     */
    public function racao()
    {
        return $this->belongsTo(Racao::class, 'racao_id');
    }
}