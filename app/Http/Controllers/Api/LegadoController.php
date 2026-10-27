<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cocho;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Racao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

/**
 * Executa um método dos Controllers web (defaults "alvo" = [Classe, 'metodo'])
 * e traduz o retorno para JSON, para a API não duplicar regra de negócio:
 *  - View     -> {"data": <dados que alimentariam a view>}
 *  - Redirect -> 422 {"message","errors"} se houve erro de validação/negócio;
 *                senão {"message"} (+ "data" com o recurso afetado, se a rota
 *                declarar o default "recurso": fazenda|lote|racao|cocho);
 *  - qualquer outra resposta (JSON, PDF...) passa direto.
 * Requer a middleware PonteApiLegado (sessão em memória com o usuário).
 */
class LegadoController extends Controller
{
    public function __invoke(Request $request)
    {
        $rota = $request->route();
        [$classe, $metodo] = $rota->defaults['alvo'];
        $recurso = $rota->defaults['recurso'] ?? null;
        $parametros = Arr::except($rota->parameters(), ['alvo', 'recurso']);

        $resposta = app()->call([app($classe), $metodo], $parametros);

        if ($resposta instanceof View) {
            return response()->json([
                'data' => Arr::except($resposta->getData(), ['cadastroLogado', 'errors', '__env', 'app']),
            ]);
        }

        if ($resposta instanceof RedirectResponse) {
            return $this->converterRedirect($request, $parametros, $recurso);
        }

        return $resposta;
    }

    private function converterRedirect(Request $request, array $parametros, ?string $recurso): JsonResponse
    {
        $bag = session('errors')?->getBag('default')->messages() ?? [];

        if ($bag) {
            return response()->json([
                'message' => Arr::first(Arr::flatten($bag)),
                'errors' => $bag,
            ], 422);
        }

        $mensagem = session('success') ?? session('sucesso') ?? session('perfil_success');

        if ($mensagem === null) {
            // Ex.: o Controller mandou "de volta pro login" porque o cadastro sumiu.
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $corpo = ['message' => $mensagem];

        if ($recurso && !$request->isMethod('DELETE')) {
            $corpo['data'] = $this->buscarRecurso($recurso, $parametros['id'] ?? null);
        }

        return response()->json($corpo, $recurso && $request->isMethod('POST') && !isset($parametros['id']) ? 201 : 200);
    }

    /** O recurso recém-criado (sem id na rota) ou o afetado (com id) — sempre
     *  escopado ao usuário do token. */
    private function buscarRecurso(string $recurso, $id)
    {
        $fazendaIds = Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');

        $consulta = match ($recurso) {
            'fazenda' => Fazenda::where('cadastro_id', session('usuario_id')),
            'lote' => Lote::whereIn('fazenda_id', $fazendaIds),
            'racao' => Racao::whereIn('fazenda_id', $fazendaIds),
            'cocho' => Cocho::whereIn('lote_id', Lote::whereIn('fazenda_id', $fazendaIds)->pluck('id')),
        };

        $registro = $id ? $consulta->find($id) : $consulta->latest('id')->first();

        // Cocho e ração voltam no MESMO formato da listagem (resumo do
        // lote/fazenda, status e indicadores) — senão o item editado no app
        // perdia esses campos até a próxima recarga da lista.
        return $registro instanceof Cocho || $registro instanceof Racao
            ? $registro->dadosApi()
            : $registro;
    }
}
