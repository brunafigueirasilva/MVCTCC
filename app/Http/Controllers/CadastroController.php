<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Http\Controllers\Concerns\ResolveFazendaSelecionada;
use App\Http\Requests\AtualizarPerfilRequest;
use App\Models\Cadastro;
use App\Models\CodigoVerificacao;
use App\Models\Racao;
use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Cocho;
use App\Models\CochoConsumo;
use App\Models\Sensor;
use App\Models\LoteConsumoSnapshot;
use App\Models\Ordenha;
use App\Support\LoteRegras;
use App\Notifications\CodigoVerificacaoNotification;
use App\Notifications\PerfilAtualizadoNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CadastroController extends Controller
{
    use ResolveFazendaSelecionada;

    // Política de senha do sistema: mínimo 8 caracteres, com pelo menos uma
    // letra maiúscula, uma minúscula, um número e o caractere @.
    public const SENHA_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*@).{8,}$/';
    public const SENHA_MENSAGEM = 'A senha deve ter no mínimo 8 caracteres e incluir letra maiúscula, letra minúscula, número e o caractere @.';

    /** Cadastro de conta (web e API): sem pasta lang/pt_BR, as mensagens
     *  padrão dessas regras saíam em inglês ("The email has already been taken."). */
    public const MENSAGENS_CADASTRO = [
        'nome.required' => 'Informe o seu nome.',
        'email.required' => 'Informe o e-mail.',
        'email.email' => 'Informe um e-mail válido.',
        'email.unique' => 'Este e-mail já está cadastrado. Faça login ou use "Esqueci minha senha".',
        'password.required' => 'Informe uma senha.',
        'password.confirmed' => 'A confirmação de senha não corresponde.',
        'telefone.required' => 'Informe o telefone.',
    ];

    // TELA LOGIN
    public function login()
    {
        return view('login');
    }

    // DASHBOARD
    public function dashboard(Request $request, PlanoController $planoController)
    {
        // A autenticação em si (sessão válida) já é garantida pelo middleware
        // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).
        // Garante que o snapshot de HOJE (lote_consumo_snapshots, usado nos
        // cards "Consumo Hoje"/"Custo Hoje" e no gráfico "Histórico de
        // Consumo" abaixo) reflita o vínculo de ração ATUAL mesmo que
        // ninguém tenha aberto o Plano Alimentar ainda hoje — mesma rotina
        // que aquela tela já roda a cada visita (ver PlanoController::
        // capturarSnapshotsDeHoje), só que aqui cobre todas as fazendas do
        // usuário logado de uma vez, já que o Dashboard não fica preso a
        // uma só.
        $planoController->capturarSnapshotsDoUsuario();

        // Fazendas são exclusivas de cada usuário: o painel só pode listar e
        // aceitar como selecionada uma fazenda que pertença à conta logada,
        // senão bastaria trocar o "fazenda_id" na URL para ver os lotes,
        // cochos e alertas de outra pessoa.
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))
            ->orderBy('nome_fazenda')
            ->get();

        // Fazenda escolhida na barra "Unidade Produtiva", com persistência
        // em sessão: quando definida, todo o painel (lotes, cochos, animais
        // e histórico de consumo) passa a refletir somente essa fazenda — e
        // a escolha continua valendo ao navegar pra Ordenha/Leite, Plano
        // Alimentar e Relatório, sem precisar reselecionar. Sem fazenda
        // selecionada, todo cartão/gráfico/indicador é exibido zerado.
        $fazendaSelecionadaId = $this->resolverFazendaSelecionadaComSessao($request, $fazendas);

        // O modal "Editar Perfil" (acessível pelo avatar na navbar) precisa
        // dos dados do usuário logado para pré-preencher o formulário; sem
        // isso, os campos chegavam sempre vazios e qualquer alteração
        // enviada por ali sobrescrevia os dados reais com valores em branco.
        $cadastroLogado = Cadastro::find(session('usuario_id'));

        if (!$fazendaSelecionadaId) {
            return view('dashboard', $this->dadosDashboardVazio($fazendas) + ['cadastroLogado' => $cadastroLogado]);
        }

        return view('dashboard', $this->dadosDashboardDaFazenda($fazendas, $fazendaSelecionadaId) + ['cadastroLogado' => $cadastroLogado]);
    }

    /**
     * Resumo de alertas para o sino de notificações do layout global —
     * mesmos alertas reais do Dashboard, mas expostos em qualquer página
     * (sem recalcular os cartões/gráficos pesados, só o necessário aqui).
     */
    public function alertasResumo(Request $request)
    {
        $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))
            ->orderBy('nome_fazenda')
            ->get();

        // Mesma fazenda selecionada (e salva em sessão) do Dashboard/Ordenha
        // e Leite/Plano Alimentar/Relatório — assim que uma é escolhida em
        // qualquer uma dessas telas, o sino de notificações já passa a
        // mostrar os alertas dela em qualquer página, sem esperar o usuário
        // voltar pro Dashboard.
        $fazendaSelecionadaId = $this->resolverFazendaSelecionadaComSessao($request, $fazendas);

        if (!$fazendaSelecionadaId) {
            return response()->json(['count' => 0, 'alertas' => []]);
        }

        $alertas = $this->alertasDaFazenda($fazendaSelecionadaId);

        return response()->json([
            'count' => $alertas->count(),
            'alertas' => $alertas->map(fn ($alerta) => [
                'titulo' => $alerta->titulo,
                'descricao' => $alerta->descricao,
                'prioridade' => $alerta->prioridade,
                // estoque | cocho | lote | ordenha | consumo — o app usa para
                // separar alertas de atenção de lembretes de rotina.
                'categoria' => $alerta->categoria ?? null,
            ])->values(),
        ]);
    }

    /**
     * Alertas reais da fazenda informada — mesmas condições/regras usadas
     * em dadosDashboardDaFazenda(), extraídas aqui para serem reaproveitadas
     * por alertasResumo() sem duplicar os cartões/gráficos do Dashboard.
     */
    private function alertasDaFazenda($fazendaSelecionadaId)
    {
        $lotes = Lote::where('fazenda_id', $fazendaSelecionadaId)->get();
        $loteIds = $lotes->pluck('id');

        $cochos = Cocho::whereIn('lote_id', $loteIds)->with('sensores')->get();
        $racoesFazenda = Racao::where('fazenda_id', $fazendaSelecionadaId)->get();

        $hoje = Carbon::today();
        // Filtra o dia no banco: antes trazia TODO o histórico de snapshots
        // da fazenda e descartava em PHP — e esta consulta roda a cada
        // página aberta (sino de notificações).
        $snapshotsHoje = LoteConsumoSnapshot::whereIn('lote_id', $loteIds)
            ->whereDate('data', $hoje)
            ->get();

        $ordenhas7Dias = Ordenha::whereIn('lote_id', $loteIds)
            ->whereDate('data', '>=', Carbon::today()->subDays(6))
            ->get();

        $lotesElegiveisOrdenha = $lotes->filter(fn ($lote) => $lote->status === 'Ativo'
            && LoteRegras::tipoTemOrdenha($lote->tipo_animal)
            && in_array($lote->sexo, LoteRegras::SEXOS_COM_FEMEA, true));
        $loteIdsComHistoricoOrdenha = Ordenha::whereIn('lote_id', $loteIds)->distinct()->pluck('lote_id');

        return collect()
            ->merge($this->alertasEstoqueRacao($racoesFazenda))
            ->merge($this->alertasCochos($cochos))
            ->merge($this->alertasLotesSemCocho($lotes, $cochos))
            ->merge($this->alertasAlimentacaoHoje($lotes, $snapshotsHoje))
            ->merge($this->alertasLotesSemOrdenhaHoje($lotesElegiveisOrdenha, $loteIdsComHistoricoOrdenha, $ordenhas7Dias, $hoje))
            ->sortBy(fn ($alerta) => $alerta->prioridade === 'critico' ? 0 : 1)
            ->values();
    }

    /**
     * Estrutura completa do dashboard sem nenhuma fazenda selecionada:
     * todos os cartões, gráficos e indicadores zerados.
     */
    private function dadosDashboardVazio($fazendas): array
    {
        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => null,
            'lotesCount' => 0,
            'totalAnimais' => 0,
            'lotesNovosSemana' => 0,
            'cochosCount' => 0,
            'nivelMedioCochos' => 0,
            'racaoNosCochos' => 0.0,
            'consumoHoje' => 0.0,
            'consumoRegistradoHoje' => 0.0,
            'variacaoConsumo' => null,
            'custoHoje' => 0.0,
            'precoMedioKg' => 0.0,
            'eficienciaAlimentar' => 0.0,
            'lotesComRacaoHoje' => 0,
            'lotesComRegistroHoje' => 0,
            'estoqueAtual' => 0.0,
            'estoqueMinimo' => 0.0,
            'diasRestantesEstoque' => null,
            'labelsConsumo' => $this->labelsUltimos7Dias(),
            'dadosConsumo' => array_fill(0, 7, 0.0),
            'labelsAnimais' => [],
            'dadosAnimais' => [],
            'alertas' => collect(),

            // Ordenha e produção de leite (por LOTE) — os cartões/gráficos
            // completos vivem em Ordenha e Leite (OrdenhaLeiteController);
            // aqui só sobra o resumo do card "Ordenha e Leite" que linka
            // pra lá.
            'lotesComOrdenha' => 0,
            'producaoLeiteHoje' => 0.0,
        ];
    }

    /**
     * Estrutura completa do dashboard com todos os cartões, gráficos e
     * indicadores calculados a partir dos dados reais da fazenda escolhida.
     */
    private function dadosDashboardDaFazenda($fazendas, $fazendaSelecionadaId): array
    {
        $lotes = Lote::where('fazenda_id', $fazendaSelecionadaId)->get();
        $loteIds = $lotes->pluck('id');

        $cochos = Cocho::whereIn('lote_id', $loteIds)->with('sensores')->get();

        // Todos os cards/gráficos do painel olham só pros lotes ATIVOS —
        // mesma regra da Gestão de Lotes (status === 'Ativo'). Antes
        // contavam também lotes Inativos/Vendidos (e os cochos, animais e
        // consumo deles), inflando os números em relação às telas de gestão.
        $lotesAtivos = $lotes->where('status', 'Ativo')->values();
        $loteIdsAtivos = $lotesAtivos->pluck('id');
        $cochosAtivos = $cochos->whereIn('lote_id', $loteIdsAtivos);

        $nivelMedioCochos = $cochosAtivos->isNotEmpty()
            ? (int) round($cochosAtivos->avg(fn ($c) => $c->capacidade > 0 ? min(100, ($c->nivel_atual / $c->capacidade) * 100) : 0))
            : 0;

        $seteDiasAtras = Carbon::today()->subDays(7);
        $lotesNovosSemana = $lotesAtivos->filter(fn ($l) => $l->created_at && $l->created_at->greaterThanOrEqualTo($seteDiasAtras))->count();

        // Consumo real: lote_consumo_snapshots, gravado automaticamente pelo
        // Plano Alimentar (PlanoController::capturarSnapshotsDeHoje / o
        // comando agendado plano:capturar-snapshots) — mesma fonte já usada
        // em Relatórios e no próprio Plano Alimentar. Este card lia antes da
        // tabela "planos", alimentada por um "registrar alimentação" manual
        // que não existe mais em lugar nenhum do sistema: a tabela fica
        // parada em zero registros pra sempre, então "Consumo Hoje", "Custo
        // Hoje", "Eficiência Alimentar" e o gráfico "Histórico de Consumo"
        // apareciam sempre zerados/vazios, mesmo com lotes consumindo ração
        // de verdade pelo vínculo automático.
        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();

        $snapshots = LoteConsumoSnapshot::whereIn('lote_id', $loteIdsAtivos)
            ->whereDate('data', '>=', $hoje->copy()->subDays(6))
            ->get();

        $snapshotsHoje = $snapshots->filter(fn ($s) => $s->data->isSameDay($hoje));
        $consumoHoje = (float) $snapshotsHoje->sum('consumo_kg');
        $consumoOntem = (float) $snapshots->filter(fn ($s) => $s->data->isSameDay($ontem))->sum('consumo_kg');

        $variacaoConsumo = $this->variacaoPercentual($consumoHoje, $consumoOntem);

        // Consumo REAL informado hoje nos cochos dos lotes ativos ("Registrar
        // consumo" / ajuste de nível) — o consumoHoje acima é o PREVISTO
        // pelo plano. Os dois aparecem lado a lado, nunca somados.
        $consumoRegistradoHoje = round((float) CochoConsumo::whereIn('lote_id', $loteIdsAtivos)
            ->whereDate('data_consumo', $hoje)
            ->sum('quantidade'), 2);

        // Rações cadastradas para esta fazenda (estoque, consumo e preço).
        $racoesFazenda = Racao::where('fazenda_id', $fazendaSelecionadaId)->get();

        // Custo real: cada kg consumido hoje vale o "valor_kg" cadastrado da
        // ração que o lote comeu — antes era um preço fixo de R$ 3,50 que
        // não tinha nada a ver com as rações da fazenda.
        $custoHoje = round($snapshotsHoje->sum(
            fn ($s) => (float) $s->consumo_kg * $this->valorKgDoSnapshot($s, $lotesAtivos, $racoesFazenda)
        ), 2);
        $precoMedioKg = $consumoHoje > 0
            ? round($custoHoje / $consumoHoje, 2)
            : round((float) $racoesFazenda->avg('valor_kg'), 2);

        // Histórico dos últimos 7 dias.
        $labelsConsumo = $this->labelsUltimos7Dias();
        $dadosConsumo = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $dadosConsumo[] = (float) $snapshots
                ->filter(fn ($s) => $s->data->isSameDay($dia))
                ->sum('consumo_kg');
        }

        // Eficiência alimentar de hoje: % dos lotes com snapshot "saudável"
        // (status_class = status-success, ou seja, com ração vinculada —
        // ver PlanoController::statusRacaoParaLote()) entre os que já têm
        // algum registro hoje. Mesma definição usada no card "Últimos 7
        // Dias" do Plano Alimentar, pra nunca haver dois critérios
        // diferentes de "eficiência" no sistema.
        $comRegistroHoje = $snapshotsHoje->count();
        $saudaveisHoje = $snapshotsHoje->where('status_class', 'status-success')->count();
        $eficienciaAlimentar = $comRegistroHoje > 0
            ? round(($saudaveisHoje / $comRegistroHoje) * 100, 1)
            : 0.0;

        // Autonomia do estoque: estoque total das rações da fazenda dividido
        // pelo consumo REAL de hoje dos lotes ativos. Antes dividia pela
        // soma do "consumo" cadastrado de TODAS as rações — inclusive as que
        // nenhum lote está comendo —, o que subestimava muito a autonomia.
        // Sem consumo hoje (nenhum lote ativo comendo) não há o que estimar.
        $estoqueAtual = (float) $racoesFazenda->sum('estoque');
        $estoqueMinimo = round($consumoHoje * Racao::DIAS_SEGURANCA_ESTOQUE, 2);
        $diasRestantesEstoque = $consumoHoje > 0
            ? (int) floor($estoqueAtual / $consumoHoje)
            : null;

        $animaisPorTipo = $lotesAtivos
            ->groupBy(fn ($l) => $l->tipo_animal ?: 'Não informado')
            ->map(fn ($grupo) => (int) $grupo->sum('quantidade_animais'))
            ->filter(fn ($total) => $total > 0)
            ->sortDesc();

        $labelsAnimais = $animaisPorTipo->keys()->values()->all();
        $dadosAnimais = $animaisPorTipo->values()->all();

        // ==========================================
        // ORDENHA E LEITE — produção controlada exclusivamente por LOTE.
        // Os cartões/gráficos completos (produção diária, evolução mensal,
        // produção por lote) vivem na tela própria "Ordenha e Leite"
        // (OrdenhaLeiteController) — aqui só sobra o mínimo pro card-resumo
        // do Dashboard e pros alertas abaixo.
        // ==========================================
        $producaoLeiteHoje = (float) Ordenha::whereIn('lote_id', $loteIds)
            ->whereDate('data', $hoje)
            ->sum('quantidade_litros');

        // Ordenhas dos últimos 7 dias — só para o card-resumo e o alerta de
        // "lote sem ordenha hoje" abaixo (o histórico completo vive em
        // Ordenha e Leite).
        $ordenhas7Dias = Ordenha::whereIn('lote_id', $loteIds)
            ->whereDate('data', '>=', Carbon::today()->subDays(6))
            ->get();

        $lotesComOrdenha = $ordenhas7Dias->pluck('lote_id')->unique()->count();

        // Só lotes que já tiveram alguma ordenha registrada alguma vez
        // entram no alerta de "sem ordenha hoje" — evita alertar um lote
        // elegível mas recém-criado, que nunca começou a ser ordenhado.
        $lotesElegiveisOrdenha = $lotes->filter(fn ($lote) => $lote->status === 'Ativo'
            && LoteRegras::tipoTemOrdenha($lote->tipo_animal)
            && in_array($lote->sexo, LoteRegras::SEXOS_COM_FEMEA, true));
        $loteIdsComHistoricoOrdenha = Ordenha::whereIn('lote_id', $loteIds)->distinct()->pluck('lote_id');

        // Todo alerta possível vem de uma condição real dos dados (nunca
        // inventado): estoque de ração, nível dos cochos, lotes sem cocho
        // vinculado, lotes sem alimentação registrada hoje e lotes em
        // produção de leite sem ordenha registrada hoje.
        $alertas = collect()
            ->merge($this->alertasEstoqueRacao($racoesFazenda))
            ->merge($this->alertasCochos($cochos))
            ->merge($this->alertasLotesSemCocho($lotes, $cochos))
            ->merge($this->alertasAlimentacaoHoje($lotes, $snapshotsHoje))
            ->merge($this->alertasLotesSemOrdenhaHoje($lotesElegiveisOrdenha, $loteIdsComHistoricoOrdenha, $ordenhas7Dias, $hoje))
            ->sortBy(fn ($alerta) => $alerta->prioridade === 'critico' ? 0 : 1)
            ->values();

        return [
            'fazendas' => $fazendas,
            'fazendaSelecionadaId' => $fazendaSelecionadaId,
            'lotesCount' => $lotesAtivos->count(),
            'totalAnimais' => (int) $lotesAtivos->sum('quantidade_animais'),
            'lotesNovosSemana' => $lotesNovosSemana,
            'cochosCount' => $cochosAtivos->count(),
            'nivelMedioCochos' => $nivelMedioCochos,
            // Ração já colocada nos cochos (saiu do armazém): soma do nível
            // atual dos cochos dos lotes ativos — separada do estoque.
            'racaoNosCochos' => round((float) $cochosAtivos->sum('nivel_atual'), 2),
            'consumoHoje' => $consumoHoje,
            'consumoRegistradoHoje' => $consumoRegistradoHoje,
            'variacaoConsumo' => $variacaoConsumo,
            'custoHoje' => $custoHoje,
            'precoMedioKg' => $precoMedioKg,
            'eficienciaAlimentar' => $eficienciaAlimentar,
            'lotesComRacaoHoje' => $saudaveisHoje,
            'lotesComRegistroHoje' => $comRegistroHoje,
            'estoqueAtual' => $estoqueAtual,
            'estoqueMinimo' => $estoqueMinimo,
            'diasRestantesEstoque' => $diasRestantesEstoque,
            'labelsConsumo' => $labelsConsumo,
            'dadosConsumo' => $dadosConsumo,
            'labelsAnimais' => $labelsAnimais,
            'dadosAnimais' => $dadosAnimais,
            'alertas' => $alertas,

            // Só o necessário pro card-resumo "Ordenha e Leite" — o resto
            // (gráficos, comparativos, histórico) mora em Ordenha e Leite.
            'lotesComOrdenha' => $lotesComOrdenha,
            'producaoLeiteHoje' => $producaoLeiteHoje,
        ];
    }

    /**
     * Estoque de ração: zerado (crítico, independente do consumo médio) ou
     * baixo o suficiente para acabar dentro da margem de segurança.
     */
    private function alertasEstoqueRacao($racoesFazenda)
    {
        return $racoesFazenda->flatMap(function ($racao) {
            $status = $racao->statusEstoque();

            if ($status === 'esgotado') {
                return [(object) [
                    'prioridade' => 'critico',
                    'categoria' => 'estoque',
                    'titulo' => "Estoque zerado de {$racao->nome_racao}",
                    'descricao' => 'Não há mais estoque registrado desta ração. Reponha o quanto antes para não interromper a alimentação.',
                ]];
            }

            if ($status === 'baixo') {
                $diasRestantes = $racao->diasRestantesEstoque();

                return [(object) [
                    'prioridade' => $diasRestantes <= 3 ? 'critico' : 'baixo',
                    'categoria' => 'estoque',
                    'titulo' => "Estoque baixo de {$racao->nome_racao}",
                    'descricao' => "Restam aproximadamente {$diasRestantes} dia(s) de estoque no ritmo de consumo atual.",
                ]];
            }

            return [];
        })->values();
    }

    /** Cochos vazios (crítico) ou com nível abaixo de 20% da capacidade. */
    private function alertasCochos($cochos)
    {
        return $this->alertasSensoresCochos($cochos)->merge($cochos->flatMap(function ($cocho) {
            if ($cocho->capacidade <= 0) {
                return [];
            }

            if ($cocho->nivel_atual <= 0) {
                return [(object) [
                    'prioridade' => 'critico',
                    'categoria' => 'cocho',
                    'titulo' => "Cocho {$cocho->nome_cocho} vazio",
                    'descricao' => 'Nenhuma ração disponível neste cocho no momento.',
                ]];
            }

            $percentual = ($cocho->nivel_atual / $cocho->capacidade) * 100;

            // Mesmo limiar da tela de Cochos e da API (Cocho::PERCENTUAL_ALERTA).
            if ($percentual < Cocho::PERCENTUAL_ALERTA) {
                return [(object) [
                    'prioridade' => 'baixo',
                    'categoria' => 'cocho',
                    'titulo' => "Nível baixo no cocho {$cocho->nome_cocho}",
                    'descricao' => 'Está com apenas ' . round($percentual) . '% da capacidade — considere reabastecer.',
                ]];
            }

            return [];
        }))->values();
    }

    /**
     * Sensores IoT dos cochos: com erro (leitura recusada) ou offline (sem
     * leitura dentro de config('iot.minutos_offline')). Sensor que nunca
     * enviou nada não vira alerta — é só "sem leitura".
     */
    private function alertasSensoresCochos($cochos)
    {
        return $cochos->flatMap(function ($cocho) {
            return $cocho->sensores->where('ativo', true)->map(function (Sensor $sensor) use ($cocho) {
                $nome = ($sensor->tipo === Sensor::TIPO_BALANCA ? 'Balança' : 'Sensor de distância') . " {$sensor->codigo}";

                return match ($sensor->status()) {
                    Sensor::STATUS_ERRO => (object) [
                        'prioridade' => 'critico',
                        'categoria' => 'cocho',
                        'titulo' => "{$nome} com erro ({$cocho->nome_cocho})",
                        'descricao' => (string) $sensor->ultimo_erro,
                    ],
                    Sensor::STATUS_OFFLINE => (object) [
                        'prioridade' => 'baixo',
                        'categoria' => 'cocho',
                        'titulo' => "{$nome} offline ({$cocho->nome_cocho})",
                        'descricao' => 'Sem leitura desde ' . $sensor->ultima_leitura_em->format('d/m H:i') . ' — verifique a conexão do sensor.',
                    ],
                    default => null,
                };
            })->filter();
        })->values();
    }

    /** Lotes com animais mas sem nenhum cocho de alimentação vinculado. */
    private function alertasLotesSemCocho($lotes, $cochos)
    {
        $loteIdsComCocho = $cochos->pluck('lote_id')->map(fn ($id) => (string) $id);

        return $lotes
            ->reject(fn ($lote) => $loteIdsComCocho->contains((string) $lote->id))
            ->map(fn ($lote) => (object) [
                'prioridade' => 'baixo',
                'categoria' => 'lote',
                'titulo' => "{$lote->nome_lote} sem cocho cadastrado",
                'descricao' => 'Este lote não tem nenhum cocho de alimentação vinculado.',
            ])
            ->values();
    }

    /**
     * Lotes que já têm plano alimentar ativo (algum registro histórico em
     * "planos") mas nenhuma alimentação lançada hoje — indício de que a
     * rotina foi esquecida. Lotes recém-cadastrados, sem nenhum histórico
     * ainda, não geram esse alerta.
     */
    /** Lotes elegíveis à ordenha (ativos, de espécie leiteira, com fêmeas)
     *  que já têm histórico de produção mas não têm nenhuma ordenha
     *  registrada hoje — indício de que a rotina foi esquecida. Um lote
     *  elegível que nunca foi ordenhado (recém-criado) não gera alerta. */
    private function alertasLotesSemOrdenhaHoje($lotesElegiveis, $loteIdsComHistoricoOrdenha, $ordenhasRecentes, $hoje)
    {
        $loteIdsComOrdenhaHoje = $ordenhasRecentes
            ->filter(fn ($o) => $o->data->isSameDay($hoje))
            ->pluck('lote_id');

        return $lotesElegiveis
            ->filter(fn ($lote) => $loteIdsComHistoricoOrdenha->contains($lote->id))
            ->reject(fn ($lote) => $loteIdsComOrdenhaHoje->contains($lote->id))
            ->map(fn ($lote) => (object) [
                'prioridade' => 'baixo',
                'categoria' => 'ordenha',
                'titulo' => "Sem ordenha registrada hoje: {$lote->nome_lote}",
                'descricao' => 'Este lote produz leite normalmente, mas nenhuma ordenha foi registrada hoje.',
            ])->values();
    }

    /**
     * Alerta só para lotes que JÁ têm ração vinculada (o vínculo é o que
     * torna o consumo automático a partir de agora — sem vínculo, "sem
     * consumo hoje" seria óbvio demais pra virar alerta) mas cujo snapshot
     * de hoje ainda não existe ou veio zerado — normalmente porque ainda
     * não passou das 23:55 (quando o comando agendado plano:capturar-
     * snapshots roda) nem alguém abriu o Plano Alimentar hoje ainda.
     */
    private function alertasAlimentacaoHoje($lotes, $snapshotsHoje)
    {
        $snapshotPorLote = $snapshotsHoje->keyBy(fn ($s) => (string) $s->lote_id);

        return $lotes->filter(fn ($lote) => $lote->racao_id !== null)
            ->flatMap(function ($lote) use ($snapshotPorLote) {
                $snapshot = $snapshotPorLote->get((string) $lote->id);

                if ($snapshot && (float) $snapshot->consumo_kg > 0) {
                    return [];
                }

                return [(object) [
                    'prioridade' => 'baixo',
                    'categoria' => 'consumo',
                    'titulo' => "Sem consumo registrado hoje: {$lote->nome_lote}",
                    'descricao' => 'Este lote tem ração vinculada, mas ainda não há registro de consumo hoje.',
                ]];
            })->values();
    }

    /**
     * Preço por kg da ração consumida num snapshot: a ração gravada no
     * próprio snapshot; senão a vinculada ao lote; senão (lote sem vínculo,
     * cujo consumo vem da ração sugerida pelo Plano Alimentar) a média do
     * valor_kg das rações da fazenda do mesmo tipo de animal do lote; e, em
     * último caso, a média de todas as rações da fazenda.
     */
    private function valorKgDoSnapshot($snapshot, $lotes, $racoesFazenda): float
    {
        $lote = $lotes->firstWhere('id', $snapshot->lote_id);
        $racaoId = $snapshot->racao_id ?: optional($lote)->racao_id;
        $racao = $racaoId ? $racoesFazenda->firstWhere('id', $racaoId) : null;

        if ($racao) {
            return (float) $racao->valor_kg;
        }

        $mesmoTipo = $lote ? $racoesFazenda->where('tipo', $lote->tipo_animal) : collect();

        return (float) ($mesmoTipo->isNotEmpty() ? $mesmoTipo->avg('valor_kg') : $racoesFazenda->avg('valor_kg'));
    }

    /** Rótulos (Dom..Sáb) dos últimos 7 dias, terminando hoje. */
    private function labelsUltimos7Dias(): array
    {
        $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = $diasSemana[Carbon::today()->subDays($i)->dayOfWeek];
        }

        return $labels;
    }

    /** Rótulos (mês/ano abreviado, em português) dos últimos 6 meses,
     *  terminando no atual — mesmo padrão de labelsUltimos7Dias() (nomes
     *  fixos em vez de depender do locale do Carbon, que não está
     *  configurado como pt-BR neste projeto). */
    private function labelsUltimos6Meses(): array
    {
        $mesesAbreviados = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::today()->subMonths($i);
            $labels[] = $mesesAbreviados[$mes->month - 1] . '/' . $mes->format('y');
        }

        return $labels;
    }

    /** Variação percentual entre hoje e o valor anterior, ou null sem base de comparação. */
    private function variacaoPercentual(float $atual, float $anterior): ?float
    {
        if ($anterior <= 0) {
            return $atual > 0 ? null : 0.0;
        }

        return round((($atual - $anterior) / $anterior) * 100, 1);
    }

    // PÁGINA PROJETO VÍDEO
    public function projetovideo(Request $request)
    {
        return view('projetovideo', [
            'animaisPorLote' => $this->animaisPorLote($request),
        ]);
    }

    // Mesmo dado do gráfico "Animais por Lote" da página inicial, em JSON —
    // o gráfico consulta periodicamente pra animar a atualização quando
    // os lotes mudam.
    public function animaisPorLoteJson(Request $request)
    {
        return response()->json($this->animaisPorLote($request));
    }

    /** Padroniza a caixa só pra exibição ("LOTE 1" / "lote 2" → "Lote 1",
     *  "Lote 2"): primeira letra de cada palavra maiúscula, conectivos
     *  (de, da, do...) minúsculos e códigos com número (A1, B2) maiúsculos. */
    private function padronizarNome(string $nome): string
    {
        $palavras = preg_split('/\s+/u', trim($nome));
        $conectivos = ['de', 'da', 'do', 'das', 'dos', 'e'];

        foreach ($palavras as $i => $palavra) {
            $minuscula = mb_strtolower($palavra);

            if (preg_match('/\d/u', $palavra)) {
                $palavras[$i] = mb_strtoupper($palavra);
            } elseif ($i > 0 && in_array($minuscula, $conectivos, true)) {
                $palavras[$i] = $minuscula;
            } else {
                $palavras[$i] = mb_convert_case($minuscula, MB_CASE_TITLE);
            }
        }

        return implode(' ', $palavras);
    }

    /** Distribuição de animais entre os lotes ativos (os 4 maiores + o
     *  restante somado em "Outros"). Logado: só a fazenda SELECIONADA (a
     *  mesma do Dashboard — ?fazenda_id= ou a da sessão; sem nenhuma
     *  escolhida, fica vazio como o Dashboard). Mesma regra do
     *  GET /api/lotes/animais-por-lote do app. Visitante: só totais por
     *  espécie (ver animaisPorEspecie). */
    private function animaisPorLote(Request $request): array
    {
        $query = Lote::ativos()->where('quantidade_animais', '>', 0);

        if (session('usuario_id')) {
            $fazendas = Fazenda::where('cadastro_id', session('usuario_id'))->orderBy('nome_fazenda')->get();
            $fazendaId = $this->resolverFazendaSelecionadaComSessao($request, $fazendas);

            if (!$fazendaId) {
                return ['total' => 0, 'lotes' => [], 'fazenda' => null];
            }

            $query->where('fazenda_id', $fazendaId);
        }

        $lotes = $query->orderByDesc('quantidade_animais')
            ->get(['id', 'nome_lote', 'tipo_animal', 'raca', 'finalidade', 'sexo', 'idade_media', 'quantidade_animais', 'quantidade_cochos', 'peso_medio', 'created_at']);

        $total = (int) $lotes->sum('quantidade_animais');

        // Visitante (página pública): nunca vê lotes de ninguém — só o total
        // de animais por ESPÉCIE, somado entre todas as contas. Antes recebia
        // nome, raça, peso e idade dos 4 maiores lotes de outros usuários.
        if (!session('usuario_id')) {
            return $this->animaisPorEspecie($lotes, $total);
        }

        // "detalhes" alimenta o painel que abre ao clicar numa fatia do
        // gráfico — só dados de manejo do lote, nada que identifique a
        // fazenda ou o dono (a página é pública).
        $itens = $lotes->take(4)->map(fn ($lote) => [
            'nome' => trim($this->padronizarNome($lote->nome_lote)
                . ($lote->tipo_animal ? ' - ' . $this->padronizarNome($lote->tipo_animal) : '')),
            'quantidade' => (int) $lote->quantidade_animais,
            'detalhes' => [
                'lote' => $this->padronizarNome($lote->nome_lote),
                'especie' => $lote->tipo_animal,
                'raca' => $lote->raca ? $this->padronizarNome($lote->raca) : null,
                'finalidade' => $lote->finalidade,
                'sexo' => $lote->sexo,
                'idade_media' => $lote->idade_media,
                'peso_medio' => (float) $lote->peso_medio,
                'cochos' => (int) $lote->quantidade_cochos,
                'dias_no_sistema' => $lote->created_at ? (int) $lote->created_at->copy()->startOfDay()->diffInDays(Carbon::today()) : null,
            ],
        ])->values();

        $outros = $lotes->slice(4);
        $restante = (int) $outros->sum('quantidade_animais');
        if ($restante > 0) {
            $itens->push([
                'nome' => 'Outros',
                'quantidade' => $restante,
                'detalhes' => [
                    'agrupados' => $outros->count(),
                    'especies' => $outros->groupBy('tipo_animal')
                        ->map(fn ($grupo, $tipo) => ['especie' => $tipo, 'animais' => (int) $grupo->sum('quantidade_animais')])
                        ->sortByDesc('animais')->values()->all(),
                    'peso_medio' => $restante > 0
                        ? round($outros->sum(fn ($l) => $l->peso_medio * $l->quantidade_animais) / $restante, 2)
                        : 0,
                    'cochos' => (int) $outros->sum('quantidade_cochos'),
                ],
            ]);
        }

        return [
            'total' => $total,
            'lotes' => $itens->map(fn ($item) => $item + [
                'percentual' => $total > 0 ? (int) round($item['quantidade'] / $total * 100) : 0,
            ])->all(),
        ];
    }

    /** Gráfico público: animais por espécie (4 maiores + "Outros"), sem
     *  nenhum dado que identifique um lote, fazenda ou dono. */
    private function animaisPorEspecie($lotes, int $total): array
    {
        $grupos = $lotes->groupBy(fn ($l) => $l->tipo_animal ?: 'Não informado')
            ->map(fn ($grupo, $especie) => [
                'especie' => $especie,
                'animais' => (int) $grupo->sum('quantidade_animais'),
                'lotes' => $grupo->count(),
                'cochos' => (int) $grupo->sum('quantidade_cochos'),
                'peso_total' => (float) $grupo->sum(fn ($l) => $l->peso_medio * $l->quantidade_animais),
            ])
            ->sortByDesc('animais')->values();

        $resumo = fn ($itens, string $nome) => [
            'nome' => $nome,
            'quantidade' => (int) $itens->sum('animais'),
            'detalhes' => [
                'agrupados' => (int) $itens->sum('lotes'),
                'especies' => $itens->map(fn ($g) => ['especie' => $g['especie'], 'animais' => $g['animais']])->values()->all(),
                'peso_medio' => $itens->sum('animais') > 0 ? round($itens->sum('peso_total') / $itens->sum('animais'), 2) : 0,
                'cochos' => (int) $itens->sum('cochos'),
            ],
        ];

        $itens = $grupos->take(4)->map(fn ($g) => $resumo(collect([$g]), $this->padronizarNome($g['especie'])))->values();
        if ($grupos->count() > 4) {
            $itens->push($resumo($grupos->slice(4), 'Outros'));
        }

        return [
            'total' => $total,
            'lotes' => $itens->map(fn ($item) => $item + [
                'percentual' => $total > 0 ? (int) round($item['quantidade'] / $total * 100) : 0,
            ])->all(),
        ];
    }

    // Landing page: cada seção (menos o Início/Hero) virou uma página
    // própria, sem rolagem entre elas — mesmo padrão que a Avaliações
    // (avaliacoes.blade.php) já usava.
    public function landingProblema()
    {
        return view('landing.problema');
    }

    public function landingSolucao()
    {
        return view('landing.solucao');
    }

    public function landingComoFunciona()
    {
        return view('landing.como-funciona');
    }

    public function landingBeneficios()
    {
        return view('landing.beneficios');
    }

    public function landingSobre()
    {
        return view('landing.sobre');
    }

    public function landingContato()
    {
        return view('landing.contato');
    }

    // FORMULÁRIO CADASTRO
    public function cadastro()
    {
        return view('cadastro');
    }

    // CADASTRAR USUÁRIO
    public function add(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:cadastros,email',
            'password' => ['required', 'string', 'confirmed', 'regex:' . self::SENHA_REGEX],
            'telefone' => 'required|string|max:20'
        ], [
            'password.regex' => self::SENHA_MENSAGEM,
            ...self::MENSAGENS_CADASTRO,
        ]);

        Cadastro::create([
            'responsavel' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->password),
            'telefone' => $request->telefone
        ]);

        return redirect()
            ->route('login.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    // AUTENTICAR LOGIN
    public function autenticar(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Cadastro::where('email', $request->email)->first();

        // Mesma mensagem genérica para "e-mail não existe" e "senha errada"
        // — devolver um erro diferente pra cada caso (como antes) permite
        // enumerar e-mails cadastrados só testando um a um, contrariando o
        // mesmo cuidado já tomado no fluxo de "esqueci minha senha".
        if (!$usuario || !Hash::check($request->password, $usuario->senha)) {
            return back()
                ->withErrors([
                    'email' => 'E-mail ou senha inválidos.'
                ])
                ->onlyInput('email');
        }

        // Gera um novo ID de sessão ao autenticar (mantendo os dados já
        // presentes) para prevenir fixação de sessão — sem isso, um ID de
        // sessão obtido antes do login continuaria válido depois dele.
        $request->session()->regenerate();

        session([
            'usuario_id' => $usuario->id,
            'usuario_nome' => $usuario->responsavel,
            'usuario_email' => $usuario->email
        ]);

        // Volta para a página que exigiu login (ver EnsureCadastroAutenticado
        // ::redirectTo), ou o Dashboard quando o acesso começou pelo /login.
        return redirect()
            ->intended(route('cadastro.index'))
            ->with('success', 'Login realizado com sucesso!');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        // invalidate() derruba os dados E troca o ID de sessão (diferente de
        // flush(), que só limpava os dados mas mantinha o mesmo ID) —
        // regenerateToken() troca também o token CSRF, já que o antigo foi
        // invalidado junto com a sessão.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login.index')
            ->with('success', 'Logout realizado com sucesso!');
    }

    // PERFIL DO ADMINISTRADOR (acessado pelo avatar na navbar)
    public function perfil()
    {
        // A autenticação em si (sessão válida) já é garantida pelo middleware
        // 'auth.cadastro' aplicado ao grupo de rotas (ver routes/web.php).
        $cadastro = Cadastro::findOrFail(session('usuario_id'));
        // O layout global (navbar/drawer) espera $cadastroLogado, igual às
        // demais páginas — aqui é o mesmo registro que $cadastro.
        $cadastroLogado = $cadastro;

        return view('perfil', compact('cadastro', 'cadastroLogado'));
    }

    // ATUALIZAR PERFIL
    //
    // Fluxo de segurança: qualquer alteração de perfil exige a senha atual
    // (AtualizarPerfilRequest::rules -> SenhaAtualCorreta), CPF só é aceito
    // para maiores de 18 anos (comprovado pela data de nascimento), a nova
    // senha precisa ser forte e diferente da anterior, e um e-mail de
    // alerta é disparado a cada alteração sensível — inclusive para o
    // endereço de e-mail ANTIGO quando o próprio e-mail é trocado, para que
    // o dono original da conta seja avisado mesmo que o atacante já tenha
    // trocado o e-mail cadastrado.
    public function atualizarPerfil(AtualizarPerfilRequest $request)
    {
        $cadastro = $request->cadastroLogado();
        $dadosValidados = $request->validated();

        $emailAnterior = $cadastro->email;
        $fotoAnterior = $cadastro->foto;

        $novoCpf = $dadosValidados['cpf'] ?? null;
        $senhaAlterada = $request->filled('password');
        $fotoAlterada = $request->hasFile('foto');
        $emailAlterado = $dadosValidados['email'] !== $emailAnterior;

        // A senha não entra na lista de "campos alterados" do aviso abaixo
        // porque ela ainda não foi aplicada neste momento — só entra em
        // vigor depois que o código de confirmação (enviado logo adiante)
        // for validado em confirmarSenha().
        $camposAlterados = array_filter([
            $dadosValidados['responsavel'] !== $cadastro->responsavel ? 'Nome' : null,
            $novoCpf !== $cadastro->cpf ? 'CPF' : null,
            $dadosValidados['telefone'] !== $cadastro->telefone ? 'Telefone' : null,
            $emailAlterado ? 'E-mail' : null,
            $fotoAlterada ? 'Foto de perfil' : null,
        ]);

        $dados = [
            'responsavel' => $dadosValidados['responsavel'],
            'cpf' => $novoCpf,
            'data_nascimento' => $dadosValidados['data_nascimento'] ?? null,
            'email' => $dadosValidados['email'],
            'telefone' => $dadosValidados['telefone'],
        ];

        if ($emailAlterado) {
            $dados['email_alterado_em'] = now();
            // O "verificado" é sempre do endereço ATUAL — trocar de e-mail
            // exige verificar de novo o novo endereço, nunca herda o status
            // do antigo.
            $dados['email_verificado_em'] = null;
        }

        if ($fotoAlterada) {
            $dados['foto'] = $request->file('foto')->store('perfil', 'public');
        }

        DB::transaction(function () use ($cadastro, $dados, $fotoAlterada, $fotoAnterior) {
            $cadastro->update($dados);

            // Só remove o arquivo antigo depois que o novo caminho já foi
            // gravado com sucesso, para nunca ficar sem nenhuma foto no
            // disco caso o update falhe.
            // A foto anterior pode ser a URL da conta Google — aí não há
            // arquivo nosso para apagar.
            if ($fotoAlterada && $fotoAnterior && !Cadastro::fotoEhExterna($fotoAnterior)) {
                Storage::disk('public')->delete($fotoAnterior);
            }
        });

        session(['usuario_nome' => $cadastro->responsavel]);

        if ($camposAlterados !== []) {
            $alerta = new PerfilAtualizadoNotification(
                array_values($camposAlterados),
                now()->format('d/m/Y H:i'),
                $request->ip()
            );

            $cadastro->notify($alerta);

            if ($emailAlterado) {
                Notification::route('mail', $emailAnterior)->notify($alerta);
            }
        }

        // A senha nova fica "pendente": guardada com hash junto do código
        // (nunca gravada no cadastro ainda) até o usuário confirmar o
        // código enviado por e-mail em /perfil/confirmar-senha.
        if ($senhaAlterada) {
            $codigo = CodigoVerificacao::gerar(
                $cadastro->email,
                'alteracao_senha',
                Hash::make($dadosValidados['password'])
            );

            Notification::route('mail', $cadastro->email)
                ->notify(new CodigoVerificacaoNotification($codigo, 'troca de senha'));

            return redirect()
                ->route('perfil.confirmarSenha')
                ->with('perfil_success', 'Perfil atualizado! Enviamos um código para confirmar a nova senha.');
        }

        // volta para a página de onde o formulário foi enviado: tanto a
        // tela /perfil quanto o modal "Editar Perfil" (presente na navbar
        // de todas as telas) usam esta mesma rota.
        return redirect()
            ->back()
            ->with('perfil_success', 'Perfil atualizado com sucesso!');
    }

    // As antigas rotas /cadastro/{id} (editar/atualizar/excluir) foram
    // removidas: não existe tela nenhuma de administração de usuários que
    // aponte para elas (o único fluxo real de edição da própria conta é
    // /perfil/update, ver atualizarPerfil() acima) e elas não tinham
    // NENHUMA checagem de autenticação nem de dono — bastava trocar o {id}
    // na URL para qualquer visitante atualizar (inclusive a senha) ou
    // excluir a conta de QUALQUER outro usuário do sistema.

    // ==========================================
    // ESQUECEU A SENHA & REDEFINIÇÃO (por código de verificação)
    // ==========================================

    // TELA: Solicitar redefinição (informar o e-mail)
    public function esqueciSenha()
    {
        return view('esqueciSenha');
    }

    // AÇÃO: Gera o código de verificação e envia por e-mail.
    //
    // Por segurança, NUNCA revela se o e-mail existe ou não na base: a
    // validação usada aqui é só de formato ("email"), sem "exists". Se o
    // e-mail não estiver cadastrado, a rota simplesmente não envia nada —
    // mas responde com a mesma tela e a mesma mensagem de sucesso de quando
    // envia de verdade, pra um invasor não conseguir descobrir contas
    // válidas testando e-mails um a um (enumeração de usuários).
    public function enviarCodigoSenha(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $usuario = Cadastro::where('email', $request->email)->first();

        if ($usuario) {
            $codigo = CodigoVerificacao::gerar($request->email, 'redefinicao_senha');

            Notification::route('mail', $request->email)
                ->notify(new CodigoVerificacaoNotification($codigo, 'redefinição de senha'));
        }

        return redirect()
            ->route('password.reset', ['email' => $request->email])
            ->with('success', 'Se este e-mail estiver cadastrado, enviamos um código de verificação para ele.');
    }

    // TELA: Digitar o código + nova senha
    public function mostrarFormRedefinir(Request $request)
    {
        $email = $request->query('email');

        return view('auth.reset-password', compact('email'));
    }

    // AÇÃO: Confirma o código e grava a nova senha.
    //
    // Mesma regra de não-enumeração do envio: sem "exists" na validação do
    // e-mail. Um e-mail nunca cadastrado nunca vai ter um código válido
    // pendente (gerar() só roda pra quem existe — ver enviarCodigoSenha),
    // então cai naturalmente no mesmo erro genérico "Código inválido ou
    // expirado" usado pra qualquer código errado, sem precisar de uma
    // mensagem própria que entregaria a existência da conta.
    public function redefinirSenha(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'codigo' => 'required|digits:6',
            'password' => ['required', 'string', 'confirmed', 'regex:' . self::SENHA_REGEX],
        ], [
            'password.regex' => self::SENHA_MENSAGEM,
        ]);

        $codigoValido = CodigoVerificacao::validar($request->email, $request->codigo, 'redefinicao_senha');
        $usuario = $codigoValido ? Cadastro::where('email', $request->email)->first() : null;

        if (!$codigoValido || !$usuario) {
            return back()->withErrors(['codigo' => 'Código inválido ou expirado.'])->withInput();
        }

        // A nova senha não pode repetir a atual. Checado antes de consumir o
        // código, para o usuário poder tentar de novo com o mesmo código.
        if (Hash::check($request->password, $usuario->senha)) {
            return back()->withErrors(['password' => 'A nova senha deve ser diferente da senha anterior.'])->withInput();
        }

        $usuario->update([
            'senha' => Hash::make($request->password),
            'senha_alterada_em' => now(),
        ]);
        $codigoValido->marcarUsado();

        return redirect()
            ->route('login.index')
            ->with('success', 'Sua senha foi redefinida com sucesso!');
    }

    // ==========================================
    // CONFIRMAÇÃO DE TROCA DE SENHA (a partir do Perfil, já logado)
    // ==========================================

    // TELA: Digitar o código enviado para confirmar a troca de senha
    public function mostrarConfirmarSenha()
    {
        return view('perfilConfirmarSenha');
    }

    // AÇÃO: Confirma o código e efetiva a nova senha, deixada pendente em atualizarPerfil()
    public function confirmarSenha(Request $request)
    {
        $cadastro = Cadastro::find(session('usuario_id'));

        if (!$cadastro) {
            return redirect()->route('login.index');
        }

        $request->validate(['codigo' => 'required|digits:6']);

        $codigoValido = CodigoVerificacao::validar($cadastro->email, $request->codigo, 'alteracao_senha');

        if (!$codigoValido) {
            return back()->withErrors(['codigo' => 'Código inválido ou expirado.']);
        }

        $cadastro->update([
            'senha' => $codigoValido->payload,
            'senha_alterada_em' => now(),
        ]);
        $codigoValido->marcarUsado();

        return redirect()->route('perfil')->with('perfil_success', 'Senha alterada com sucesso!');
    }

    // ==========================================
    // VERIFICAÇÃO DE E-MAIL (a partir do Perfil, já logado)
    //
    // Reaproveita o mesmo App\Models\CodigoVerificacao usado em "esqueci
    // minha senha" / troca de senha, com o propósito 'verificacao_email' —
    // mesma garantia de expiração (15 min), hash do código e impossibilidade
    // de reuso após confirmado. O status fica em cadastros.email_verificado_em
    // (null = não verificado), que é a fonte de verdade lida no Perfil.
    // ==========================================

    // TELA: Digitar o código enviado para verificar o e-mail
    public function mostrarVerificarEmail()
    {
        $cadastro = Cadastro::find(session('usuario_id'));

        if (!$cadastro) {
            return redirect()->route('login.index');
        }

        // Já verificado: não faz sentido reabrir a tela de código.
        if ($cadastro->email_verificado) {
            return redirect()->route('perfil')->with('perfil_success', 'Seu e-mail já está verificado.');
        }

        return view('perfilVerificarEmail', compact('cadastro'));
    }

    // AÇÃO: Gera (ou reenvia) o código de verificação para o e-mail da conta.
    // Mesma rota atende o primeiro envio e o "Reenviar código" — gerar()
    // sempre invalida qualquer código pendente anterior antes de criar um novo.
    public function enviarCodigoVerificacaoEmail(Request $request)
    {
        $cadastro = Cadastro::find(session('usuario_id'));

        if (!$cadastro) {
            return redirect()->route('login.index');
        }

        if ($cadastro->email_verificado) {
            return redirect()->route('perfil')->with('perfil_success', 'Seu e-mail já está verificado.');
        }

        $codigo = CodigoVerificacao::gerar($cadastro->email, 'verificacao_email');

        Notification::route('mail', $cadastro->email)
            ->notify(new CodigoVerificacaoNotification($codigo, 'verificação de e-mail'));

        return redirect()
            ->route('perfil.verificarEmail')
            ->with('success', 'Enviamos um código de verificação para ' . $cadastro->email . '.');
    }

    // AÇÃO: Confirma o código e marca o e-mail como verificado.
    public function confirmarVerificacaoEmail(Request $request)
    {
        $cadastro = Cadastro::find(session('usuario_id'));

        if (!$cadastro) {
            return redirect()->route('login.index');
        }

        $request->validate([
            'codigo' => 'required|digits:6',
        ], [
            'codigo.required' => 'Digite o código de 6 dígitos enviado para o seu e-mail.',
            'codigo.digits' => 'O código deve ter exatamente 6 dígitos.',
        ]);

        $codigoValido = CodigoVerificacao::validar($cadastro->email, $request->codigo, 'verificacao_email');

        if (!$codigoValido) {
            $motivo = CodigoVerificacao::motivoFalha($cadastro->email, $request->codigo, 'verificacao_email');

            $mensagem = match ($motivo) {
                'expirado' => 'Este código expirou. Clique em "Reenviar código" para receber um novo.',
                'sem_codigo' => 'Nenhum código pendente encontrado. Clique em "Reenviar código" para solicitar um.',
                default => 'Código incorreto. Confira os 6 dígitos e tente novamente.',
            };

            return back()->withErrors(['codigo' => $mensagem]);
        }

        $cadastro->update(['email_verificado_em' => now()]);
        $codigoValido->marcarUsado();

        return redirect()->route('perfil')->with('perfil_success', 'E-mail verificado com sucesso!');
    }
}
