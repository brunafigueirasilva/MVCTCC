<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Uma entrada, saída ou ajuste no estoque de uma ração. Só é criada pelo
 * App\Services\EstoqueService (dentro da mesma transação que altera
 * racaos.estoque), por isso não tem $fillable aberto para request algum.
 */
class EstoqueMovimentacao extends Model
{
    protected $table = 'estoque_movimentacoes';

    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SAIDA = 'saida';
    public const TIPO_AJUSTE = 'ajuste';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'quantidade' => 'float',
            'saldo_anterior' => 'float',
            'saldo_posterior' => 'float',
            'valor_unitario' => 'float',
            'data_movimentacao' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function racao()
    {
        return $this->belongsTo(Racao::class);
    }

    public function fazenda()
    {
        return $this->belongsTo(Fazenda::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function cocho()
    {
        return $this->belongsTo(Cocho::class);
    }

    public function cadastro()
    {
        return $this->belongsTo(Cadastro::class);
    }
}
