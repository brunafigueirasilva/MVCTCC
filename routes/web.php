<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CadastroController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\RacaoController;
use App\Http\Controllers\FazendaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\CochoController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AssistenteIaController;
use App\Http\Controllers\AcessibilidadeController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\OrdenhaLeiteController;
use App\Http\Controllers\ContatoController;

/*
|--------------------------------------------------------------------------
| Rotas públicas (sem exigir login)
|--------------------------------------------------------------------------
|
| Landing page, autenticação (login/cadastro/recuperação de senha) e as
| poucas telas que existem propositalmente para visitantes não logados
| (Avaliações, painel de Acessibilidade). Tudo que exige uma conta fica no
| grupo "auth.cadastro" mais abaixo.
*/

// Página inicial
Route::get('/', [CadastroController::class, 'projetovideo'])
    ->name('projeto.index');

// Dados reais do gráfico "Animais por Lote" da página inicial (atualização
// animada sem recarregar a página).
Route::get('/animais-por-lote', [CadastroController::class, 'animaisPorLoteJson'])
    ->middleware('throttle:30,1')
    ->name('projeto.animaisPorLote');

// Landing page: cada seção é uma página própria (sem rolagem entre elas),
// mesmo padrão que /avaliacoes já usava.
Route::get('/problema', [CadastroController::class, 'landingProblema'])
    ->name('landing.problema');

Route::get('/solucao', [CadastroController::class, 'landingSolucao'])
    ->name('landing.solucao');

Route::get('/como-funciona', [CadastroController::class, 'landingComoFunciona'])
    ->name('landing.comoFunciona');

Route::get('/beneficios', [CadastroController::class, 'landingBeneficios'])
    ->name('landing.beneficios');

Route::get('/sobre', [CadastroController::class, 'landingSobre'])
    ->name('landing.sobre');

Route::get('/contato', [CadastroController::class, 'landingContato'])
    ->name('landing.contato');

// Envio do formulário "Entre em Contato": confirmação por e-mail/WhatsApp
// pra pessoa + cópia pra equipe. Público, então limitado contra spam.
Route::post('/contato', [ContatoController::class, 'enviar'])
    ->middleware('throttle:5,1')
    ->name('landing.contato.enviar');

// Login
Route::get('/login', [CadastroController::class, 'login'])
    ->name('login.index');

Route::post('/login', [CadastroController::class, 'autenticar'])
    ->middleware('throttle:6,1')
    ->name('login.autenticar');

// Logout: fora do grupo autenticado de propósito — encerrar uma sessão que
// já não existe mais (ex.: clique duplicado, sessão expirada em outra aba)
// não deve virar erro, só cair de volta no login.
Route::get('/logout', [CadastroController::class, 'logout'])
    ->name('logout');

// Cadastro de conta nova
Route::get('/cadastro', [CadastroController::class, 'cadastro'])
    ->name('cadastro.cadastro');

Route::post('/cadastro/add', [CadastroController::class, 'add'])
    ->middleware('throttle:6,1')
    ->name('cadastro.add');

// Avaliações (página pública de feedback, acessada pela landing page — não exige login)
Route::get('/avaliacoes', [ComentarioController::class, 'listar'])
    ->name('comentario.listar');

// Formulário público (sem login) — sem limite de tentativas, virava um alvo
// fácil de flood/spam automatizado (nada mais no formulário barra isso: sem
// captcha, sem exigir conta). Mesmo throttle já usado nos outros formulários
// públicos do sistema (recuperação de senha, verificação de e-mail).
Route::post('/avaliacoes', [ComentarioController::class, 'salvar'])
    ->middleware('throttle:6,1')
    ->name('comentario.salvar');

// PAINEL DE ACESSIBILIDADE: widget presente em toda página com navbar (ver
// partials/acessibilidade). Sem login, o controller devolve
// autenticado=false e o front-end continua só com localStorage — por isso
// nenhuma das duas rotas exige sessão para responder.
Route::get('/acessibilidade', [AcessibilidadeController::class, 'show'])
    ->name('acessibilidade.show');

Route::post('/acessibilidade', [AcessibilidadeController::class, 'store'])
    ->name('acessibilidade.store');

// ==========================================
// RECUPERAÇÃO DE SENHA (por código de verificação) — sempre pública, é
// exatamente para quem não consegue logar.
// ==========================================

// Tela: Exibe o formulário de "Esqueceu a senha"
Route::get('/esqueceu-senha', [CadastroController::class, 'esqueciSenha'])->name('password.request');

// Ação: Recebe o e-mail e envia o código de verificação
Route::post('/esqueceu-senha', [CadastroController::class, 'enviarCodigoSenha'])
    ->middleware('throttle:6,1')
    ->name('password.email');

// Tela: Exibe o formulário para digitar o CÓDIGO + nova senha (?email=... na URL)
Route::get('/redefinir-senha', [CadastroController::class, 'mostrarFormRedefinir'])->name('password.reset');

// Ação: Confirma o código e grava a nova senha
Route::post('/redefinir-senha', [CadastroController::class, 'redefinirSenha'])
    ->middleware('throttle:6,1')
    ->name('password.update');

// Login social (Google)
Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('google.login');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('google.callback');

/*
|--------------------------------------------------------------------------
| Rotas autenticadas (exigem sessão válida — ver
| App\Http\Middleware\EnsureCadastroAutenticado)
|--------------------------------------------------------------------------
|
| Tudo aqui dentro só responde para quem está logado: sem sessão válida, a
| middleware redireciona para /login (navegação normal) ou devolve 401 em
| JSON (requisições AJAX que enviam "Accept: application/json"), nunca
| deixando o Controller ser executado. Isso substitui as checagens manuais
| que existiam espalhadas (e, em alguns pontos, faltando) em cada Controller.
*/
Route::middleware('auth.cadastro')->group(function () {

    // Dashboard
    Route::get('/dashboard', [CadastroController::class, 'dashboard'])
        ->name('cadastro.index');

    // Resumo de alertas para o sino de notificações (disponível em toda a
    // aplicação, não só no Dashboard) — usado pelo modal de notificações.
    Route::get('/alertas/resumo', [CadastroController::class, 'alertasResumo'])
        ->name('alertas.resumo');

    // ==========================================
    // PERFIL DO ADMINISTRADOR (acessado pelo avatar na navbar)
    // ==========================================
    Route::get('/perfil', [CadastroController::class, 'perfil'])
        ->name('perfil');

    // Limite de tentativas: alteração de perfil mexe em dados sensíveis
    // (senha, e-mail, CPF), então é limitada para dificultar força bruta
    // contra o campo "senha atual".
    Route::put('/perfil/update', [CadastroController::class, 'atualizarPerfil'])
        ->middleware('throttle:6,1')
        ->name('perfil.update');

    // Confirmação por código do e-mail ao trocar a senha pelo perfil.
    Route::get('/perfil/confirmar-senha', [CadastroController::class, 'mostrarConfirmarSenha'])
        ->name('perfil.confirmarSenha');

    Route::post('/perfil/confirmar-senha', [CadastroController::class, 'confirmarSenha'])
        ->middleware('throttle:6,1')
        ->name('perfil.confirmarSenha.post');

    // Verificação de e-mail (a partir do Perfil, já logado).
    Route::get('/perfil/verificar-email', [CadastroController::class, 'mostrarVerificarEmail'])
        ->name('perfil.verificarEmail');

    // Mesma rota atende o primeiro envio e o "Reenviar código" — throttle
    // evita spam de envios (mesmo limite usado em "esqueci minha senha").
    Route::post('/perfil/verificar-email/enviar', [CadastroController::class, 'enviarCodigoVerificacaoEmail'])
        ->middleware('throttle:6,1')
        ->name('perfil.verificarEmail.enviar');

    Route::post('/perfil/verificar-email', [CadastroController::class, 'confirmarVerificacaoEmail'])
        ->middleware('throttle:6,1')
        ->name('perfil.verificarEmail.post');

    // ==========================================
    // FAZENDAS
    // ==========================================
    Route::get('/fazenda/listar', [FazendaController::class, 'listar'])
        ->name('fazenda.listar');

    Route::get('/fazenda/cadastrar', [FazendaController::class, 'cadastrar'])
        ->name('fazenda.cadastrar');

    Route::post('/fazenda/add', [FazendaController::class, 'add'])
        ->name('fazenda.add');

    Route::get('/fazenda/{id}', [FazendaController::class, 'atualizar'])
        ->name('fazenda.edit');

    Route::get('/fazenda/atualizar/{id}', [FazendaController::class, 'atualizar'])
        ->name('fazenda.atualizar');

    Route::put('/fazenda/update/{id}', [FazendaController::class, 'update'])
        ->name('fazenda.update');

    Route::delete('/fazenda/delete/{id}', [FazendaController::class, 'deletar'])
        ->name('fazenda.delete');

    // ==========================================
    // RAÇÕES
    // ==========================================
    Route::get('/racao/listar', [RacaoController::class, 'listar'])
        ->name('racao.listar');

    Route::get('/racao/cadastrar', [RacaoController::class, 'cadastrar'])
        ->name('racao.cadastrar');

    Route::post('/racao/add', [RacaoController::class, 'add'])
        ->name('racao.add');

    Route::get('/racao/{id}', [RacaoController::class, 'atualizar'])
        ->name('racao.edit');

    Route::get('/racao/atualizar/{id}', [RacaoController::class, 'atualizar'])
        ->name('racao.atualizar');

    Route::put('/racao/update/{id}', [RacaoController::class, 'update'])
        ->name('racao.update');

    Route::delete('/racao/delete/{id}', [RacaoController::class, 'deletar'])
        ->name('racao.delete');

    // ==========================================
    // ESTOQUE — só exibe o estoque direto das Rações (ver EstoqueController),
    // sem CRUD próprio de "movimentação" (removido: duplicava e podia
    // divergir do estoque real cadastrado em cada ração).
    // ==========================================
    Route::get('/estoque/listar', [EstoqueController::class, 'listar'])
        ->name('estoque.listar');

    // Entrada (compra) e saída manual de ração — mesmas regras da API
    // (POST /api/estoque/entrada|saida), registradas no histórico.
    Route::post('/estoque/entrada', [EstoqueController::class, 'entrada'])
        ->name('estoque.entrada');
    Route::post('/estoque/saida', [EstoqueController::class, 'saida'])
        ->name('estoque.saida');

    // ==========================================
    // PLANO ALIMENTAR — a tela calcula o consumo direto das rações
    // vinculadas aos lotes, sem nenhum registro manual de "alimentação"
    // criado pelo usuário.
    // ==========================================
    Route::get('/plano/listar', [PlanoController::class, 'listar'])
        ->name('plano.listar');

    Route::get('/plano/cadastrar', [PlanoController::class, 'cadastrar'])
        ->name('plano.cadastrar');

    // CONSULTAR LOTES (restante da lista "Consumo por Lote - Plano Diário" via AJAX)
    Route::get('/plano/lotes-consumo', [PlanoController::class, 'lotesConsumoJson'])
        ->name('plano.lotesConsumoJson');

    // CONSULTAR MAIS CONVERSÕES (restante da lista "Conversão Alimentar por Lote" via AJAX)
    Route::get('/plano/conversoes-alimentares', [PlanoController::class, 'conversoesAlimentaresJson'])
        ->name('plano.conversoesJson');

    // VINCULAR RAÇÃO: liga/troca/remove o vínculo lote-ração a partir do
    // modal de detalhes na tela de Plano Alimentar.
    Route::post('/plano/lotes/{loteId}/vincular-racao', [PlanoController::class, 'vincularRacao'])
        ->name('plano.vincularRacao');

    // REGISTRAR PESAGEM: checkpoint de peso do lote usado para calcular a
    // Conversão Alimentar real (kg de ração / kg de peso ganho) — ver
    // PlanoController::registrarPesagem / App\Models\DesempenhoLote.
    Route::post('/plano/lotes/{loteId}/pesagem', [PlanoController::class, 'registrarPesagem'])
        ->name('plano.registrarPesagem');

    // ==========================================
    // LOTES
    // ==========================================
    Route::get('/lotes', [LoteController::class, 'listar'])
        ->name('lote.listar');

    Route::get('/lote/cadastrar', [LoteController::class, 'cadastrar'])
        ->name('lote.cadastrar');

    Route::post('/lote/add', [LoteController::class, 'add'])
        ->name('lote.add');

    Route::get('/lote/atualizar/{id}', [LoteController::class, 'atualizar'])
        ->name('lote.atualizar');

    Route::put('/lote/update/{id}', [LoteController::class, 'update'])
        ->name('lote.update');

    Route::delete('/lote/delete/{id}', [LoteController::class, 'deletar'])
        ->name('lote.delete');

    Route::post('/lote/{id}/morte', [LoteController::class, 'registrarMorte'])
        ->name('lote.morte');

    Route::post('/lote/{id}/doenca', [LoteController::class, 'registrarDoenca'])
        ->name('lote.doenca');

    Route::post('/lote/{id}/recuperacao', [LoteController::class, 'registrarRecuperacao'])
        ->name('lote.recuperacao');

    Route::post('/lote/{id}/parto', [LoteController::class, 'registrarParto'])
        ->name('lote.parto');

    Route::post('/lote/{id}/eclosao', [LoteController::class, 'registrarEclosao'])
        ->name('lote.eclosao');

    Route::post('/lote/{id}/postura', [LoteController::class, 'registrarPostura'])
        ->name('lote.postura');

    // ORDENHA — produção de leite controlada exclusivamente por LOTE (sem
    // nenhum cadastro ou vínculo com animal individual), acessada a partir
    // da própria tela de Lote.
    Route::post('/lote/{id}/ordenha', [LoteController::class, 'registrarOrdenha'])
        ->name('lote.ordenha');

    Route::put('/lote/{id}/ordenha/{ordenhaId}', [LoteController::class, 'atualizarOrdenha'])
        ->name('lote.ordenha.update');

    Route::delete('/lote/{id}/ordenha/{ordenhaId}', [LoteController::class, 'excluirOrdenha'])
        ->name('lote.ordenha.delete');

    Route::get('/lote/{id}/ordenha/historico', [LoteController::class, 'historicoOrdenha'])
        ->name('lote.ordenha.historico');

    // ==========================================
    // ORDENHA E LEITE — página própria com os cartões/gráficos de produção
    // de leite por lote.
    // ==========================================
    Route::get('/ordenha-leite', [OrdenhaLeiteController::class, 'listar'])
        ->name('ordenha.listar');

    // CONSULTAR MAIS ORDENHAS (restante do histórico "Últimas Ordenhas
    // Registradas" via AJAX) — mesmo padrão do "Consultar Lotes"/"Consultar
    // mais Conversões" do Plano Alimentar.
    Route::get('/ordenha-leite/historico', [OrdenhaLeiteController::class, 'historicoJson'])
        ->name('ordenha.historicoJson');

    // ==========================================
    // COCHOS (agrupado por Lote — cochos são criados/removidos
    // automaticamente a partir de "Quantidade de Cochos" no Lote, ver
    // LoteController::sincronizarCochos(); não existe mais cadastro/
    // exclusão manual de cocho).
    // ==========================================
    Route::get('/cocho/listar', [CochoController::class, 'listar'])
        ->name('cocho.listar');

    Route::get('/cocho/cadastrar', [CochoController::class, 'cadastrar'])
        ->name('cocho.cadastrar');

    Route::get('/cocho/atualizar/{id}', [CochoController::class, 'atualizar'])
        ->name('cocho.atualizar');

    Route::put('/cocho/update/{id}', [CochoController::class, 'update'])
        ->name('cocho.update');

    // DETALHES / MONITORAMENTO IoT do cocho: a página e o JSON que ela
    // consome (mesmos dados de GET /api/cochos/{id}/iot e /iot/historico,
    // aqui pela sessão web).
    Route::get('/cocho/{id}/detalhes', [CochoController::class, 'detalhes'])
        ->whereNumber('id')
        ->name('cocho.detalhes');

    Route::get('/cocho/{id}/iot', [CochoController::class, 'iot'])
        ->whereNumber('id')
        ->name('cocho.iot');

    Route::get('/cocho/{id}/iot/historico', [CochoController::class, 'iotHistorico'])
        ->whereNumber('id')
        ->name('cocho.iot.historico');

    // ABASTECER: desconta do Estoque de Rações a quantidade colocada no(s)
    // cocho(s) — um cocho por vez, ou distribuído entre todos os cochos do
    // lote de uma vez (ver CochoController::abastecer/abastecerLote).
    Route::post('/cocho/{id}/abastecer', [CochoController::class, 'abastecer'])
        ->name('cocho.abastecer');

    // CONSUMO: quanto os animais comeram do cocho (cocho sem sensor) —
    // diminui o nível do cocho e grava o histórico (cocho_consumos).
    Route::post('/cocho/{id}/consumo', [CochoController::class, 'registrarConsumo'])
        ->name('cocho.consumo');

    Route::post('/lote/{id}/cochos/abastecer', [CochoController::class, 'abastecerLote'])
        ->name('cocho.abastecerLote');

    // ==========================================
    // RELATÓRIOS
    // ==========================================
    Route::get('/relatorios', [RelatorioController::class, 'listar'])
        ->name('relatorio.listar');

    // CONSULTAR MAIS DO RANKING (restante da tabela "Ranking de Lotes por
    // Desempenho" via AJAX) — mesmo padrão do "Consultar Lotes"/"Consultar
    // mais Conversões" do Plano Alimentar.
    Route::get('/relatorios/ranking', [RelatorioController::class, 'rankingJson'])
        ->name('relatorio.rankingJson');

    Route::get('/relatorio-pdf', [RelatorioController::class, 'exportarPDF'])
        ->name('relatorio.pdf');

    // ==========================================
    // ASSISTENTE IA: widget de chat flutuante presente em todas as telas
    // autenticadas. Limitado a 15 perguntas por minuto por IP — cada uma
    // dispara uma chamada de API paga, então precisa de uma barreira contra
    // abuso além do custo natural de digitar uma pergunta.
    // ==========================================
    Route::post('/assistente-ia/perguntar', [AssistenteIaController::class, 'perguntar'])
        ->middleware('throttle:15,1')
        ->name('assistenteIa.perguntar');
});
