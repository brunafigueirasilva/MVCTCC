<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Plano alimentar de um lote: qual ração, quanto (kg por animal por dia),
 * quantas vezes por dia e em que período. fazenda_id é sempre a do lote
 * (definida no controller, nunca vinda do cliente).
 */
class Plano extends Model
{
    protected $table = 'planos';

    protected $fillable = [
        'lote_id',
        'racao_id',
        'quantidade_recomendada',
        'frequencia_diaria',
        'data_inicio',
        'data_fim',
        'observacoes',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'quantidade_recomendada' => 'float',
            'frequencia_diaria' => 'integer',
            'data_inicio' => 'date:Y-m-d',
            'data_fim' => 'date:Y-m-d',
            'ativo' => 'boolean',
        ];
    }

    public function fazenda()
    {
        return $this->belongsTo(Fazenda::class, 'fazenda_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function racao()
    {
        return $this->belongsTo(Racao::class, 'racao_id');
    }

    /** Consumo diário total do lote previsto pelo plano (kg/dia). */
    public function consumoDiarioPrevisto(): float
    {
        return round($this->quantidade_recomendada * ($this->lote?->quantidade_animais ?? 0), 2);
    }

    /**
     * Torna $racao a ração do lote: um único plano ATIVO (o mesmo registro
     * que o app cria pela API) + lotes.racao_id. Sem quantidade informada,
     * mantém a do plano atual (se for a mesma ração) ou parte do consumo
     * cadastrado na ração dividido pelos animais do lote. Usado pelo
     * "vincular ração" do Plano Alimentar e pelo cadastro de ração com lote.
     */
    public static function ativarParaLote(Lote $lote, Racao $racao, ?float $porAnimal = null): self
    {
        $atual = static::where('lote_id', $lote->id)->where('ativo', true)->latest('data_inicio')->first();
        $mesmaRacao = $atual && (int) $atual->racao_id === (int) $racao->id;
        $animais = max((int) $lote->quantidade_animais, 1);

        $porAnimal = $porAnimal !== null
            ? round($porAnimal, 2)
            : (($mesmaRacao && $atual->quantidade_recomendada > 0)
                ? (float) $atual->quantidade_recomendada
                : round((float) $racao->consumo / $animais, 2));

        if ($mesmaRacao) {
            $atual->update(['quantidade_recomendada' => $porAnimal]);
            $plano = $atual;
        } else {
            static::where('lote_id', $lote->id)->where('ativo', true)
                ->update(['ativo' => false, 'data_fim' => today()->toDateString()]);
            $plano = new static([
                'lote_id' => $lote->id,
                'racao_id' => $racao->id,
                'quantidade_recomendada' => $porAnimal,
                'frequencia_diaria' => 3,
                'data_inicio' => today()->toDateString(),
                'ativo' => true,
            ]);
            $plano->fazenda_id = $lote->fazenda_id;
            $plano->save();
        }

        $lote->update(['racao_id' => $racao->id]);

        return $plano->setRelation('lote', $lote);
    }

    /** Lote fica sem ração: encerra o plano ativo (o histórico continua). */
    public static function encerrarDoLote(Lote $lote): void
    {
        static::where('lote_id', $lote->id)->where('ativo', true)
            ->update(['ativo' => false, 'data_fim' => today()->toDateString()]);
        $lote->update(['racao_id' => null]);
    }
}
