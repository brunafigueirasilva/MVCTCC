<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\EscopoDoUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanoAlimentarRequest;
use App\Models\Lote;
use App\Models\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CRUD de planos alimentares (Fazenda → Lote → Ração). Cada lote tem no
 * máximo um plano ATIVO: ativar/criar um plano desativa o anterior e
 * vincula a ração ao lote (lotes.racao_id) — o mesmo vínculo que a tela
 * web de Plano Alimentar e o Dashboard já usam para calcular o consumo.
 */
class PlanoAlimentarController extends Controller
{
    use EscopoDoUsuario;

    // GET /api/planos-alimentares?fazenda_id=&lote_id=&ativo=
    public function index(Request $request)
    {
        $fazenda = $this->fazendaSelecionada($request);

        $planos = $this->planosDoUsuario($request)
            ->with(['lote:id,nome_lote,fazenda_id,quantidade_animais,tipo_animal', 'racao:id,nome_racao,valor_kg,unidade', 'fazenda:id,nome_fazenda'])
            ->when($fazenda, fn ($q) => $q->where('fazenda_id', $fazenda->id))
            ->when($request->query('lote_id'), fn ($q, $v) => $q->where('lote_id', $v))
            ->when($request->has('ativo'), fn ($q) => $q->where('ativo', $request->boolean('ativo')))
            ->orderByDesc('ativo')->latest('data_inicio')
            ->get();

        return response()->json([
            'message' => 'Planos alimentares carregados com sucesso.',
            'data' => $planos->map(fn (Plano $p) => $this->formatar($p))->values(),
        ]);
    }

    // GET /api/planos-alimentares/{plano}
    public function show(Request $request, $plano)
    {
        return response()->json([
            'message' => 'Plano alimentar carregado com sucesso.',
            'data' => $this->formatar($this->buscar($request, $plano)),
        ]);
    }

    // POST /api/planos-alimentares
    public function store(PlanoAlimentarRequest $request)
    {
        $dados = $request->validated();
        $lote = $this->lotesDoUsuario($request)->findOrFail($dados['lote_id']);

        $plano = DB::transaction(function () use ($dados, $lote) {
            $plano = new Plano($dados + ['frequencia_diaria' => 1, 'ativo' => true]);
            $plano->fazenda_id = $lote->fazenda_id;
            $plano->save();

            if ($plano->ativo) {
                $this->tornarUnicoAtivo($plano, $lote);
            }

            return $plano;
        });

        return response()->json([
            'message' => 'Plano alimentar cadastrado com sucesso.',
            'data' => $this->formatar($plano->fresh(['lote', 'racao', 'fazenda'])),
        ], 201);
    }

    // PUT /api/planos-alimentares/{plano}
    public function update(PlanoAlimentarRequest $request, $plano)
    {
        $plano = $this->buscar($request, $plano);
        $dados = $request->validated();

        DB::transaction(function () use ($plano, $dados, $request) {
            $plano->fill($dados);

            if (isset($dados['lote_id'])) {
                $plano->fazenda_id = $this->lotesDoUsuario($request)->findOrFail($dados['lote_id'])->fazenda_id;
            }

            $plano->save();

            if ($plano->ativo) {
                $this->tornarUnicoAtivo($plano, $plano->lote()->firstOrFail());
            }
        });

        return response()->json([
            'message' => 'Plano alimentar atualizado com sucesso.',
            'data' => $this->formatar($plano->fresh(['lote', 'racao', 'fazenda'])),
        ]);
    }

    // DELETE /api/planos-alimentares/{plano}
    public function destroy(Request $request, $plano)
    {
        $this->buscar($request, $plano)->delete();

        return response()->json(['message' => 'Plano alimentar excluído com sucesso.']);
    }

    private function buscar(Request $request, $id): Plano
    {
        return $this->planosDoUsuario($request)->with(['lote', 'racao', 'fazenda'])->findOrFail($id);
    }

    private function tornarUnicoAtivo(Plano $plano, Lote $lote): void
    {
        Plano::where('lote_id', $lote->id)->whereKeyNot($plano->id)->where('ativo', true)->update(['ativo' => false]);
        $lote->update(['racao_id' => $plano->racao_id]);
    }

    private function formatar(Plano $plano): array
    {
        $valorKg = (float) ($plano->racao?->valor_kg ?? 0);
        $consumoDiario = $plano->consumoDiarioPrevisto();

        return $plano->toArray() + [
            'consumo_diario_previsto_kg' => $consumoDiario,
            'custo_diario_previsto' => round($consumoDiario * $valorKg, 2),
            'quantidade_por_trato_kg' => $plano->frequencia_diaria > 0
                ? round($consumoDiario / $plano->frequencia_diaria, 2)
                : $consumoDiario,
        ];
    }
}
