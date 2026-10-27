<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Racao extends Model
{
    // fazenda_id continua preenchível porque o RacaoController o deriva do
    // lote escolhido (já validado como do usuário) — nunca do request cru.
    protected $fillable = [
        'fazenda_id',
        'nome_racao',
        'tipo',
        'sexo',
        'idade',
        'finalidade',
        'nutrientes',
        'unidade',
        'estoque',
        'estoque_total',
        'consumo',
        'valor_kg',
        'fornecedor',
        // Características nutricionais e de controle (tela "Tipos de
        // Rações" do app) — todas opcionais.
        'proteina_percentual',
        'energia_mcal_kg',
        'fibra_percentual',
        'minerais',
        'estoque_minimo',
        'status',
        'observacoes',
    ];

    /** 'inativa' some das opções de vínculo (ver CompatibilidadeRacao). */
    public const STATUS = ['ativa', 'inativa'];

    /** Quantidades em kg e preço como número no JSON da API (o DECIMAL do
     *  MySQL chegaria como texto "12.50"). */
    protected function casts(): array
    {
        return [
            'estoque' => 'float',
            'estoque_total' => 'float',
            'consumo' => 'float',
            'valor_kg' => 'float',
            'proteina_percentual' => 'float',
            'energia_mcal_kg' => 'float',
            'fibra_percentual' => 'float',
            'estoque_minimo' => 'float',
        ];
    }

    /** Até quantos dias restantes o estoque já é crítico (mesmo limiar dos
     *  alertas do Dashboard, que marcam "crítico" com até 3 dias). */
    public const DIAS_ESTOQUE_CRITICO = 3;

    /** Margem de segurança (em dias) usada tanto pelos alertas do
     *  Dashboard quanto pelo status mostrado na tela de Rações — um único
     *  lugar pra essa regra, pra nunca ficar divergente entre as duas
     *  telas. */
    public const DIAS_SEGURANCA_ESTOQUE = 10;

    public function fazenda()
    {
        return $this->belongsTo(Fazenda::class, 'fazenda_id');
    }

    /** Lotes que têm esta ração vinculada (lotes.racao_id). */
    public function lotes()
    {
        return $this->hasMany(Lote::class, 'racao_id');
    }

    /** Histórico de entradas/saídas de estoque desta ração. */
    public function movimentacoes()
    {
        return $this->hasMany(EstoqueMovimentacao::class)->latest('data_movimentacao')->latest('id');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class);
    }

    /** Histórico diário do nível de estoque (ver App\Models\RacaoEstoqueSnapshot). */
    public function estoqueSnapshots()
    {
        return $this->hasMany(RacaoEstoqueSnapshot::class);
    }

    private ?float $consumoReferenciaCache = null;

    /**
     * Consumo diário (kg/dia) usado para prever quanto o estoque dura: o
     * PLANEJADO dos lotes que comem esta ração (kg por animal × animais do
     * plano ativo — a mesma regra do Plano Alimentar e da API). Sem lote com
     * plano por animal, vale o "consumo (kg/dia)" cadastrado na ração.
     * Antes era sempre o número fixo da ração, mesmo depois de nascimentos
     * ou óbitos mudarem o tamanho do lote.
     */
    public function consumoDiarioReferencia(): float
    {
        if ($this->consumoReferenciaCache !== null) {
            return $this->consumoReferenciaCache;
        }

        $planejado = Plano::where('racao_id', $this->id)
            ->where('ativo', true)
            ->whereHas('lote', fn ($q) => $q->where('racao_id', $this->id))
            ->with('lote:id,quantidade_animais')
            ->get()
            ->sum(fn (Plano $p) => $p->consumoDiarioPrevisto());

        return $this->consumoReferenciaCache = $planejado > 0 ? round($planejado, 2) : (float) $this->consumo;
    }

    /** Quantos dias de estoque restam no ritmo de consumo planejado — null
     *  se não houver consumo previsto (não dá pra estimar). */
    public function diasRestantesEstoque(): ?int
    {
        $consumo = $this->consumoDiarioReferencia();

        return $consumo > 0 ? (int) floor($this->estoque / $consumo) : null;
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

    /** Formato único da ração na API (listagem, detalhe e resposta de
     *  cadastro/edição), com os indicadores de estoque calculados. */
    public function dadosApi(): array
    {
        return $this->toArray() + [
            'status_estoque' => $this->statusEstoque(),
            'nivel_estoque' => $this->nivelEstoque(),
            'dias_restantes_estoque' => $this->diasRestantesEstoque(),
            'consumo_diario_referencia' => $this->consumoDiarioReferencia(),
            'percentual_estoque' => $this->percentualEstoque(),
            'nivel_status' => $this->nivelStatus(),
            'nome_fazenda' => $this->relationLoaded('fazenda') ? $this->fazenda?->nome_fazenda : null,
        ];
    }

    /**
     * Nível do estoque para o produtor: 'critico' 🔴, 'baixo' 🟠 ou
     * 'adequado' 🟢 — tudo a partir de dados reais da ração:
     *  - crítico: zerado, acaba em até DIAS_ESTOQUE_CRITICO dias, ou abaixo
     *    da metade do estoque mínimo cadastrado;
     *  - baixo: abaixo do estoque mínimo, ou acaba dentro da margem de
     *    segurança (statusEstoque() 'baixo');
     *  - adequado: o resto.
     */
    public function nivelEstoque(): string
    {
        $dias = $this->diasRestantesEstoque();
        $minimo = (float) ($this->estoque_minimo ?? 0);

        if ($this->estoque <= 0
            || ($dias !== null && $dias <= self::DIAS_ESTOQUE_CRITICO)
            || ($minimo > 0 && $this->estoque <= $minimo / 2)) {
            return 'critico';
        }

        if (($minimo > 0 && $this->estoque <= $minimo) || $this->statusEstoque() === 'baixo') {
            return 'baixo';
        }

        return 'adequado';
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
