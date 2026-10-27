<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Resolve a "Unidade Produtiva" (fazenda) selecionada na barra seletora do
 * Dashboard/Ordenha e Leite/Plano Alimentar/Relatório, com persistência em
 * SESSÃO: antes, cada uma dessas telas só enxergava a seleção via
 * "?fazenda_id=" na própria URL, então trocar de página (pelo menu lateral,
 * por exemplo) sempre perdia a fazenda escolhida e voltava pro estado "sem
 * fazenda selecionada". Agora, uma vez escolhida em qualquer uma dessas
 * telas, ela fica valendo nas outras também até o usuário trocar de novo.
 */
trait ResolveFazendaSelecionada
{
    /** Chave de sessão compartilhada por todas as telas que usam este trait. */
    private const SESSAO_FAZENDA_SELECIONADA = 'fazenda_selecionada_id';

    /**
     * $fazendas precisa ser a lista de fazendas do usuário LOGADO — nunca
     * confiamos num id vindo da URL/sessão sem checar que ele pertence a
     * essa lista, senão bastaria trocar o "fazenda_id" pra ver dados de
     * outra conta.
     */
    protected function resolverFazendaSelecionadaComSessao(Request $request, Collection $fazendas): ?int
    {
        // A barra seletora sempre manda "?fazenda_id=" (mesmo vazio, quando
        // volta pra "Escolha uma fazenda") — então a PRESENÇA do parâmetro
        // na URL (não só o valor) é o que indica "o usuário decidiu algo
        // agora nesta tela", e tem prioridade sobre o que estava salvo.
        if ($request->query->has('fazenda_id')) {
            $valor = $request->query('fazenda_id');
            $fazendaSelecionadaId = ($valor !== null && $valor !== '') ? (int) $valor : null;

            if ($fazendaSelecionadaId && !$fazendas->contains('id', $fazendaSelecionadaId)) {
                $fazendaSelecionadaId = null;
            }

            session([self::SESSAO_FAZENDA_SELECIONADA => $fazendaSelecionadaId]);

            return $fazendaSelecionadaId;
        }

        // Sem escolha nesta requisição: usa a última selecionada (nesta
        // tela ou em qualquer outra da mesma família), se ainda for válida
        // — a fazenda pode ter sido excluída, ou a sessão pode ser de outro
        // usuário que já deslogou e outro logou no mesmo navegador.
        $fazendaSelecionadaId = session(self::SESSAO_FAZENDA_SELECIONADA);
        $fazendaSelecionadaId = $fazendaSelecionadaId ? (int) $fazendaSelecionadaId : null;

        if ($fazendaSelecionadaId && !$fazendas->contains('id', $fazendaSelecionadaId)) {
            $fazendaSelecionadaId = null;
            session()->forget(self::SESSAO_FAZENDA_SELECIONADA);
        }

        // Com uma única fazenda cadastrada não faz sentido pedir pra
        // escolher — ela já vem selecionada de cara (e fica salva, pra as
        // próximas telas nem precisarem cair aqui de novo).
        if (!$fazendaSelecionadaId && $fazendas->count() === 1) {
            $fazendaSelecionadaId = $fazendas->first()->id;
            session([self::SESSAO_FAZENDA_SELECIONADA => $fazendaSelecionadaId]);
        }

        return $fazendaSelecionadaId;
    }
}
