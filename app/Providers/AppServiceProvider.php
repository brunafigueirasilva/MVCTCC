<?php

namespace App\Providers;

use App\Models\Cadastro;
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
        // Disponibiliza o administrador logado para qualquer view (usado
        // pelo modal "Editar Perfil" presente na navbar de todas as telas).
        View::composer('*', function ($view) {
            $view->with('cadastroLogado', session('usuario_id')
                ? Cadastro::find(session('usuario_id'))
                : null);
        });
    }
}
