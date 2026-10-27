<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Cocho;
use App\Models\EstoqueMovimentacao;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Plano;
use App\Models\Racao;
use Illuminate\Http\Request;

/**
 * Consultas da API sempre escopadas ao usuário do token
 * (Usuário → Fazenda → Lote → Cocho / Ração / Plano / Movimentação).
 * Um id de outra conta simplesmente "não existe" (404) — nunca vaza nem é
 * alterado. Toda leitura/escrita dos controllers Api\* passa por aqui.
 */
trait EscopoDoUsuario
{
    protected function fazendasDoUsuario(Request $request)
    {
        return Fazenda::where('cadastro_id', $request->user()->id);
    }

    protected function fazendaIdsDoUsuario(Request $request)
    {
        return $this->fazendasDoUsuario($request)->pluck('id');
    }

    protected function lotesDoUsuario(Request $request)
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario($request));
    }

    protected function racoesDoUsuario(Request $request)
    {
        return Racao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario($request));
    }

    protected function cochosDoUsuario(Request $request)
    {
        return Cocho::whereIn('lote_id', $this->lotesDoUsuario($request)->pluck('id'));
    }

    protected function planosDoUsuario(Request $request)
    {
        return Plano::whereIn('fazenda_id', $this->fazendaIdsDoUsuario($request));
    }

    protected function movimentacoesDoUsuario(Request $request)
    {
        return EstoqueMovimentacao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario($request));
    }

    /**
     * ?fazenda_id= validado contra as fazendas do usuário: null quando não
     * veio (ou veio vazio); 404 quando é de outra conta / não existe.
     */
    protected function fazendaSelecionada(Request $request): ?Fazenda
    {
        $id = $request->query('fazenda_id', $request->input('fazenda_id'));

        if ($id === null || $id === '') {
            return null;
        }

        return $this->fazendasDoUsuario($request)->findOrFail($id);
    }
}
