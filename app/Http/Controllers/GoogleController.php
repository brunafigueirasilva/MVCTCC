<?php

namespace App\Http\Controllers;

use App\Models\cadastro;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Socialite;

class GoogleController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    // Criar função para fazer login
    public function callback(){
        // Tratamento de erros
        try{
            // Pegar os dados enviados pelo google
            $usuarioGoogle = Socialite::driver('google')->user();

            // Fazer adaptação para o contexto do banco
            // Procurar um usuário pelo email
            $user = Cadastro::where(
                'email',
                $usuarioGoogle->getEmail()
            )->first();
            // Se não existir usuario, criar usuario
            if(!$user){
                $user = Cadastro::create([
                    'responsavel' => $usuarioGoogle->getName(),
                    'email' => $usuarioGoogle->getEmail(),
                    'senha' => Hash::make('1234'),
                    'telefone' => '',
                ]);
            }

            // Faço o login no sistema
            // Auth::login($user);
            session([
                'usuario_id' => $user->id,
                'usuario_nome' => $user->responsavel,
                'usuario_email' => $user->email
            ]);

            return redirect()
                ->route('cadastro.index')
                ->with('success', 'Login realizado com sucesso!');

        } catch(Exception $e){

        }
    }
}