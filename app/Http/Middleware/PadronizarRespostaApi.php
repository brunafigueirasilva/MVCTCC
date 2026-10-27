<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Padroniza toda resposta JSON da API:
 *   sucesso: {"success": true,  "message": "...", "data": ...}
 *   erro:    {"success": false, "message": "...", "errors": {...}}
 *
 * É ADITIVO de propósito: só acrescenta "success" (e "message" quando
 * faltar) e traduz as mensagens padrão do framework — as chaves que o app
 * Flutter já lê ("data", "message", "errors", "token", "usuario") continuam
 * exatamente onde estavam. Listas soltas (raras) viram {"data": [...]};
 * sucesso sem "data" ganha "data" com uma cópia das demais chaves.
 *
 * Fica por fora de todas as rotas /api, então também alcança os erros já
 * convertidos em resposta (validação 422, auth 401, 404, 429, 500...).
 */
class PadronizarRespostaApi
{
    private const MENSAGENS_PADRAO = [
        400 => 'Requisição inválida.',
        401 => 'Não autenticado. Faça login novamente.',
        403 => 'Você não tem permissão para acessar este recurso.',
        404 => 'Registro não encontrado.',
        405 => 'Método não permitido para esta rota.',
        419 => 'Sessão expirada. Faça login novamente.',
        422 => 'Os dados informados são inválidos.',
        429 => 'Muitas requisições seguidas. Aguarde um minuto e tente novamente.',
        500 => 'Não foi possível realizar a operação.',
        503 => 'Serviço temporariamente indisponível.',
    ];

    /** Mensagens em inglês do Laravel/Symfony que trocamos pelas de cima. */
    private const MENSAGENS_FRAMEWORK = [
        'Unauthenticated.',
        'This action is unauthorized.',
        'Not Found',
        'Server Error',
        'Too Many Attempts.',
        'The route could not be found.',
        'Forbidden',
        'Method Not Allowed',
        'Service Unavailable',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $resposta = $next($request);

        if (!$resposta instanceof JsonResponse) {
            return $resposta;
        }

        $status = $resposta->getStatusCode();
        $dados = $resposta->getData(true);
        $sucesso = $status < 400;

        if (!is_array($dados) || array_is_list($dados)) {
            $dados = ['data' => $dados];
        }

        $mensagem = $dados['message'] ?? $dados['mensagem'] ?? null;

        if (
            !is_string($mensagem) || $mensagem === ''
            || in_array($mensagem, self::MENSAGENS_FRAMEWORK, true)
            || str_starts_with($mensagem, 'No query results for model')
            || (!$sucesso && $status >= 500 && !config('app.debug'))
        ) {
            $mensagem = $sucesso
                ? 'Operação realizada com sucesso.'
                : (self::MENSAGENS_PADRAO[$status] ?? 'Não foi possível realizar a operação.');
        }

        $padronizado = ['success' => $sucesso, 'message' => $mensagem] + $dados;
        $padronizado['message'] = $mensagem;

        // Respostas que nasceram sem "data" (login: token/usuario; alertas:
        // count/alertas; acessibilidade: preferencias) ganham uma cópia do
        // conteúdo em "data" — quem lê json['data'] recebia null. As chaves
        // no topo continuam onde o app já as lê.
        $conteudo = array_diff_key($dados, array_flip(['success', 'message', 'mensagem', 'errors']));
        if ($sucesso && !array_key_exists('data', $dados) && $conteudo) {
            $padronizado['data'] = $conteudo;
        }

        if (!$sucesso && !array_key_exists('errors', $padronizado)) {
            $padronizado['errors'] = (object) [];
        }

        $resposta->setData($padronizado);

        return $resposta;
    }
}
