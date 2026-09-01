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
        'finalidade',
        'sexo',
        'idade_media',
        'quantidade_animais',
        'fazenda_id',
        'lote_origem_id',
        'racao_id',
        'data_entrada',
        'peso_medio',
        'status',
    ];

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

    public function cochos()
    {
        return $this->hasMany(Cocho::class);
    }

    /** Animais individuais cadastrados neste lote (módulo de
     *  Parto/Reprodução/Lactação/Ordenha por animal). */
    public function animais()
    {
        return $this->hasMany(Animal::class);
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