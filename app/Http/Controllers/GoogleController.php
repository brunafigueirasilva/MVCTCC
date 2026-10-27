<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // LOGIN/CADASTRO VIA GOOGLE
    public function callback(Request $request)
    {
        try {
            $usuarioGoogle = Socialite::driver('google')->user();

            $usuario = Cadastro::where('email', $usuarioGoogle->getEmail())->first();

            if (!$usuario) {
                $usuario = Cadastro::create([
                    'responsavel' => $usuarioGoogle->getName(),
                    'email' => $usuarioGoogle->getEmail(),
                    // Senha aleatória e nunca exibida/enviada ao usuário —
                    // esta conta só é acessada via Google. Um valor fixo
                    // (como o '1234' usado antes) permitiria a qualquer um
                    // logar pelo formulário normal de e-mail/senha em
                    // qualquer conta criada por aqui, sem nunca ter passado
                    // pelo Google.
                    'senha' => Hash::make(Str::random(40)),
                    'telefone' => '',
                ]);
            }

            // Foto de perfil da conta Google (se ainda não houver foto própria).
            $usuario->usarFotoDoGoogle($usuarioGoogle->getAvatar());

            // Mesma proteção contra fixação de sessão do login tradicional
            // (ver CadastroController::autenticar).
            $request->session()->regenerate();

            session([
                'usuario_id' => $usuario->id,
                'usuario_nome' => $usuario->responsavel,
                'usuario_email' => $usuario->email,
            ]);

            return redirect()
                ->intended(route('cadastro.index'))
                ->with('success', 'Login realizado com sucesso!');
        } catch (Throwable $e) {
            // Antes, qualquer falha (usuário cancelou no Google, state
            // expirado/inválido, erro de rede) era engolida em silêncio por
            // um catch vazio — a requisição terminava sem nenhuma resposta,
            // gerando um erro 500 genérico e sem registro do que houve.
            Log::warning('Login via Google falhou', ['mensagem' => $e->getMessage()]);

            return redirect()
                ->route('login.index')
                ->withErrors(['login' => 'Não foi possível concluir o login com o Google. Tente novamente.']);
        }
    }
}
