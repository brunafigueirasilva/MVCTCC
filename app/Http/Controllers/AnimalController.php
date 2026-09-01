<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Models\Animal;
use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Lactacao;
use App\Models\Lote;
use App\Models\Ordenha;
use App\Support\AnimalRegras;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Módulo de Parto, Reprodução, Lactação e Ordenha por ANIMAL individual —
 * aditivo ao módulo de Lote (LoteController::registrarParto/registrarOrdenha),
 * que continua existindo separadamente para quem prefere registrar em
 * agregado, por grupo. Aqui o rastreio é por animal específico, com
 * lactações numeradas e histórico próprio.
 *
 * Toda a lógica de cascata (parto → lactação → status do animal → ordenha
 * liberada/bloqueada) fica centralizada aqui dentro, em transações — mesmo
 * padrão do LoteController, sem camada de serviço/observers.
 */
class AnimalController extends Controller
{
    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function lotesDoUsuarioIds()
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario())->pluck('id');
    }

    private function animaisDoUsuario()
    {
        return Animal::whereIn('lote_id', $this->lotesDoUsuarioIds());
    }

    private function exigirLogin()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login.index');
        }

        return null;
    }

    /** Opções fixas dos selects, repassadas pra view montar os <option>. */
    private function opcoesAnimal(): array
    {
        return [
            'sexosAnimal' => AnimalRegras::SEXOS,
            'statusAnimal' => AnimalRegras::STATUS,
            'statusReprodutivo' => AnimalRegras::STATUS_REPRODUTIVO,
            'tiposParto' => AnimalRegras::TIPOS_PARTO,
            'turnosOrdenha' => AnimalRegras::TURNOS_ORDENHA,
            'hoje' => now()->format('Y-m-d'),
        ];
    }

    // ==========================================
    // CRUD — acessado a partir da própria tela de Lote (cadastrolote.blade.php),
    // não existe mais uma listagem própria de Animal.
    // ==========================================

    public function add(AnimalRequest $request)
    {
        $dados = $request->validated();

        Animal::create([
            'lote_id' => $dados['lote_id'],
            'nome' => $dados['nome'],
            'identificador' => $dados['identificador'] ?? null,
            'sexo' => $dados['sexo'],
            'raca' => $dados['raca'] ?? null,
            'data_nascimento' => $dados['data_nascimento'],
            'peso' => $dados['peso'] ?? null,
            'status' => $dados['status'] ?? 'Ativo',
        ]);

        return redirect()->route('lote.listar')->with('success', 'Animal cadastrado com sucesso!');
    }

    // A edição acontece via modal na própria tela de Lote.
    public function atualizar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $this->animaisDoUsuario()->findOrFail($id);

        return redirect()->route('lote.listar');
    }

    public function update(AnimalRequest $request, $id)
    {
        $dados = $request->validated();

        $animal = $this->animaisDoUsuario()->findOrFail($id);

        $animal->update([
            'lote_id' => $dados['lote_id'],
            'nome' => $dados['nome'],
            'identificador' => $dados['identificador'] ?? null,
            'sexo' => $dados['sexo'],
            'raca' => $dados['raca'] ?? null,
            'data_nascimento' => $dados['data_nascimento'],
            'peso' => $dados['peso'] ?? null,
            'status' => $dados['status'] ?? $animal->status,
        ]);

        return redirect()->route('lote.listar')->with('success', 'Animal atualizado com sucesso!');
    }

    public function deletar($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->findOrFail($id);
        $animal->delete();

        return redirect()->route('lote.listar')->with('success', 'Animal excluído com sucesso!');
    }

    // ==========================================
    // TELA DE DETALHES (Resumo / Reprodução / Partos / Ordenhas /
    // Lactações / Histórico)
    // ==========================================

    public function detalhe($id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->with([
            'lote', 'partos.bezerros', 'lactacoes', 'lactacaoAtiva', 'ordenhas',
        ])->findOrFail($id);

        $lactacaoAtiva = $animal->lactacaoAtiva;

        $producaoHoje = $lactacaoAtiva
            ? (float) $lactacaoAtiva->ordenhas()->whereDate('data', Carbon::today())->sum('quantidade_litros')
            : 0.0;

        // Próximo parto em aberto (ainda não realizado/cancelado) — a lista
        // já vem ordenada por data_prevista desc, então pega o mais recente
        // dos que ainda estão em aberto.
        $proximoParto = $animal->partos->first(
            fn ($parto) => in_array($parto->status, AnimalRegras::STATUS_PARTO_CALCULADOS, true)
        );

        $motivoBloqueioParto = $this->motivoBloqueioReproducao($animal);
        $motivoBloqueioOrdenha = $this->motivoBloqueioOrdenha($animal, $lactacaoAtiva);

        $historico = $this->montarHistorico($animal);

        $cadastroLogado = Cadastro::find(session('usuario_id'));

        return view(
            'animaldetalhe',
            compact(
                'animal',
                'lactacaoAtiva',
                'producaoHoje',
                'proximoParto',
                'motivoBloqueioParto',
                'motivoBloqueioOrdenha',
                'historico',
                'cadastroLogado'
            ) + $this->opcoesAnimal()
        );
    }

    /** Timeline única (partos + ordenhas + início/fim de lactação), mais
     *  recente primeiro — mesmo espírito do pipeline de alertas do
     *  dashboard (collect()->merge()->sortBy()). */
    private function montarHistorico(Animal $animal)
    {
        $eventosPartos = $animal->partos->map(fn ($parto) => (object) [
            'data' => $parto->data_parto ?? $parto->data_prevista,
            'tipo' => 'parto',
            'titulo' => $parto->data_parto
                ? "Parto realizado ({$parto->tipo_parto})"
                : "Parto previsto — {$parto->status}",
            'descricao' => $parto->observacoes,
        ]);

        $eventosOrdenhas = $animal->ordenhas->map(fn ($ordenha) => (object) [
            'data' => $ordenha->data,
            'tipo' => 'ordenha',
            'titulo' => "Ordenha — {$ordenha->turno}",
            'descricao' => number_format($ordenha->quantidade_litros, 2, ',', '.') . ' L',
        ]);

        $eventosLactacao = $animal->lactacoes->flatMap(function ($lactacao) {
            $eventos = [(object) [
                'data' => $lactacao->data_inicio,
                'tipo' => 'lactacao',
                'titulo' => "Lactação nº {$lactacao->numero_lactacao} iniciada",
                'descricao' => null,
            ]];

            if ($lactacao->data_fim) {
                $eventos[] = (object) [
                    'data' => $lactacao->data_fim,
                    'tipo' => 'lactacao',
                    'titulo' => "Lactação nº {$lactacao->numero_lactacao} encerrada",
                    'descricao' => null,
                ];
            }

            return $eventos;
        });

        return collect()
            ->merge($eventosPartos)
            ->merge($eventosOrdenhas)
            ->merge($eventosLactacao)
            ->filter(fn ($evento) => $evento->data !== null)
            ->sortByDesc(fn ($evento) => $evento->data->format('Y-m-d'))
            ->values();
    }

    // ==========================================
    // GUARDAS DE REGRA DE NEGÓCIO
    // ==========================================

    /** Mensagem de bloqueio pra registrar parto (Realizado/Previsto), ou
     *  null se o animal pode ter parto registrado. */
    private function motivoBloqueioReproducao(Animal $animal): ?string
    {
        if ($animal->status !== 'Ativo') {
            return "Animal {$animal->status} — só é possível registrar parto em animais ativos.";
        }

        if ($animal->sexo !== 'Fêmea') {
            return 'Somente fêmeas podem ter partos registrados.';
        }

        return null;
    }

    private function bloqueioReproducao(Animal $animal): ?RedirectResponse
    {
        $motivo = $this->motivoBloqueioReproducao($animal);

        return $motivo
            ? back()->withErrors(['parto' => $motivo])
            : null;
    }

    /** Mensagem de bloqueio pra registrar ordenha, explicando o motivo
     *  exato — ou null se o animal pode ser ordenhado agora. */
    private function motivoBloqueioOrdenha(Animal $animal, ?Lactacao $lactacaoAtiva): ?string
    {
        if ($animal->status !== 'Ativo') {
            return "Animal {$animal->status} — não é possível registrar ordenha.";
        }

        if ($animal->sexo !== 'Fêmea') {
            return 'Somente fêmeas produzem leite.';
        }

        if (!$lactacaoAtiva) {
            return $animal->qtd_partos > 0
                ? 'Animal em período seco — sem lactação ativa. Registre um novo parto para liberar a ordenha.'
                : 'Animal ainda não teve nenhum parto/lactação registrada.';
        }

        return null;
    }

    private function bloqueioOrdenha(Animal $animal): ?RedirectResponse
    {
        $motivo = $this->motivoBloqueioOrdenha($animal, $animal->lactacaoAtiva);

        return $motivo
            ? back()->withErrors(['ordenha' => $motivo])
            : null;
    }

    // ==========================================
    // PARTO
    // ==========================================

    public function registrarParto(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->findOrFail($id);

        if ($erro = $this->bloqueioReproducao($animal)) {
            return $erro;
        }

        $dados = $request->validate([
            'data_prevista' => ['required', 'date'],
            'observacoes' => ['nullable', 'string', 'max:255'],
        ], [
            'data_prevista.required' => 'Informe a data prevista do parto.',
        ]);

        $animal->partos()->create([
            'data_prevista' => $dados['data_prevista'],
            'observacoes' => $dados['observacoes'] ?? null,
            'responsavel_id' => session('usuario_id'),
        ]);

        // Só promove de "Vazia" pra "Prenhe" — se já estiver "Em lactação"
        // ou "Período seco", o status muda de verdade só quando o parto for
        // efetivamente marcado como realizado (ver marcarPartoRealizado).
        if ($animal->status_reprodutivo === 'Vazia') {
            $animal->update(['status_reprodutivo' => 'Prenhe']);
        }

        return back()
            ->with('success', "Parto previsto registrado para \"{$animal->nome}\".");
    }

    public function marcarPartoRealizado(Request $request, $id, $partoId)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->findOrFail($id);
        $parto = $animal->partos()->findOrFail($partoId);

        if ($parto->data_parto !== null) {
            return back()
                ->withErrors(['parto' => 'Este parto já foi registrado como realizado.']);
        }

        $dados = $request->validate([
            'data_parto' => ['required', 'date', 'before_or_equal:today'],
            'tipo_parto' => ['required', Rule::in(AnimalRegras::TIPOS_PARTO)],
            // 0 é aceito (ex.: Aborto, sem bezerro vivo).
            'quantidade_bezerros' => ['required', 'integer', 'min:0', 'max:10'],
            'bezerros' => ['array'],
            'bezerros.*.sexo' => ['required_with:bezerros', Rule::in(AnimalRegras::SEXOS)],
            'bezerros.*.data_nascimento' => ['required_with:bezerros', 'date'],
            'bezerros.*.observacoes' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string', 'max:255'],
        ], [
            'data_parto.required' => 'Informe a data do parto.',
            'data_parto.before_or_equal' => 'A data do parto não pode ser no futuro.',
            'tipo_parto.required' => 'Selecione o tipo de parto.',
            'quantidade_bezerros.required' => 'Informe a quantidade de bezerros (0 em caso de aborto).',
        ]);

        // Não permite data real do parto anterior ao nascimento do animal.
        if (Carbon::parse($dados['data_parto'])->lt($animal->data_nascimento)) {
            return back()
                ->withErrors(['data_parto' => 'A data do parto não pode ser anterior à data de nascimento do animal.'])
                ->withInput();
        }

        $bezerros = $dados['bezerros'] ?? [];

        if (count($bezerros) !== (int) $dados['quantidade_bezerros']) {
            return back()
                ->withErrors(['quantidade_bezerros' => 'A quantidade de bezerros informada não bate com os dados preenchidos de cada bezerro.'])
                ->withInput();
        }

        DB::transaction(function () use ($animal, $parto, $dados, $bezerros) {
            $parto->update([
                'data_parto' => $dados['data_parto'],
                'tipo_parto' => $dados['tipo_parto'],
                'quantidade_bezerros' => $dados['quantidade_bezerros'],
                'status' => 'Realizado',
                'observacoes' => $dados['observacoes'] ?? $parto->observacoes,
            ]);

            foreach ($bezerros as $bezerro) {
                $parto->bezerros()->create([
                    'mae_id' => $animal->id,
                    'sexo' => $bezerro['sexo'],
                    'data_nascimento' => $bezerro['data_nascimento'],
                    'observacoes' => $bezerro['observacoes'] ?? null,
                ]);
            }

            // Número da lactação incrementado automaticamente por animal —
            // 1º parto = lactação 1, 2º parto = lactação 2, e assim por diante.
            $numero = $animal->lactacoes()->count() + 1;

            $animal->lactacoes()->create([
                'parto_id' => $parto->id,
                'numero_lactacao' => $numero,
                'data_inicio' => $dados['data_parto'],
                'status' => 'Ativa',
            ]);

            // Encerra o período seco (se houver) e libera a ordenha — a
            // liberação em si é implícita: bloqueioOrdenha só verifica se
            // existe uma lactação Ativa, sem precisar de uma flag separada.
            $animal->update([
                'status_reprodutivo' => 'Em lactação',
                'data_ultimo_parto' => $dados['data_parto'],
                'qtd_partos' => $animal->qtd_partos + 1,
            ]);
        });

        return back()
            ->with('success', "Parto de \"{$animal->nome}\" registrado como realizado! Uma nova lactação foi criada automaticamente e a ordenha já está liberada.");
    }

    public function cancelarParto(Request $request, $id, $partoId)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->findOrFail($id);
        $parto = $animal->partos()->findOrFail($partoId);

        if ($parto->data_parto !== null) {
            return back()
                ->withErrors(['parto' => 'Este parto já foi realizado — não é possível cancelar.']);
        }

        $dados = $request->validate([
            'motivo' => ['required', Rule::in(['Cancelado', 'Perdido'])],
            'observacoes' => ['nullable', 'string', 'max:255'],
        ], [
            'motivo.required' => 'Selecione o motivo (Cancelado ou Perdido).',
        ]);

        $parto->update([
            'status' => $dados['motivo'],
            'observacoes' => $dados['observacoes'] ?? $parto->observacoes,
        ]);

        if ($animal->status_reprodutivo === 'Prenhe') {
            $animal->update(['status_reprodutivo' => 'Vazia']);
        }

        return back()
            ->with('success', "Parto de \"{$animal->nome}\" marcado como {$dados['motivo']}.");
    }

    // ==========================================
    // ORDENHA
    // ==========================================

    public function registrarOrdenha(Request $request, $id)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->findOrFail($id);

        if ($erro = $this->bloqueioOrdenha($animal)) {
            return $erro;
        }

        $dados = $request->validate([
            'data' => ['required', 'date', 'before_or_equal:today'],
            'horario' => ['required', 'date_format:H:i'],
            'turno' => ['required', Rule::in(AnimalRegras::TURNOS_ORDENHA)],
            'quantidade_litros' => ['required', 'numeric', 'gt:0'],
            'observacoes' => ['nullable', 'string', 'max:255'],
        ], [
            'data.before_or_equal' => 'A data da ordenha não pode ser no futuro.',
            'horario.date_format' => 'Informe um horário válido (hh:mm).',
            'quantidade_litros.required' => 'Informe a quantidade de leite ordenhado.',
            'quantidade_litros.gt' => 'A quantidade de leite deve ser maior que zero.',
        ]);

        // Sem duplicidade: mesmo animal, mesma data, mesmo turno e mesmo
        // horário não pode ser registrado duas vezes.
        $duplicado = Ordenha::where('animal_id', $animal->id)
            ->whereDate('data', $dados['data'])
            ->where('turno', $dados['turno'])
            ->where('horario', $dados['horario'])
            ->exists();

        if ($duplicado) {
            return back()
                ->withErrors(['ordenha' => "Já existe uma ordenha registrada para \"{$animal->nome}\" nesta data, turno e horário."])
                ->withInput();
        }

        $lactacaoAtiva = $animal->lactacaoAtiva;

        DB::transaction(function () use ($animal, $lactacaoAtiva, $dados) {
            $animal->ordenhas()->create([
                'lactacao_id' => $lactacaoAtiva->id,
                'data' => $dados['data'],
                'horario' => $dados['horario'],
                'turno' => $dados['turno'],
                'quantidade_litros' => $dados['quantidade_litros'],
                'responsavel_id' => session('usuario_id'),
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            $this->recalcularLactacao($lactacaoAtiva);
        });

        $litrosFormatado = number_format((float) $dados['quantidade_litros'], 2, ',', '.');

        return back()
            ->with('success', "Ordenha registrada: {$litrosFormatado} L de \"{$animal->nome}\".");
    }

    /** Recalcula produção total, média diária, maior e menor produção da
     *  lactação a partir do histórico real de ordenhas — nunca confiar em
     *  incremento manual, sempre reconstruir dos dados de verdade. */
    private function recalcularLactacao(Lactacao $lactacao): void
    {
        $litrosPorDia = $lactacao->ordenhas()
            ->get()
            ->groupBy(fn ($ordenha) => $ordenha->data->format('Y-m-d'))
            ->map(fn ($grupo) => (float) $grupo->sum('quantidade_litros'));

        $producaoTotal = round($litrosPorDia->sum(), 2);

        // Média diária considera os dias corridos desde o início da
        // lactação (não só os dias com ordenha registrada) — é a métrica
        // de "produção média por dia em lactação" de verdade.
        $diasEmLactacao = max(1, $lactacao->data_inicio->diffInDays(Carbon::today()) + 1);

        $lactacao->update([
            'producao_total' => $producaoTotal,
            'media_diaria' => round($producaoTotal / $diasEmLactacao, 2),
            'maior_producao' => $litrosPorDia->isNotEmpty() ? round($litrosPorDia->max(), 2) : null,
            'menor_producao' => $litrosPorDia->isNotEmpty() ? round($litrosPorDia->min(), 2) : null,
        ]);
    }

    // ==========================================
    // LACTAÇÃO — encerrar (início do período seco)
    // ==========================================

    public function encerrarLactacao(Request $request, $id, $lactacaoId)
    {
        if ($redirect = $this->exigirLogin()) {
            return $redirect;
        }

        $animal = $this->animaisDoUsuario()->findOrFail($id);
        $lactacao = $animal->lactacoes()->findOrFail($lactacaoId);

        if ($lactacao->status !== 'Ativa') {
            return back()
                ->withErrors(['lactacao' => 'Esta lactação já está encerrada.']);
        }

        $dados = $request->validate([
            'data_fim' => [
                'nullable',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . $lactacao->data_inicio->format('Y-m-d'),
            ],
        ], [
            'data_fim.after_or_equal' => 'A data de término não pode ser anterior ao início da lactação.',
            'data_fim.before_or_equal' => 'A data de término não pode ser no futuro.',
        ]);

        DB::transaction(function () use ($animal, $lactacao, $dados) {
            $lactacao->update([
                'status' => 'Encerrada',
                'data_fim' => $dados['data_fim'] ?? now()->format('Y-m-d'),
            ]);

            // Ordenha passa a ficar bloqueada automaticamente: sem lactação
            // ativa, bloqueioOrdenha() reprova qualquer novo registro.
            $animal->update(['status_reprodutivo' => 'Período seco']);
        });

        return back()
            ->with('success', "Lactação nº {$lactacao->numero_lactacao} de \"{$animal->nome}\" encerrada. O animal entrou em período seco.");
    }
}
