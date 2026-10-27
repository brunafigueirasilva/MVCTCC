<?php

namespace App\Http\Requests\Api;

use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use App\Support\CompatibilidadeRacao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Criação/edição de plano alimentar pela API. Lote e ração precisam ser do
 * usuário do token e da MESMA fazenda; a fazenda do plano é sempre a do
 * lote (nunca enviada pelo cliente). Na edição (PUT) os campos são
 * opcionais — só o que vier é alterado.
 */
class PlanoAlimentarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('quantidade_recomendada')) {
            $this->merge([
                'quantidade_recomendada' => str_replace(',', '.', (string) $this->input('quantidade_recomendada')),
            ]);
        }
    }

    public function rules(): array
    {
        $fazendaIds = Fazenda::where('cadastro_id', $this->user()->id)->pluck('id');
        $edicao = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $obrigatorio = $edicao ? 'sometimes' : 'required';

        return [
            'lote_id' => [$obrigatorio, 'integer', Rule::exists('lotes', 'id')->whereIn('fazenda_id', $fazendaIds)],
            'racao_id' => [$obrigatorio, 'integer', Rule::exists('racaos', 'id')->whereIn('fazenda_id', $fazendaIds)],
            'quantidade_recomendada' => [$obrigatorio, 'numeric', 'gt:0', 'max:1000'],
            'frequencia_diaria' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'data_inicio' => [$obrigatorio, 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'ativo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'lote_id.required' => 'Selecione o lote.',
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'racao_id.required' => 'Selecione a ração.',
            'racao_id.exists' => 'Ração inválida — selecione uma das suas rações.',
            'quantidade_recomendada.required' => 'Informe a quantidade recomendada (kg por animal por dia).',
            'quantidade_recomendada.numeric' => 'A quantidade recomendada deve ser um número.',
            'quantidade_recomendada.gt' => 'A quantidade recomendada deve ser maior que zero.',
            'frequencia_diaria.min' => 'A frequência deve ser de pelo menos 1 trato por dia.',
            'frequencia_diaria.max' => 'A frequência máxima é de 12 tratos por dia.',
            'data_inicio.required' => 'Informe a data de início do plano.',
            'data_fim.after_or_equal' => 'A data de término não pode ser anterior à data de início.',
        ];
    }

    /** Lote e ração da mesma fazenda (considerando o plano atual na edição). */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $plano = $this->route('plano') ? \App\Models\Plano::find($this->route('plano')) : null;
            $loteId = $this->input('lote_id', $plano?->lote_id);
            $racaoId = $this->input('racao_id', $plano?->racao_id);

            if ($loteId && $racaoId
                && Lote::whereKey($loteId)->value('fazenda_id') !== Racao::whereKey($racaoId)->value('fazenda_id')) {
                $validator->errors()->add('racao_id', 'A ração precisa ser da mesma fazenda do lote.');

                return;
            }

            // Tipo de animal, sexo, finalidade e status — mesma regra da
            // lista de rações compatíveis (App\Support\CompatibilidadeRacao).
            $lote = $loteId ? Lote::find($loteId) : null;
            $racao = $racaoId ? Racao::find($racaoId) : null;
            if ($lote && $racao && !CompatibilidadeRacao::compativel($racao, $lote)) {
                $validator->errors()->add('racao_id', CompatibilidadeRacao::MENSAGEM);
            }
        });
    }
}
