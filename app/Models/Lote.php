<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';

    protected $fillable = [
        'nome_lote',
        'tipo_animal',
        'raca',
        'finalidade',
        'sexo',
        'idade_media',
        'quantidade_animais',
        'quantidade_cochos',
        'fazenda_id',
        'lote_origem_id',
        'racao_id',
        'peso_medio',
        'data_entrada',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantidade_animais' => 'integer',
            'quantidade_cochos' => 'integer',
            'peso_medio' => 'float',
            'data_entrada' => 'date:Y-m-d',
        ];
    }

    /** Lote de espécie leiteira com fêmeas (sexo "Fêmea" ou "Misto") —
     *  lote de machos nunca tem ordenha. Enviado ao app nos endpoints de
     *  lote da API (não em $appends: lote carregado com colunas parciais
     *  sairia "false" por engano). */
    public function getAceitaOrdenhaAttribute(): bool
    {
        return \App\Support\LoteRegras::loteAceitaOrdenha($this->tipo_animal, $this->sexo);
    }

    // --- Relacionamentos Existentes ---

    public function fazenda()
    {
        return $this->belongsTo(Fazenda::class, 'fazenda_id');
    }

    /** Ração vinculada explicitamente a este lote na tela de Plano
     *  Alimentar — null se o usuário ainda não vinculou nenhuma. */
    public function racao()
    {
        return $this->belongsTo(Racao::class, 'racao_id');
    }

    /** Cochos gerados automaticamente a partir de "quantidade_cochos" (ver
     *  LoteController::sincronizarCochos()), sempre na ordem do "numero". */
    public function cochos()
    {
        return $this->hasMany(Cocho::class)->orderBy('numero');
    }

    /** Registros de ordenha deste lote, mais recente primeiro. */
    public function ordenhas()
    {
        return $this->hasMany(Ordenha::class)->latest('data')->latest('horario');
    }

    /** Planos alimentares do lote (o ativo é o vigente). */
    public function planos()
    {
        return $this->hasMany(Plano::class, 'lote_id');
    }

    /** Saídas de ração usadas nos cochos deste lote. */
    public function movimentacoesEstoque()
    {
        return $this->hasMany(EstoqueMovimentacao::class, 'lote_id');
    }

    /** Histórico de ocorrências do lote (óbitos etc.), mais recente primeiro. */
    public function ocorrencias()
    {
        return $this->hasMany(LoteOcorrencia::class)->latest('data_ocorrencia')->latest('id');
    }

    /** Se este lote é um lote de isolamento (criado por "Registrar
     *  Doença"), este é o único lote pra onde os animais dele podem
     *  voltar — null em lotes "normais". */
    public function origem()
    {
        return $this->belongsTo(Lote::class, 'lote_origem_id');
    }

    /** Ocorrências de OUTROS lotes que citam este como "o outro lote
     *  envolvido" (ex.: uma recuperação registrada no lote de origem que
     *  aponta de volta pro lote de isolamento de onde os animais saíram).
     *  Usado pra calcular o estoque por doença sem duplicar dado. */
    public function ocorrenciasComoDestino()
    {
        return $this->hasMany(LoteOcorrencia::class, 'lote_destino_id');
    }

    /** Quantos animais deste lote (que precisa ser um lote de isolamento)
     *  ainda estão com cada doença — soma das entradas registradas menos
     *  as recuperações já devolvidas pro lote de origem e menos os óbitos
     *  já atribuídos a cada doença. Se não houver nenhuma entrada
     *  registrada (lote antigo, de antes desse controle existir), cai num
     *  balde único "Não especificada" com o total do lote, pra não travar
     *  o fluxo de devolução/óbito. */
    public function estoquePorDoenca(): array
    {
        $estoque = [];

        foreach ($this->ocorrencias->where('tipo', 'entrada') as $entrada) {
            $estoque[$entrada->doenca] = ($estoque[$entrada->doenca] ?? 0) + $entrada->quantidade;
        }

        foreach ($this->ocorrenciasComoDestino->where('tipo', 'recuperacao') as $recuperacao) {
            $estoque[$recuperacao->doenca] = ($estoque[$recuperacao->doenca] ?? 0) - $recuperacao->quantidade;
        }

        foreach ($this->ocorrencias->where('tipo', 'morte')->whereNotNull('doenca') as $morte) {
            $estoque[$morte->doenca] = ($estoque[$morte->doenca] ?? 0) - $morte->quantidade;
        }

        $estoque = array_filter($estoque, fn ($quantidade) => $quantidade > 0);

        if (empty($estoque) && $this->quantidade_animais > 0) {
            $estoque = ['Não especificada' => $this->quantidade_animais];
        }

        return $estoque;
    }

    /** Só os lotes com status "Ativo" — um lote encerrado não deve mais
     *  ser tratado como se estivesse em produção. */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'Ativo');
    }

}