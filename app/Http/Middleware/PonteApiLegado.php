<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ponte entre a API (token Sanctum) e os Controllers web, que identificam o
 * usuário por session('usuario_id') e sinalizam sucesso/erro via flash
 * (with()/withErrors()). Em vez de duplicar toda a regra de negócio, a API
 * reaproveita esses Controllers: esta middleware monta uma sessão em memória
 * (driver "array", descartada ao fim da requisição — nada vai para o banco)
 * com o usuário do token, e o Api\LegadoController converte o resultado
 * (view/redirect) em JSON.
 */
class PonteApiLegado
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['session.driver' => 'array']);

        $store = app('session')->driver('array');
        $store->flush();

        app()->instance('session.store', $store);
        app('redirect')->setSession($store);
        $request->setLaravelSession($store);

        if ($usuario = auth('sanctum')->user()) {
            $store->put([
                'usuario_id' => $usuario->id,
                'usuario_nome' => $usuario->responsavel,
                'usuario_email' => $usuario->email,
            ]);
        }

        return $next($request);
    }
}
