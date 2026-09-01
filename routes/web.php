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
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\OrdenhaLeiteController;

// Página inicial
Route::get('/', [CadastroController::class, 'projetovideo'])
    ->name('projeto.index');

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

// Login
Route::get('/login', [CadastroController::class, 'login'])
    ->name('login.index');

Route::post('/login', [CadastroController::class, 'autenticar'])
    ->name('login.autenticar');

// Logout
Route::get('/logout', [CadastroController::class, 'logout'])
    ->name('logout');

// Dashboard
Route::get('/dashboard', [CadastroController::class, 'dashboard'])
    ->name('cadastro.index');

// Perfil do administrador (acessado pelo avatar na navbar)
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

// Avaliações (página pública de feedback, acessada pela landing page — não exige login)
Route::get('/avaliacoes', [ComentarioController::class, 'listar'])
    ->name('comentario.listar');

Route::post('/avaliacoes', [ComentarioController::class, 'salvar'])
    ->name('comentario.salvar');

// Cadastro
Route::get('/cadastro', [CadastroController::class, 'cadastro'])
    ->name('cadastro.cadastro');

Route::post('/cadastro/add', [CadastroController::class, 'add'])
    ->name('cadastro.add');

// Listagem
Route::get('/cadastro/listar', [CadastroController::class, 'listar'])
    ->name('cadastro.listar');

// Editar
Route::get('/cadastro/{id}', [CadastroController::class, 'atualizar'])
    ->name('cadastro.edit');

// Atualizar
Route::put('/cadastro/update/{id}', [CadastroController::class, 'update'])
    ->name('cadastro.update');

// Excluir
Route::delete('/cadastro/delete/{id}', [CadastroController::class, 'deletar'])
    ->name('cadastro.delete');


// LISTAR
Route::get('/fazenda/listar', [FazendaController::class, 'listar'])
    ->name('fazenda.listar');

// FORMULÁRIO CADASTRO
Route::get('/fazenda/cadastrar', [FazendaController::class, 'cadastrar'])
    ->name('fazenda.cadastrar');

// ADICIONAR
Route::post('/fazenda/add', [FazendaController::class, 'add'])
    ->name('fazenda.add');

// FORMULÁRIO ATUALIZAR
Route::get('/fazenda/{id}', [FazendaController::class, 'atualizar'])
    ->name('fazenda.edit');

Route::get('/fazenda/atualizar/{id}', [FazendaController::class, 'atualizar'])
    ->name('fazenda.atualizar');

// UPDATE
Route::put('/fazenda/update/{id}', [FazendaController::class, 'update'])
    ->name('fazenda.update');

// DELETAR
Route::delete('/fazenda/delete/{id}', [FazendaController::class, 'deletar'])
    ->name('fazenda.delete');


// LISTAR
Route::get('/racao/listar', [RacaoController::class, 'listar'])
    ->name('racao.listar');

// FORM CADASTRAR
Route::get('/racao/cadastrar', [RacaoController::class, 'cadastrar'])
    ->name('racao.cadastrar');

// ADICIONAR
Route::post('/racao/add', [RacaoController::class, 'add'])
    ->name('racao.add');

// FORMULÁRIO ATUALIZAR
Route::get('/racao/{id}', [RacaoController::class, 'atualizar'])
    ->name('racao.edit');

Route::get('/racao/atualizar/{id}', [RacaoController::class, 'atualizar'])
    ->name('racao.atualizar');

// UPDATE
Route::put('/racao/update/{id}', [RacaoController::class, 'update'])
    ->name('racao.update');

// DELETAR
Route::delete('/racao/delete/{id}', [RacaoController::class, 'deletar'])
    ->name('racao.delete');




// LISTAR — só exibe o estoque direto das Rações (ver EstoqueController),
// sem CRUD próprio de "movimentação" (removido: duplicava e podia
// divergir do estoque real cadastrado em cada ração).
Route::get('/estoque/listar', [EstoqueController::class, 'listar'])
    ->name('estoque.listar');



// LISTAR — a tela calcula o consumo direto das rações vinculadas aos
// lotes, sem nenhum registro manual de "alimentação" criado pelo usuário.
Route::get('/plano/listar', [PlanoController::class, 'listar'])
    ->name('plano.listar');

// FORM CADASTRAR
Route::get('/plano/cadastrar', [PlanoController::class, 'cadastrar'])
    ->name('plano.cadastrar');

// CONSULTAR LOTES (restante da lista "Consumo por Lote - Plano Diário" via
// AJAX)
Route::get('/plano/lotes-consumo', [PlanoController::class, 'lotesConsumoJson'])
    ->name('plano.lotesConsumoJson');

// CONSULTAR MAIS CONVERSÕES (restante da lista "Conversão Alimentar por
// Lote" via AJAX).
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

// ==========================================
// ANIMAL — módulo de Parto/Reprodução/Lactação/Ordenha por animal
// individual, acessado a partir da própria tela de Lote (cada animal
// pertence a um lote). Substitui o antigo registro agregado de
// ordenha/parto por lote (lote.ordenha/lote.parto foram removidas).
// ==========================================
Route::post('/animal/add', [AnimalController::class, 'add'])
->name('animal.add');

Route::get('/animal/atualizar/{id}', [AnimalController::class, 'atualizar'])
->name('animal.atualizar');

Route::put('/animal/update/{id}', [AnimalController::class, 'update'])
->name('animal.update');

Route::delete('/animal/delete/{id}', [AnimalController::class, 'deletar'])
->name('animal.delete');

Route::get('/animal/{id}', [AnimalController::class, 'detalhe'])
->name('animal.detalhe');

Route::post('/animal/{id}/parto', [AnimalController::class, 'registrarParto'])
->name('animal.parto');

Route::put('/animal/{id}/parto/{partoId}/realizar', [AnimalController::class, 'marcarPartoRealizado'])
->name('animal.parto.realizar');

Route::put('/animal/{id}/parto/{partoId}/cancelar', [AnimalController::class, 'cancelarParto'])
->name('animal.parto.cancelar');

Route::post('/animal/{id}/ordenha', [AnimalController::class, 'registrarOrdenha'])
->name('animal.ordenha');

Route::put('/animal/{id}/lactacao/{lactacaoId}/encerrar', [AnimalController::class, 'encerrarLactacao'])
->name('animal.lactacao.encerrar');


// ==========================================
// ORDENHA E LEITE — página própria com os cartões/gráficos de produção de
// leite e partos que antes ficavam misturados no Dashboard.
// ==========================================
Route::get('/ordenha-leite', [OrdenhaLeiteController::class, 'listar'])
    ->name('ordenha.listar');

// CONSULTAR MAIS ORDENHAS (restante do histórico "Últimas Ordenhas
// Registradas" via AJAX) — mesmo padrão do "Consultar Lotes"/"Consultar
// mais Conversões" do Plano Alimentar.
Route::get('/ordenha-leite/historico', [OrdenhaLeiteController::class, 'historicoJson'])
    ->name('ordenha.historicoJson');


// LISTAR
Route::get('/cocho/listar', [CochoController::class, 'listar'])
->name('cocho.listar');

// CADASTRAR
Route::get('/cocho/cadastrar', [CochoController::class, 'cadastrar'])
->name('cocho.cadastrar');

// ADICIONAR
Route::post('/cocho/add', [CochoController::class, 'add'])
    ->name('cocho.add');

// ATUALIZAR
Route::get('/cocho/atualizar/{id}', [CochoController::class, 'atualizar'])
->name('cocho.atualizar');

// UPDATE
Route::put('/cocho/update/{id}', [CochoController::class, 'update'])
->name('cocho.update');

// DELETE
Route::delete('/cocho/delete/{id}', [CochoController::class, 'deletar'])
->name('cocho.delete');



// LISTAR
Route::get('/relatorios', [RelatorioController::class, 'listar'])
->name('relatorio.listar');

// CADASTRAR
Route::get('/relatorios/cadastrar', [RelatorioController::class, 'cadastrar'])
->name('relatorio.cadastrar');

// SALVAR
Route::post('/relatorios/salvar', [RelatorioController::class, 'salvar'])
->name('relatorio.salvar');

// ATUALIZAR
Route::get('/relatorios/atualizar/{id}', [RelatorioController::class, 'atualizar'])
->name('relatorio.atualizar');

// UPDATE
Route::put('/relatorios/update/{id}', [RelatorioController::class, 'update'])
->name('relatorio.update');

// DELETE
Route::delete('/relatorios/delete/{id}', [RelatorioController::class, 'delete'])
->name('relatorio.delete');

// CONSULTAR MAIS DO RANKING (restante da tabela "Ranking de Lotes por
// Desempenho" via AJAX) — mesmo padrão do "Consultar Lotes"/"Consultar mais
// Conversões" do Plano Alimentar.
Route::get('/relatorios/ranking', [RelatorioController::class, 'rankingJson'])
->name('relatorio.rankingJson');



Route::get('/relatorio-pdf', [RelatorioController::class, 'exportarPDF'])
    ->name('relatorio.pdf');

// PAINEL DE ACESSIBILIDADE: widget presente em toda página com navbar (ver
// partials/acessibilidade). Sem login, o controller devolve
// autenticado=false e o front-end continua só com localStorage — por isso
// nenhuma das duas rotas exige sessão para responder.
Route::get('/acessibilidade', [AcessibilidadeController::class, 'show'])
    ->name('acessibilidade.show');

Route::post('/acessibilidade', [AcessibilidadeController::class, 'store'])
    ->name('acessibilidade.store');

// ASSISTENTE IA: widget de chat flutuante presente em todas as telas
// autenticadas. Limitado a 15 perguntas por minuto por IP — cada uma
// dispara uma chamada de API paga, então precisa de alguma barreira
// contra abuso além do custo natural de digitar uma pergunta.
Route::post('/assistente-ia/perguntar', [AssistenteIaController::class, 'perguntar'])
    ->middleware('throttle:15,1')
    ->name('assistenteIa.perguntar');

    // ==========================================
// ROTAS DE RECUPERAÇÃO DE SENHA (por código de verificação)
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

// Rotas do GOOGLE
Route::get('/auth/google', [GoogleController::class, 'redirect'])
->name('google.login');

Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
