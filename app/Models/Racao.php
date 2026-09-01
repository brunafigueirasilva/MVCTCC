<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Racao extends Model
{
    protected $fillable = [
        'fazenda_id',
        'nome_racao',
        'tipo',
        'sexo',
        'idade',
        'finalidade',
        'estoque',
        'estoque_total',
        'consumo',
        'valor_kg',
        'fornecedor',
        'reposicao'
    ];

    /** Margem de segurança (em dias) usada tanto pelos alertas do
     *  Dashboard quanto pelo status mostrado na tela de Rações — um único
     *  lugar pra essa regra, pra nunca ficar divergente entre as duas
     *  telas. */
    public const DIAS_SEGURANCA_ESTOQUE = 10;

    public function fazenda()
    {
        return $this->belongsTo(Fazenda::class, 'fazenda_id');
    }

    /** Quantos dias de estoque restam no ritmo de consumo atual — null se
     *  não houver consumo diário registrado (não dá pra estimar). */
    public function diasRestantesEstoque(): ?int
    {
        return $this->consumo > 0 ? (int) floor($this->estoque / $this->consumo) : null;
    }

    /** Percentual do estoque atual em relação à capacidade total
     *  cadastrada ("Estoque Total") — null se a capacidade não foi
     *  informada (rações cadastradas antes desse campo existir). */
    public function percentualEstoque(): ?int
    {
        if (!$this->estoque_total || $this->estoque_total <= 0) {
            return null;
        }

        return (int) round(min(100, ($this->estoque / $this->estoque_total) * 100));
    }

    /** 'esgotado' (zerado, crítico independente do consumo), 'baixo'
     *  (acaba dentro da margem de segurança) ou 'ok'. */
    public function statusEstoque(): string
    {
        if ($this->estoque <= 0) {
            return 'esgotado';
        }

        $dias = $this->diasRestantesEstoque();

        if ($dias !== null && $dias <= self::DIAS_SEGURANCA_ESTOQUE) {
            return 'baixo';
        }

        return 'ok';
    }

    /**
     * Status da barrinha de "Nível" (percentual do estoque atual sobre o
     * Estoque Total cadastrado) — puramente visual, e por isso separado de
     * statusEstoque() (que mede dias restantes de consumo, não percentual).
     * 'ok' (>70%), 'baixo' (30% a 70%) ou 'esgotado' (<30%). Reaproveita o
     * mesmo vocabulário/CSS de statusEstoque() (verde/amarelo/vermelho) só
     * porque as cores já existiam prontas — os dois critérios continuam
     * independentes um do outro. Null quando não há Estoque Total
     * cadastrado (percentualEstoque() null).
     */
    public function nivelStatus(): ?string
    {
        $percentual = $this->percentualEstoque();

        if ($percentual === null) {
            return null;
        }

        if ($percentual < 30) {
            return 'esgotado';
        }

        if ($percentual <= 70) {
            return 'baixo';
        }

        return 'ok';
    }
}