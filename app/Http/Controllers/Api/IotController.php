<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\EscopoDoUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LeituraIotRequest;
use App\Services\CochoIotService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Integração IoT dos cochos:
 *  - POST /api/iot/leitura (dispositivo, cabeçalho X-IoT-Key): recebe a
 *    leitura do sensor de distância ou da balança;
 *  - GET  /api/cochos/{id}/iot e /iot/historico (usuário, token Sanctum):
 *    situação atual, indicadores e histórico para gráficos — escopado às
 *    fazendas do usuário (cocho de outra conta = 404).
 */
class IotController extends Controller
{
    use EscopoDoUsuario;

    public function __construct(private CochoIotService $iot)
    {
    }

    public function leitura(LeituraIotRequest $request)
    {
        $resultado = $this->iot->registrarLeitura($request->cocho, $request->sensor, $request->validated());
        $cocho = $request->cocho->fresh();

        return response()->json([
            'message' => $resultado['duplicada'] ? 'Leitura já registrada anteriormente.' : 'Leitura registrada.',
            'data' => [
                'leitura' => $resultado['leitura']->dadosApi(),
                'cocho' => [
                    'cocho_id' => $cocho->id,
                    'capacidade_total' => (float) $cocho->capacidade,
                    'quantidade_atual' => (float) $cocho->nivel_atual,
                    'nivel_percentual' => (float) ($cocho->percentualNivel() ?? 0),
                    'status_abastecimento' => $cocho->statusAbastecimento(),
                ],
            ],
        ], $resultado['duplicada'] ? 200 : 201);
    }

    public function cocho(Request $request, $id)
    {
        $cocho = $this->cochosDoUsuario($request)->findOrFail($id);

        return response()->json(['data' => $this->iot->resumo($cocho)]);
    }

    /** ?periodo=hoje|7d|30d (padrão: hoje) */
    public function historico(Request $request, $id)
    {
        $dados = $request->validate([
            'periodo' => ['nullable', Rule::in(CochoIotService::PERIODOS)],
        ], ['periodo.in' => 'Período inválido: use hoje, 7d ou 30d.']);

        $cocho = $this->cochosDoUsuario($request)->findOrFail($id);

        return response()->json(['data' => $this->iot->historico($cocho, $dados['periodo'] ?? 'hoje')]);
    }
}
