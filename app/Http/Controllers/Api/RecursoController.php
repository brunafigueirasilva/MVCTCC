<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\EscopoDoUsuario;
use App\Http\Controllers\Controller;
use App\Models\Cocho;
use App\Models\Lote;
use App\Models\Racao;
use App\Support\CatalogoRacoes;
use App\Support\CompatibilidadeRacao;
use Illuminate\Http\Request;

/**
 * Leituras (listar/detalhar) com JSON limpo e estável para o app Flutter.
 * Tudo escopado ao usuário do token: fazenda -> lote -> cocho/ração
 * (ver Concerns\EscopoDoUsuario). As escritas ficam nos Controllers web
 * (ver LegadoController) e nos controllers Api\* próprios.
 */
class RecursoController extends Controller
{
    use EscopoDoUsuario;

    /** Campos do lote enviados junto de cada cocho — o app (models/cocho.dart)
     *  lê nome do lote, tipo de animal, quantidade e lote.fazenda.nome_fazenda. */
    private const LOTE_DO_COCHO = 'lote:id,nome_lote,fazenda_id,tipo_animal,quantidade_animais';

    // ---------------- Fazendas ----------------

    public function fazendasIndex(Request $request)
    {
        return response()->json(['data' => $this->fazendasDoUsuario($request)->withCount('lotes')->orderBy('nome_fazenda')->get()]);
    }

    public function fazendasShow(Request $request, $id)
    {
        return response()->json(['data' => $this->fazendasDoUsuario($request)->withCount(['lotes', 'racoes', 'cochos'])->findOrFail($id)]);
    }

    // ---------------- Lotes ----------------

    /** ?fazenda_id=&tipo_animal=&finalidade=&sexo=&status=&busca= */
    public function lotesIndex(Request $request)
    {
        $lotes = $this->lotesDoUsuario($request)
            ->with(['fazenda:id,nome_fazenda', 'racao:id,nome_racao'])
            ->when($request->query('busca'), fn ($q, $b) => $q->where('nome_lote', 'like', "%{$b}%"))
            ->orderBy('nome_lote');

        foreach (['fazenda_id', 'tipo_animal', 'finalidade', 'sexo', 'status'] as $campo) {
            $lotes->when($request->query($campo), fn ($q, $v) => $q->where($campo, $v));
        }

        // aceita_ordenha: o app esconde a ordenha de lote sem fêmeas pela
        // mesma regra do web (LoteRegras::loteAceitaOrdenha).
        return response()->json(['data' => $lotes->get()->map(fn (Lote $l) => $l->toArray() + ['aceita_ordenha' => $l->aceita_ordenha])]);
    }

    public function lotesShow(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario($request)
            ->with(['fazenda:id,nome_fazenda', 'racao', 'cochos', 'origem:id,nome_lote'])
            ->findOrFail($id);

        return response()->json(['data' => $lote->toArray() + [
            'aceita_ordenha' => $lote->aceita_ordenha,
            'estoque_por_doenca' => $lote->estoquePorDoenca(),
            'plano_ativo' => $lote->planos()->where('ativo', true)->with('racao:id,nome_racao')->first(),
        ]]);
    }

    /**
     * GET /api/lotes/animais-por-lote?fazenda_id= — dados do gráfico de
     * pizza "Animais por Lote" (Web e app): cada lote ATIVO com animais da
     * fazenda escolhida (mesma regra dos cards do Dashboard), com a
     * porcentagem sobre o total. Sem fazenda: vazio, como o Dashboard.
     * Calculado na hora do banco — criar/editar/excluir lote, óbito, parto
     * etc. aparecem na próxima consulta.
     */
    public function lotesAnimaisPorLote(Request $request)
    {
        $fazenda = $this->fazendaSelecionada($request);

        if (!$fazenda) {
            return response()->json([
                'message' => 'Nenhuma fazenda selecionada.',
                'data' => ['fazenda' => null, 'total' => 0, 'lotes' => []],
            ]);
        }

        $lotes = Lote::ativos()
            ->where('fazenda_id', $fazenda->id)
            ->where('quantidade_animais', '>', 0)
            ->orderByDesc('quantidade_animais')->orderBy('nome_lote')
            ->get(['id', 'nome_lote', 'tipo_animal', 'quantidade_animais']);

        $total = (int) $lotes->sum('quantidade_animais');

        return response()->json([
            'message' => $lotes->isEmpty() ? 'Nenhum animal cadastrado nesta fazenda.' : 'Animais por lote carregados com sucesso.',
            'data' => [
                'fazenda' => $fazenda->only(['id', 'nome_fazenda']),
                'total' => $total,
                'lotes' => $lotes->map(fn (Lote $l) => [
                    'lote_id' => $l->id,
                    'nome_lote' => $l->nome_lote,
                    'tipo_animal' => $l->tipo_animal,
                    'quantidade_animais' => (int) $l->quantidade_animais,
                    'percentual' => $total > 0 ? round($l->quantidade_animais / $total * 100, 1) : 0.0,
                ])->values(),
            ],
        ]);
    }

    public function lotesOcorrencias(Request $request, $id)
    {
        return response()->json(['data' => $this->lotesDoUsuario($request)->findOrFail($id)->ocorrencias]);
    }

    /** ?data_inicio=&data_fim= */
    public function lotesOrdenhas(Request $request, $id)
    {
        $ordenhas = $this->lotesDoUsuario($request)->findOrFail($id)->ordenhas()
            ->when($request->query('data_inicio'), fn ($q, $d) => $q->whereDate('data', '>=', $d))
            ->when($request->query('data_fim'), fn ($q, $d) => $q->whereDate('data', '<=', $d))
            ->get();

        return response()->json(['data' => $ordenhas]);
    }

    // ---------------- Rações ----------------

    /**
     * ?fazenda_id=&busca=&tipo_animal=&sexo=&finalidade=
     * &status=ativa|inativa&estoque=critico|baixo|adequado
     *
     * "busca" procura no nome, tipo de animal e finalidade.
     * O filtro de estoque usa o nível calculado (Racao::nivelEstoque()).
     */
    /**
     * GET /api/racoes/catalogo?fazenda_id= — os cards "Tipos de ração"
     * (App\Support\CatalogoRacoes), com os lotes do usuário compatíveis com
     * cada modelo e quantas rações desse perfil ele já tem. O cadastro em si
     * é o POST /racoes normal (sem lote_id, com o perfil do modelo).
     */
    public function racoesCatalogo(Request $request)
    {
        $fazendaId = $request->query('fazenda_id');

        return response()->json([
            'data' => CatalogoRacoes::comResumo(
                $this->lotesDoUsuario($request)->when($fazendaId, fn ($q, $v) => $q->where('fazenda_id', $v))->get(),
                $this->racoesDoUsuario($request)->when($fazendaId, fn ($q, $v) => $q->where('fazenda_id', $v))->get(),
            )->values(),
        ]);
    }

    public function racoesIndex(Request $request)
    {
        $racoes = $this->racoesDoUsuario($request)
            ->with('fazenda:id,nome_fazenda')
            ->when($request->query('fazenda_id'), fn ($q, $v) => $q->where('fazenda_id', $v))
            ->when(trim((string) $request->query('busca')), function ($q, $busca) {
                $q->where(function ($q) use ($busca) {
                    foreach (['nome_racao', 'tipo', 'finalidade'] as $campo) {
                        $q->orWhere($campo, 'like', "%{$busca}%");
                    }
                });
            })
            ->when($request->query('tipo_animal'), fn ($q, $v) => $q->where('tipo', $v))
            ->when($request->query('sexo'), fn ($q, $v) => $q->where('sexo', $v))
            ->when($request->query('finalidade'), fn ($q, $v) => $q->where('finalidade', $v))
            ->when($request->query('status'), function ($q, $v) {
                // Rações antigas (antes da coluna) contam como ativas.
                $v === 'ativa'
                    ? $q->where(fn ($q) => $q->where('status', 'ativa')->orWhereNull('status'))
                    : $q->where('status', $v);
            })
            ->orderBy('nome_racao')
            ->get();

        if ($nivel = $request->query('estoque')) {
            $racoes = $racoes->filter(fn (Racao $r) => $r->nivelEstoque() === $nivel);
        }

        return response()->json(['data' => $racoes->map(fn (Racao $r) => $this->racaoComIndicadores($r))->values()]);
    }

    public function racoesShow(Request $request, $id)
    {
        return response()->json(['data' => $this->racaoComIndicadores($this->racoesDoUsuario($request)->with('fazenda:id,nome_fazenda')->findOrFail($id))]);
    }

    /**
     * GET /lotes/{id}/racoes-compativeis — só as rações que podem ser
     * vinculadas a este lote (regra única em CompatibilidadeRacao), das mais
     * adequadas para as menos. Cada uma informa se já está vinculada a
     * OUTRO lote (só informativo: a mesma ração pode alimentar vários
     * lotes — cada lote tem o próprio consumo planejado).
     */
    public function lotesRacoesCompativeis(Request $request, $id)
    {
        $lote = $this->lotesDoUsuario($request)->findOrFail($id);
        $racoes = $this->racoesDoUsuario($request)->with('fazenda:id,nome_fazenda')->get();

        $vinculadasAOutros = Lote::whereIn('fazenda_id', $racoes->pluck('fazenda_id')->unique())
            ->whereNotNull('racao_id')
            ->where('id', '!=', $lote->id)
            ->pluck('racao_id')
            ->map(fn ($v) => (int) $v);

        $compativeis = CompatibilidadeRacao::filtrar($lote, $racoes)->map(fn (Racao $r) => $this->racaoComIndicadores($r) + [
            'prioritaria' => CompatibilidadeRacao::pontuacao($r, $lote) === 2,
            'vinculada_a_outro_lote' => $vinculadasAOutros->contains((int) $r->id),
        ]);

        return response()->json(['data' => $compativeis->values()]);
    }

    private function racaoComIndicadores(Racao $racao): array
    {
        return $racao->dadosApi();
    }

    // ---------------- Cochos ----------------

    /** ?fazenda_id=&lote_id=&status=Indefinido|Vazio|Baixo|OK&alerta=1 */
    public function cochosIndex(Request $request)
    {
        $cochos = $this->cochosDoUsuario($request)
            ->with([self::LOTE_DO_COCHO, 'lote.fazenda:id,nome_fazenda'])
            ->when($request->query('lote_id'), fn ($q, $v) => $q->where('lote_id', $v))
            ->when($request->query('fazenda_id'), fn ($q, $v) => $q->whereIn('lote_id', Lote::where('fazenda_id', $v)->pluck('id')))
            ->orderBy('lote_id')->orderBy('numero')
            ->get()
            ->map(fn (Cocho $c) => $this->cochoComStatus($c));

        if ($status = $request->query('status')) {
            $cochos = $cochos->where('status_abastecimento', $status);
        }

        if ($request->boolean('alerta')) {
            $cochos = $cochos->where('alerta_nivel_baixo', true);
        }

        return response()->json(['data' => $cochos->values()]);
    }

    public function cochosShow(Request $request, $id)
    {
        $cocho = $this->cochosDoUsuario($request)->with([self::LOTE_DO_COCHO, 'lote.fazenda:id,nome_fazenda'])->findOrFail($id);

        return response()->json(['data' => $this->cochoComStatus($cocho)]);
    }

    /** GET /api/cochos/{id}/status — só nível e status (consulta leve, ex.: sensor/IoT). */
    public function cochosStatus(Request $request, $id)
    {
        $cocho = $this->cochosDoUsuario($request)->findOrFail($id);

        return response()->json(['data' => [
            'id' => $cocho->id,
            'nome_cocho' => $cocho->nome_cocho,
            'capacidade' => $cocho->capacidade,
            'nivel_atual' => $cocho->nivel_atual,
            'percentual_nivel' => $cocho->percentualNivel(),
            'status_abastecimento' => $cocho->statusAbastecimento(),
            'alerta_nivel_baixo' => $cocho->precisaAbastecer(),
            'percentual_alerta' => Cocho::PERCENTUAL_ALERTA,
        ]]);
    }

    /** Status pela regra única do model (alerta abaixo de Cocho::PERCENTUAL_ALERTA %). */
    private function cochoComStatus(Cocho $cocho): array
    {
        return $cocho->dadosApi();
    }
}
