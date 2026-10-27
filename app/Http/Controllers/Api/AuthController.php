<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CadastroController;
use App\Http\Controllers\Controller;
use App\Models\Cadastro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    // POST /api/auth/register
    public function register(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:cadastros,email',
            'password' => ['required', 'string', 'confirmed', 'regex:' . CadastroController::SENHA_REGEX],
            'telefone' => 'required|string|max:20',
        ], [
            'password.regex' => CadastroController::SENHA_MENSAGEM,
            ...CadastroController::MENSAGENS_CADASTRO,
        ]);

        $cadastro = Cadastro::create([
            'responsavel' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => Hash::make($dados['password']),
            'telefone' => $dados['telefone'],
        ]);

        return response()->json([
            'message' => 'Conta criada com sucesso!',
            'token' => $cadastro->createToken($request->input('dispositivo', 'api'))->plainTextToken,
            'usuario' => $cadastro,
        ], 201);
    }

    // POST /api/auth/login  ->  { token, usuario }
    public function login(Request $request)
    {
        $dados = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'dispositivo' => 'nullable|string|max:100',
        ]);

        $cadastro = Cadastro::where('email', $dados['email'])->first();

        // Mesma mensagem genérica para e-mail inexistente e senha errada
        // (evita enumeração de contas), como no login web.
        if (!$cadastro || !Hash::check($dados['password'], $cadastro->senha)) {
            return response()->json([
                'message' => 'E-mail ou senha inválidos.',
                'errors' => ['email' => ['E-mail ou senha inválidos.']],
            ], 422);
        }

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $cadastro->createToken($dados['dispositivo'] ?? 'api')->plainTextToken,
            'usuario' => $cadastro,
        ]);
    }

    // POST /api/auth/google { access_token } — token obtido no app Flutter (google_sign_in).
    // Cria a conta na primeira vez, como o login web via Google.
    public function google(Request $request)
    {
        $dados = $request->validate(['access_token' => 'required|string']);

        try {
            $usuarioGoogle = Socialite::driver('google')->stateless()->userFromToken($dados['access_token']);
        } catch (Throwable $e) {
            Log::warning('Login via Google (API) falhou', ['mensagem' => $e->getMessage()]);

            return response()->json(['message' => 'Token do Google inválido ou expirado.'], 401);
        }

        $cadastro = Cadastro::firstOrCreate(
            ['email' => $usuarioGoogle->getEmail()],
            [
                'responsavel' => $usuarioGoogle->getName(),
                // Conta só acessível via Google: senha aleatória e nunca exibida.
                'senha' => Hash::make(Str::random(40)),
                'telefone' => '',
            ]
        );

        // Foto de perfil da conta Google (se ainda não houver foto própria).
        $cadastro->usarFotoDoGoogle($usuarioGoogle->getAvatar());

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $cadastro->createToken('google')->plainTextToken,
            'usuario' => $cadastro,
        ]);
    }

    // POST /api/auth/logout — revoga só o token usado nesta requisição
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso!']);
    }

    // GET /api/auth/me
    public function me(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }
}
