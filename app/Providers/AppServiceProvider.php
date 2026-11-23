<?php

namespace App\Providers;

use App\Models\Cadastro;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limite das leituras IoT por dispositivo (chave + IP) — não depende
        // de usuário logado, o ESP32 não tem sessão nem token de usuário.
        RateLimiter::for('iot', fn (Request $request) => Limit::perMinute(240)
            ->by(sha1((string) $request->header('X-IoT-Key')) . '|' . $request->ip()));

        // Disponibiliza o administrador logado para qualquer view (usado
        // pelo modal "Editar Perfil" presente na navbar de todas as telas).
        // O composer roda para CADA view e partial renderizado (layout +
        // ~12 partials por tela): sem memorizar, eram 12-14 consultas
        // idênticas ao mesmo usuário por página. Guarda o resultado nos
        // atributos da requisição atual — nunca entre requisições/usuários.
        View::composer('*', function ($view) {
            $usuarioId = session('usuario_id');
            if (! $usuarioId) {
                $view->with('cadastroLogado', null);

                return;
            }

            $requisicao = request();
            $chave = 'cadastroLogado:' . $usuarioId;
            if (! $requisicao->attributes->has($chave)) {
                $requisicao->attributes->set($chave, Cadastro::find($usuarioId));
            }

            $view->with('cadastroLogado', $requisicao->attributes->get($chave));
        });
    }
}
