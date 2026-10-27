<?php

namespace App\Http\Controllers;

use App\Models\Cadastro;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\LoteConsumoSnapshot;
use App\Models\Plano;
use App\Models\Racao;
use App\Models\RacaoEstoqueSnapshot;
use App\Services\EstoqueService;
use App\Support\CatalogoRacoes;
use App\Support\LoteRegras;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RacaoController extends Controller
{
    // Cada ração é cadastrada para um tipo de animal (mesma lista usada nos
    // filtros do Relatório) — lista fixa porque o campo é um <select>, não
    // texto livre, então não há risco de grafias divergentes no banco.
    private const TIPOS_ANIMAIS = ['Bovino', 'Ovino', 'Caprino', 'Suíno', 'Avicultura', 'Equino'];


    // Sexo, idade e finalidade da ração usam o mesmo vocabulário do
    // cadastro de Lote (LoteRegras) — evita ter listas divergentes pro
    // mesmo conceito. No formulário, esses três campos são preenchidos
    // automaticamente a partir de um lote escolhido (em vez de digitados
    // à mão), pra garantir que a ração sempre reflita um lote real.

    private function fazendaIdsDoUsuario()
    {
        return Fazenda::where('cadastro_id', session('usuario_id'))->pluck('id');
    }

    private function lotesDoUsuario()
    {
        return Lote::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    /** Rações não guardam lote_id — só uma "foto" do lote no momento do
     *  cadastro (fazenda_id, tipo, sexo, idade, finalidade) — então o
     *  vínculo com o usuário logado precisa passar pela fazenda mesmo. */
    private function racoesDoUsuario()
    {
        return Racao::whereIn('fazenda_id', $this->fazendaIdsDoUsuario());
    }

    // A autenticação em si (sessão válida) já é garantida pelo middleware
    // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).

    /** Cards "Tipos de ração" (App\Support\CatalogoRacoes) com os lotes
     *  compatíveis e quantas rações desse perfil o usuário já tem. */
    private function catalogo()
    {
        return CatalogoRacoes::comResumo(
            $this->lotesDoUsuario()->get(),
            $this->racoesDoUsuario()->get(),
        );
    }

    // LISTAR
    public function listar(Request $request)
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $lotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        // Cada ração pertence a uma fazenda e a um tipo de animal — os
        // filtros abaixo seguem o mesmo padrão do modal "Filtros Avançados"
        // da tela de Relatórios.
        $racaos = $this->racoesDoUsuario()
            ->with('fazenda')
            ->when($request->filled('fazenda'), function ($query) use ($request) {
                $query->where('fazenda_id', $request->fazenda);
            })
            ->when($request->filled('tipo_animal') && $request->tipo_animal !== 'todos', function ($query) use ($request) {
                $query->where('tipo', $request->tipo_animal);
            })
            ->get();

        $filtrosAplicados = $this->descreverFiltros($request, $fazendas);

        return view('cadastroracao', compact(
            'racaos',
            'fazendas',
            'lotes',
            'cadastroLogado',
            'filtrosAplicados'
        ) + [
            'tiposAnimais' => self::TIPOS_ANIMAIS,
            'sexos' => LoteRegras::SEXOS,
            'idadesMedias' => LoteRegras::IDADES_MEDIAS,
            'catalogo' => $this->catalogo(),
        ]);
    }

    /**
     * Traduz os filtros da URL em rótulos legíveis, para os "chips" de
     * filtro ativo — mesma ideia do RelatorioController::descreverFiltros.
     */
    private function descreverFiltros(Request $request, $fazendas): array
    {
        $itens = [];

        if ($request->filled('fazenda')) {
            $nome = optional($fazendas->firstWhere('id', (int) $request->fazenda))->nome_fazenda;
            $itens[] = ['chave' => 'fazenda', 'label' => 'Fazenda: ' . ($nome ?? '—')];
        }

        if ($request->filled('tipo_animal') && $request->tipo_animal !== 'todos') {
            $itens[] = ['chave' => 'tipo_animal', 'label' => 'Animal: ' . $request->tipo_animal];
        }

        return $itens;
    }

    // FORMULÁRIO CADASTRAR
    // O cadastro acontece via modal na própria tela de listagem.
    public function cadastrar()
    {
        $racaos = $this->racoesDoUsuario()->with('fazenda')->get();
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
        $lotes = $this->lotesDoUsuario()->with('fazenda')->orderBy('nome_lote')->get();
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        return view('cadastroracao', compact('racaos', 'fazendas', 'lotes', 'cadastroLogado')
            + [
                'tiposAnimais' => self::TIPOS_ANIMAIS,
                'sexos' => LoteRegras::SEXOS,
                'idadesMedias' => LoteRegras::IDADES_MEDIAS,
                'catalogo' => $this->catalogo(),
            ]);
    }

    /** Campos opcionais da ração (o formulário web não os tem; a API sim). */
    private const REGRAS_OPCIONAIS = [
        'nutrientes' => 'nullable|string|max:2000',
        'unidade' => 'nullable|string|in:kg,saco,tonelada,litro',
        'proteina_percentual' => 'nullable|numeric|min:0|max:100',
        'energia_mcal_kg' => 'nullable|numeric|min:0|max:20',
        'fibra_percentual' => 'nullable|numeric|min:0|max:100',
        'minerais' => 'nullable|string|max:255',
        'estoque_minimo' => 'nullable|numeric|min:0',
        'status' => 'nullable|in:ativa,inativa',
        'observacoes' => 'nullable|string|max:2000',
        'fornecedor' => 'nullable|string|max:150',
    ];

    /** Campos opcionais gravados como vierem (só quando enviados — ver
     *  camposOpcionais()). */
    private const CAMPOS_OPCIONAIS = [
        'nutrientes',
        'proteina_percentual',
        'energia_mcal_kg',
        'fibra_percentual',
        'minerais',
        'estoque_minimo',
        'status',
        'observacoes',
        'fornecedor',
    ];

    private const MENSAGENS = [
        'lote_id.exists' => 'Lote inválido — selecione um dos seus lotes.',
        'tipo.required_without' => 'Selecione o tipo de animal da ração.',
        'tipo.in' => 'Tipo de animal inválido.',
        'sexo.in' => 'Sexo inválido.',
        'idade.in' => 'Faixa etária inválida.',
        'finalidade.in' => 'Finalidade inválida.',
        'nome_racao.required' => 'Informe o nome da ração.',
        'estoque.min' => 'O estoque não pode ser negativo.',
        'estoque_total.min' => 'A capacidade total precisa ser maior que zero.',
        'estoque_total.gte' => 'A capacidade total não pode ser menor que o estoque atual.',
        'consumo.min' => 'O consumo não pode ser negativo.',
        'valor_kg.min' => 'O preço não pode ser negativo.',
        'valor_kg.gt' => 'Informe um preço por kg maior que zero.',
        'fornecedor.max' => 'Nome do fornecedor muito longo (máx. 150 caracteres).',
        'unidade.in' => 'Unidade inválida — use kg, saco, tonelada ou litro.',
        'proteina_percentual.max' => 'A proteína deve ficar entre 0 e 100%.',
        'fibra_percentual.max' => 'A fibra deve ficar entre 0 e 100%.',
        'energia_mcal_kg.max' => 'Energia inválida (use Mcal/kg, até 20).',
        'estoque_minimo.min' => 'O estoque mínimo não pode ser negativo.',
        'status.in' => 'Status inválido — use ativa ou inativa.',
    ];

    /**
     * Regras comuns de cadastro/edição. A ração pode vir de dois jeitos:
     *  - com "lote_id" (formulário web, Plano Alimentar): tipo, sexo,
     *    idade e finalidade são copiados do lote (como sempre foi);
     *  - sem lote (tela "Tipos de Rações" do app): esses campos vêm do
     *    próprio formulário, validados contra o mesmo vocabulário do Lote.
     */
    private function regrasRacao(): array
    {
        return [
            'fazenda_id' => ['required', Rule::exists('fazendas', 'id')->whereIn('id', $this->fazendaIdsDoUsuario())],
            'nome_racao' => 'required|string|max:255',
            'lote_id' => ['nullable', Rule::exists('lotes', 'id')->whereIn('fazenda_id', $this->fazendaIdsDoUsuario())],
            'tipo' => ['required_without:lote_id', 'nullable', Rule::in(self::TIPOS_ANIMAIS)],
            'sexo' => ['nullable', Rule::in(LoteRegras::SEXOS)],
            'idade' => ['nullable', Rule::in(LoteRegras::IDADES_MEDIAS)],
            'finalidade' => ['nullable', Rule::in(LoteRegras::todasFinalidades())],
            'estoque' => 'required|numeric|min:0',
            'estoque_total' => 'required|numeric|min:1|gte:estoque',
            'consumo' => 'required|numeric|min:0',
            // Preço por kg: maior que zero (custo e relatórios dependem dele).
            'valor_kg' => 'required|numeric|gt:0|max:100000',
        ] + self::REGRAS_OPCIONAIS;
    }

    /** Tipo/sexo/idade/finalidade: do lote escolhido, ou do formulário. */
    private function perfilAnimal(Request $request, ?Lote $lote): array
    {
        if ($lote) {
            return [
                'tipo' => $lote->tipo_animal,
                'sexo' => $lote->sexo,
                'idade' => $lote->idade_media,
                'finalidade' => $lote->finalidade,
            ];
        }

        return [
            'tipo' => $request->input('tipo'),
            'sexo' => $request->input('sexo') ?: null,
            'idade' => $request->input('idade') ?: null,
            'finalidade' => $request->input('finalidade') ?: null,
        ];
    }

    /** Só os campos opcionais que vieram no request — quem não os envia
     *  (formulário web, ajuste de consumo do Plano Alimentar) não apaga o
     *  que já estava gravado. */
    private function camposOpcionais(Request $request): array
    {
        $dados = [];
        foreach (self::CAMPOS_OPCIONAIS as $campo) {
            if ($request->has($campo)) {
                $valor = $request->input($campo);
                $dados[$campo] = $valor === '' ? null : $valor;
            }
        }
        if (array_key_exists('status', $dados) && $dados['status'] === null) {
            $dados['status'] = 'ativa';
        }

        return $dados;
    }

    /**
     * Sem lote, o perfil vem do formulário (ou de um card do catálogo): a
     * finalidade precisa existir para a espécie e não pode ser só de fêmea
     * (leite/ovos) numa ração para machos — a mesma regra dos lotes. Assim
     * nenhuma ração "impossível" entra no banco, nem enviada à mão.
     */
    private function validarPerfilSemLote(Request $request): void
    {
        if ($request->filled('lote_id') || !$request->filled('finalidade')) {
            return;
        }

        $permitidas = LoteRegras::finalidadesPermitidas($request->input('tipo'), $request->input('sexo') ?: null);
        if (!in_array($request->input('finalidade'), $permitidas, true)) {
            throw ValidationException::withMessages([
                'finalidade' => "A finalidade \"{$request->input('finalidade')}\" não existe para {$request->input('tipo')}"
                    . ($request->input('sexo') ? " ({$request->input('sexo')})" : '') . '.',
            ]);
        }
    }

    // SALVAR
    public function add(Request $request, EstoqueService $estoque)
    {
        $request->validate($this->regrasRacao(), self::MENSAGENS);
        $this->validarPerfilSemLote($request);

        // Com lote: tipo/sexo/idade/finalidade são lidos direto do lote
        // escolhido (nunca de campos que poderiam ter sido adulterados no
        // navegador). Sem lote: vêm do formulário, já validados acima.
        $lote = $request->filled('lote_id')
            ? $this->lotesDoUsuario()->findOrFail($request->lote_id)
            : null;

        DB::transaction(function () use ($request, $lote, $estoque) {
            // Nasce com estoque 0 e o saldo informado entra como a primeira
            // movimentação — assim o histórico do estoque começa completo.
            $racao = Racao::create([
                'fazenda_id' => $lote?->fazenda_id ?? $request->fazenda_id,
                'nome_racao' => $request->nome_racao,
                ...$this->perfilAnimal($request, $lote),
                'unidade' => $request->input('unidade', 'kg'),
                'estoque' => 0,
                'estoque_total' => $request->estoque_total,
                'consumo' => $request->consumo,
                'valor_kg' => $request->valor_kg,
                'status' => 'ativa',
                ...$this->camposOpcionais($request),
            ]);

            if ((float) $request->estoque > 0) {
                $estoque->entrada($racao, (float) $request->estoque, [
                    'origem' => 'cadastro_racao',
                    'observacao' => 'Estoque inicial no cadastro da ração.',
                ]);
            }

            // O usuário já escolheu o lote no próprio formulário de cadastro —
            // pedir pra vincular de novo na tela de Plano Alimentar seria
            // repetir a mesma escolha. Vira automaticamente a ração vinculada
            // deste lote (substituindo a anterior, se houver — o cadastro de
            // uma ração nova pra este lote é a intenção mais recente do
            // usuário sobre o que ele deve comer). Sem lote (cadastro pela
            // tela "Tipos de Rações" do app), o vínculo é feito depois no
            // Plano Alimentar, que só oferece rações compatíveis. Vincular
            // = plano ativo do lote (kg por animal), como no Plano Alimentar.
            if ($lote) {
                Plano::ativarParaLote($lote, $racao);
            }
        });

        return redirect()->route('racao.listar')
            ->with('success', 'Ração cadastrada com sucesso!');
    }

    // FORMULÁRIO EDITAR
    // A edição acontece via modal na própria tela de listagem.
    public function atualizar($id)
    {
        $this->racoesDoUsuario()->findOrFail($id);

        return redirect()->route('racao.listar');
    }

    // ATUALIZAR
    public function update(Request $request, EstoqueService $estoque, $id)
    {
        $racao = $this->racoesDoUsuario()->findOrFail($id);

        // Edição sem lote e sem tipo (ex.: um cliente antigo): mantém o
        // perfil de animal já gravado, em vez de exigir o tipo de novo.
        if (!$request->filled('lote_id') && !$request->filled('tipo')) {
            $request->merge([
                'tipo' => $racao->tipo,
                'sexo' => $request->input('sexo', $racao->sexo),
                'idade' => $request->input('idade', $racao->idade),
                'finalidade' => $request->input('finalidade', $racao->finalidade),
            ]);
        }

        $request->validate($this->regrasRacao(), self::MENSAGENS);
        $this->validarPerfilSemLote($request);

        $lote = $request->filled('lote_id')
            ? $this->lotesDoUsuario()->findOrFail($request->lote_id)
            : null;

        // Mudar a ração de fazenda deixaria o histórico de estoque e os lotes
        // que a usam na fazenda antiga. Só uma ração sem uso pode mudar.
        $novaFazenda = (int) ($lote?->fazenda_id ?? $request->fazenda_id);
        if ($novaFazenda !== (int) $racao->fazenda_id
            && ($racao->lotes()->exists() || $racao->planos()->exists() || $racao->movimentacoes()->where('origem', '!=', 'cadastro_racao')->exists())) {
            return back()->withErrors([
                'fazenda_id' => "A ração \"{$racao->nome_racao}\" já tem lotes, planos ou movimentações nesta fazenda — não é possível mudá-la de fazenda. Cadastre a ração na outra fazenda.",
            ])->withInput();
        }

        DB::transaction(function () use ($request, $lote, $racao, $estoque) {
            $racao->update([
                'fazenda_id' => $lote?->fazenda_id ?? $request->fazenda_id,
                'nome_racao' => $request->nome_racao,
                ...$this->perfilAnimal($request, $lote),
                'unidade' => $request->input('unidade', $racao->unidade ?? 'kg'),
                'estoque_total' => $request->estoque_total,
                'consumo' => $request->consumo,
                'valor_kg' => $request->valor_kg,
                // Campos opcionais: só mudam quando vierem no request (o
                // formulário web e o Plano Alimentar não os enviam).
                ...$this->camposOpcionais($request),
            ]);

            // Mudança manual do estoque vira um "ajuste" no histórico (com
            // saldo anterior/posterior), em vez de sobrescrever em silêncio.
            $estoque->ajustarPara($racao, (float) $request->estoque, [
                'origem' => 'edicao_racao',
                'observacao' => 'Estoque alterado na edição da ração.',
            ]);
        });

        // Editada pelo modal da tela de Estoque: volta pra ela (com os
        // filtros que estavam aplicados), não pra tela de Rações.
        $destino = $request->input('origem') === 'estoque'
            ? redirect()->to(url()->previous(route('estoque.listar')))
            : redirect()->route('racao.listar');

        return $destino->with('success', 'Ração atualizada com sucesso!');
    }

    // EXCLUIR
    public function deletar($id)
    {
        $racao = $this->racoesDoUsuario()->findOrFail($id);

        // Excluir apaga junto (ON DELETE CASCADE) o histórico de estoque e os
        // planos alimentares da ração — sem aviso nenhum, antes. Ração que já
        // foi usada (lote vinculado, plano, abastecimento, saída ou consumo
        // registrado) não é excluída: ela pode ser INATIVADA, que a tira das
        // opções sem perder o histórico. Ração cadastrada por engano (só a
        // entrada inicial do cadastro) continua podendo ser excluída.
        $motivo = match (true) {
            $racao->lotes()->exists() => 'ela está vinculada a um lote',
            $racao->planos()->exists() => 'ela já fez parte de um plano alimentar',
            $racao->movimentacoes()->where('origem', '!=', 'cadastro_racao')->exists() => 'ela já tem movimentações de estoque (entradas, saídas ou abastecimentos)',
            LoteConsumoSnapshot::where('racao_id', $racao->id)->where('consumo_kg', '>', 0)->exists() => 'ela já tem consumo registrado',
            default => null,
        };

        if ($motivo) {
            return redirect()->route('racao.listar')->withErrors([
                'racao' => "Não é possível excluir \"{$racao->nome_racao}\" porque {$motivo} — excluir apagaria esse histórico. "
                    . 'Para ela não aparecer mais nas opções, edite a ração e mude o status para "Inativa".',
            ]);
        }

        $racao->delete();

        return redirect()->route('racao.listar')
            ->with('success', 'Ração excluída com sucesso!');
    }
}