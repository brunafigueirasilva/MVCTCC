<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Snapshot diário do nível de estoque de uma ração — capturado a cada
 * visita a Gestão de Rações/Nível de Estoque (ver RacaoController/
 * EstoqueController::capturarSnapshotEstoqueDeHoje), mesmo padrão de
 * App\Models\LoteConsumoSnapshot e App\Models\DesempenhoLote: histórico
 * real, nunca reconstruído/estimado.
 */
class RacaoEstoqueSnapshot extends Model
{
    protected $table = 'racao_estoque_snapshots';

    protected $fillable = [
        'racao_id',
        'data',
        'estoque_kg',
        'percentual_estoque',
        'status_class',
    ];

    protected $casts = [
        'data' => 'date',
        'estoque_kg' => 'float',
        'percentual_estoque' => 'integer',
    ];

    public function racao()
    {
        return $this->belongsTo(Racao::class);
    }
}
