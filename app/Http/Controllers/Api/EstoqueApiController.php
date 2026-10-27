<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\EscopoDoUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MovimentacaoEstoqueRequest;
use App\Models\EstoqueMovimentacao;
use App\Models\Racao;
use App\Services\EstoqueService;
use Illuminate\Http\Request;

/**
 * Estoque de rações: consulta, entrada, saída e histórico. O saldo vive em
 * racaos.estoque e só muda pelo App\Services\EstoqueService (transação +
 * trava + histórico + nunca negativo).
 */
class EstoqueApiController extends Controller
{
    use EscopoDoUsuario;

    // GET /api/estoque?fazenda_id=
    public function index(Request $request)
    {
        $fazenda = $this->fazendaSelecionada($request);

        $racoes = $this->racoesDoUsuario($request)
            ->when($fazenda, fn ($q) => $q->where('fazenda_id', $fazenda->id))
            ->with('fazenda:id,nome_fazenda')
            ->orderBy('nome_racao')
            ->get();

        $itens = $racoes->map(fn (Racao $r) => $this->itemEstoque($r))->values();

        return response()->json([
            'message' => 'Estoque carregado com sucesso.',
            'data' => [
                'itens' => $itens,
                'totais' => [
                    'quantidade_racoes' => $racoes->count(),
                    'estoque_total_kg' => round($racoes->sum('estoque'), 2),
                    'capacidade_total_kg' => round($racoes->sum('estoque_total'), 2),
                    'valor_total' => round($racoes->sum(fn (Racao $r) => $r->estoque * ($r->valor_kg ?? 0)), 2),
                    'racoes_esgotadas' => $racoes->filter(fn (Racao $r) => $r->statusEstoque() === 'esgotado')->count(),
                    'racoes_estoque_baixo' => $racoes->filter(fn (Racao $r) => $r->statusEstoque() === 'baixo')->count(),
                ],
            ],
        ]);
    }

    // GET /api/estoque/{racaoId}
    public function show(Request $request, $racaoId)
    {
        $racao = $this->racoesDoUsuario($request)->with('fazenda:id,nome_fazenda')->findOrFail($racaoId);

        return response()->json([
            'message' => 'Estoque da ração carregado com sucesso.',
            'data' => $this->itemEstoque($racao) + [
                'ultimas_movimentacoes' => $racao->movimentacoes()->limit(10)->get(),
            ],
        ]);
    }

    // POST /api/estoque/entrada {racao_id, quantidade, valor_unitario?, data_movimentacao?, observacao?}
    public function entrada(MovimentacaoEstoqueRequest $request, EstoqueService $estoque)
    {
        $racao = $this->racoesDoUsuario($request)->findOrFail($request->validated('racao_id'));

        $movimentacao = $estoque->entrada($racao, (float) $request->validated('quantidade'), $this->contexto($request));

        return response()->json([
            'message' => 'Entrada registrada com sucesso.',
            'data' => $movimentacao->load('racao:id,nome_racao,estoque,unidade'),
        ], 201);
    }

    // POST /api/estoque/saida {racao_id, quantidade, lote_id?, data_movimentacao?, observacao?}
    public function saida(MovimentacaoEstoqueRequest $request, EstoqueService $estoque)
    {
        $racao = $this->racoesDoUsuario($request)->findOrFail($request->validated('racao_id'));

        $movimentacao = $estoque->saida($racao, (float) $request->validated('quantidade'), $this->contexto($request));

        return response()->json([
            'message' => 'Saída registrada com sucesso.',
            'data' => $movimentacao->load('racao:id,nome_racao,estoque,unidade'),
        ], 201);
    }

    // GET /api/estoque/movimentacoes?fazenda_id=&racao_id=&lote_id=&tipo=&data_inicio=&data_fim=&page=&per_page=
    public function movimentacoes(Request $request)
    {
        $filtros = $request->validate([
            'racao_id' => 'nullable|integer',
            'lote_id' => 'nullable|integer',
            'tipo' => 'nullable|in:entrada,saida,ajuste',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);
        $fazenda = $this->fazendaSelecionada($request);

        $pagina = $this->movimentacoesDoUsuario($request)
            ->with(['racao:id,nome_racao,unidade', 'lote:id,nome_lote', 'cocho:id,nome_cocho', 'cadastro:id,responsavel'])
            ->when($fazenda, fn ($q) => $q->where('fazenda_id', $fazenda->id))
            ->when($filtros['racao_id'] ?? null, fn ($q, $v) => $q->where('racao_id', $v))
            ->when($filtros['lote_id'] ?? null, fn ($q, $v) => $q->where('lote_id', $v))
            ->when($filtros['tipo'] ?? null, fn ($q, $v) => $q->where('tipo', $v))
            ->when($filtros['data_inicio'] ?? null, fn ($q, $v) => $q->whereDate('data_movimentacao', '>=', $v))
            ->when($filtros['data_fim'] ?? null, fn ($q, $v) => $q->whereDate('data_movimentacao', '<=', $v))
            ->latest('data_movimentacao')->latest('id')
            ->paginate($filtros['per_page'] ?? 20);

        return response()->json([
            'message' => 'Histórico de movimentações carregado com sucesso.',
            'data' => $pagina->items(),
            'meta' => [
                'pagina_atual' => $pagina->currentPage(),
                'ultima_pagina' => $pagina->lastPage(),
                'por_pagina' => $pagina->perPage(),
                'total' => $pagina->total(),
            ],
        ]);
    }

    private function contexto(MovimentacaoEstoqueRequest $request): array
    {
        return array_filter([
            'cadastro_id' => $request->cadastroId(),
            'lote_id' => $request->validated('lote_id'),
            'valor_unitario' => $request->validated('valor_unitario'),
            'observacao' => $request->validated('observacao'),
            'data_movimentacao' => $request->validated('data_movimentacao'),
            'origem' => 'manual',
        ], fn ($v) => $v !== null);
    }

    private function itemEstoque(Racao $racao): array
    {
        return [
            'racao_id' => $racao->id,
            'nome_racao' => $racao->nome_racao,
            'fazenda_id' => $racao->fazenda_id,
            'fazenda' => $racao->fazenda?->only(['id', 'nome_fazenda']),
            'unidade' => $racao->unidade ?? 'kg',
            'estoque' => (float) $racao->estoque,
            'estoque_total' => $racao->estoque_total !== null ? (float) $racao->estoque_total : null,
            'consumo_diario' => $racao->consumoDiarioReferencia(),
            'valor_kg' => $racao->valor_kg !== null ? (float) $racao->valor_kg : null,
            'valor_total' => round($racao->estoque * ($racao->valor_kg ?? 0), 2),
            'status_estoque' => $racao->statusEstoque(),
            'percentual_estoque' => $racao->percentualEstoque(),
            'dias_restantes_estoque' => $racao->diasRestantesEstoque(),
        ];
    }
}
