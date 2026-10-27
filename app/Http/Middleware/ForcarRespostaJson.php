<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Faz toda requisição /api ser tratada como "espera JSON" (erros de validação
 *  viram 422 em JSON e não redirects, autenticação ausente vira 401). */
class ForcarRespostaJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
