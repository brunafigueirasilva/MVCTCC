<?php

namespace App\Http\Controllers;

use App\Models\AcessibilidadePreferencia;
use Illuminate\Http\Request;

class AcessibilidadeController extends Controller
{
    /**
     * Retorna as preferências de acessibilidade do usuário logado (sessão
     * "usuario_id"). Se não houver usuário logado, devolve null e o
     * front-end continua usando apenas o localStorage do navegador.
     */
    public function show(Request $request)
    {
        $usuarioId = session('usuario_id');

        if (!$usuarioId) {
            return response()->json(['autenticado' => false, 'preferencias' => null]);
        }

        $preferencias = AcessibilidadePreferencia::where('usuario_id', $usuarioId)->first();

        return response()->json([
            'autenticado' => true,
            'preferencias' => $preferencias,
        ]);
    }

    /**
     * Cria ou atualiza (upsert) as preferências de acessibilidade do
     * usuário logado, permitindo sincronização entre dispositivos.
     */
    public function store(Request $request)
    {
        $usuarioId = session('usuario_id');

        if (!$usuarioId) {
            // Usuário anônimo: nada para persistir no servidor.
            return response()->json(['autenticado' => false], 200);
        }

        // Faixas batem exatamente com os controles de
        // partials/acessibilidade.blade.php: "Tamanho do Texto" varia de
        // 80% a 150% em passos de 10, "Zoom do Sistema" de 0.5 a 1.3 em
        // passos de 0.05 (min/max um pouco mais largos aqui só de folga).
        $dados = $request->validate([
            'tema' => 'required|string|in:light,dark,contrast',
            'tamanho_fonte' => 'required|integer|min:50|max:200',
            'zoom' => 'required|numeric|min:0.5|max:1.3',
            'voz_nome' => 'nullable|string|max:255',
            'voz_velocidade' => 'required|numeric|min:0.5|max:2',
        ]);

        $preferencias = AcessibilidadePreferencia::updateOrCreate(
            ['usuario_id' => $usuarioId],
            $dados
        );

        return response()->json(['autenticado' => true, 'preferencias' => $preferencias]);
    }
}
