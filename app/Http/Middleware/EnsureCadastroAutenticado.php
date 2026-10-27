<?php

namespace App\Http\Middleware;

use App\Models\Cadastro;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guarda central de autenticação do sistema: aplicada a todas as rotas
 * "internas" (dashboard, fazendas, lotes, rações, cochos, plano alimentar,
 * ordenha/leite, relatórios, perfil...) via o grupo em routes/web.php.
 *
 * Antes desta middleware, cada Controller checava a sessão ("usuario_id")
 * manualmente, método por método — vários esqueciam a checagem (ex.:
 * OrdenhaLeiteController::historicoJson, RelatorioController::rankingJson,
 * LoteController::historicoOrdenha, AssistenteIaController::perguntar),
 * deixando esses endpoints acessíveis sem login. Centralizar aqui garante
 * que NENHUMA rota do grupo protegido fique acessível sem sessão válida,
 * mesmo que um Controller novo esqueça de checar.
 *
 * Resposta de "não autenticado" tem dois formatos, conforme o consumidor:
 * - Requisição que espera JSON (fetch/AJAX com "Accept: application/json"):
 *   401 em JSON — nunca um redirect, que quebraria o `resposta.json()` do
 *   front-end (o fetch seguiria o redirect e tentaria decodificar HTML).
 * - Navegação normal: redirect para a tela de login, guardando a URL atual
 *   para voltar automaticamente depois (ver redirect()->intended() em
 *   CadastroController::autenticar()).
 */
class EnsureCadastroAutenticado
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuarioId = $request->session()->get('usuario_id');

        // Sessão aponta para uma conta que não existe mais (ex.: excluída
        // em outra aba/dispositivo) — trata como não autenticado e limpa o
        // resíduo da sessão, em vez de deixar os Controllers tratarem cada
        // um a sua maneira.
        if ($usuarioId && !Cadastro::whereKey($usuarioId)->exists()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $usuarioId = null;
        }

        if (!$usuarioId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sua sessão expirou. Faça login novamente.',
                ], 401);
            }

            // withErrors() (em vez de with('error', ...)) porque a tela de
            // login só sabe renderizar a bag padrão de erros de validação
            // ($errors->all(), ver resources/views/login.blade.php).
            return redirect()->guest(route('login.index'))
                ->withErrors(['login' => 'Faça login para continuar.']);
        }

        return $next($request);
    }
}
