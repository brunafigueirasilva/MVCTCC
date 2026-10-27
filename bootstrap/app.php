<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.cadastro' => \App\Http\Middleware\EnsureCadastroAutenticado::class,
            'api.legado' => \App\Http\Middleware\PonteApiLegado::class,
            'iot.dispositivo' => \App\Http\Middleware\AutenticarDispositivoIot::class,
        ]);

        // Toda rota /api responde em JSON (mesmo sem "Accept: application/json")
        // e no formato padrão {success, message, data | errors}.
        $middleware->api(prepend: [
            \App\Http\Middleware\PadronizarRespostaApi::class,
            \App\Http\Middleware\ForcarRespostaJson::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn ($request, $e) => $request->is('api/*') || $request->expectsJson());

        // URL/método que não bate com nenhuma rota da API: o erro acontece
        // antes do grupo "api", então não passava pela PadronizarRespostaApi
        // (vinha em inglês, sem "success" e, com APP_DEBUG, com arquivo e
        // stack trace). Agora sai no mesmo formato das demais respostas.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if (!$request->is('api/*') || $request->route()) {
                return null;
            }

            $mensagem = $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
                ? 'Rota não encontrada na API: ' . $request->method() . ' /' . $request->path()
                : '';

            return (new \App\Http\Middleware\PadronizarRespostaApi())->handle(
                $request,
                fn () => response()->json(['message' => $mensagem], $e->getStatusCode(), $e->getHeaders()),
            );
        });
    })->create();
