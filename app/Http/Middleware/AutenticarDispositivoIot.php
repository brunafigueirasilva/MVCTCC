<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentica o controlador IoT (ESP32) pelo cabeçalho "X-IoT-Key", comparado
 * com config('iot.chave') (IOT_API_KEY no .env). Sem chave configurada o
 * endpoint fica fechado (503), nunca aberto.
 */
class AutenticarDispositivoIot
{
    public function handle(Request $request, Closure $next): Response
    {
        $chaveConfigurada = (string) config('iot.chave');

        if ($chaveConfigurada === '') {
            return response()->json(['message' => 'Integração IoT não configurada no servidor (defina IOT_API_KEY no .env).'], 503);
        }

        $chaveEnviada = (string) $request->header('X-IoT-Key', '');

        if ($chaveEnviada === '' || !hash_equals($chaveConfigurada, $chaveEnviada)) {
            return response()->json(['message' => 'Dispositivo IoT não autorizado.'], 401);
        }

        return $next($request);
    }
}
