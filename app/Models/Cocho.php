<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lote;

class Cocho extends Model
{
    // Indique explicitamente o nome da tabela se for diferente de "cochos"
    protected $table = 'cochos'; 

    // Campos que o formulário tem permissão para salvar
    protected $fillable = [
        'nome_cocho',
        'capacidade',
        'nivel_atual',
        'lote_id',
        'sensor',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
