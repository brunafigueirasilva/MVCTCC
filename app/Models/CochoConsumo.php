<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Consumo registrado num cocho (sem sensor). Criado só pelo
 * App\Services\EstoqueService::registrarConsumoCocho(), na mesma transação
 * que diminui cochos.nivel_atual.
 */
class CochoConsumo extends Model
{
    protected $table = 'cocho_consumos';

    public const ORIGEM_MANUAL = 'manual';
    public const ORIGEM_AJUSTE_NIVEL = 'ajuste_nivel';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'quantidade' => 'float',
            'nivel_anterior' => 'float',
            'nivel_posterior' => 'float',
            'valor_unitario' => 'float',
            'data_consumo' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function cocho()
    {
        return $this->belongsTo(Cocho::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function racao()
    {
        return $this->belongsTo(Racao::class);
    }

    public function cadastro()
    {
        return $this->belongsTo(Cadastro::class);
    }

    public function dadosApi(): array
    {
        return [
            'id' => $this->id,
            'cocho_id' => $this->cocho_id,
            'lote_id' => $this->lote_id,
            'fazenda_id' => $this->fazenda_id,
            'racao_id' => $this->racao_id,
            'cadastro_id' => $this->cadastro_id,
            'origem' => $this->origem,
            'quantidade' => $this->quantidade,
            'nivel_anterior' => $this->nivel_anterior,
            'nivel_posterior' => $this->nivel_posterior,
            'observacao' => $this->observacao,
            'data_consumo' => $this->data_consumo?->format('Y-m-d H:i:s'),
        ];
    }
}
