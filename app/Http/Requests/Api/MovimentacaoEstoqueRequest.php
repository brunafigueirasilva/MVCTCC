<?php

namespace App\Http\Requests\Api;

use App\Models\Fazenda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Entrada/saída manual de estoque — pela API (token) e pela tela de Estoque
 * (sessão), com as MESMAS regras. racao_id e lote_id precisam existir E
 * pertencer ao usuário logado; o lote (se informado) precisa
 * ser da mesma fazenda da ração. O saldo suficiente é checado dentro da
 * transação, no EstoqueService (com a linha da ração travada).
 */
class MovimentacaoEstoqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->cadastroId() !== null;
    }

    /** Usuário do token (API) ou da sessão (web). */
    public function cadastroId(): ?int
    {
        $id = $this->user()?->id ?? session('usuario_id');

        return $id ? (int) $id : null;
    }

    public function rules(): array
    {
        $fazendaIds = Fazenda::where('cadastro_id', $this->cadastroId())->pluck('id');

        return [
            'racao_id' => ['required', 'integer', Rule::exists('racaos', 'id')->whereIn('fazenda_id', $fazendaIds)],
            'quantidade' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'lote_id' => ['nullable', 'integer', Rule::exists('lotes', 'id')->whereIn('fazenda_id', $fazendaIds)],
            'valor_unitario' => ['nullable', 'numeric', 'min:0'],
            'data_movimentacao' => ['nullable', 'date', 'before_or_equal:now'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'racao_id.required' => 'Selecione a ração.',
            'racao_id.exists' => 'Ração inválida — selecione uma das suas rações.',
            'quantidade.required' => 'Informe a quantidade.',
            'quantidade.numeric' => 'A quantidade deve ser um número.',
            'quantidade.gt' => 'A quantidade deve ser maior que zero.',
            'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
            'valor_unitario.min' => 'O valor unitário não pode ser negativo.',
            'data_movimentacao.before_or_equal' => 'A data da movimentação não pode ser no futuro.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty() || !$this->filled('lote_id')) {
                return;
            }

            $fazendaRacao = \App\Models\Racao::whereKey($this->input('racao_id'))->value('fazenda_id');
            $fazendaLote = \App\Models\Lote::whereKey($this->input('lote_id'))->value('fazenda_id');

            if ($fazendaRacao !== $fazendaLote) {
                $validator->errors()->add('lote_id', 'O lote precisa ser da mesma fazenda da ração.');
            }
        });
    }
}
