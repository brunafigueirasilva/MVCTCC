<?php

use App\Http\Controllers\AcessibilidadeController;
use App\Http\Controllers\Api\ApoioController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EstoqueApiController;
use App\Http\Controllers\Api\IotController;
use App\Http\Controllers\Api\LegadoController;
use App\Http\Controllers\Api\PlanoAlimentarController;
use App\Http\Controllers\Api\RecursoController;
use App\Http\Controllers\Api\RelatorioApiController;
use App\Http\Controllers\AssistenteIaController;
use App\Http\Controllers\CadastroController;
use App\Http\Controllers\CochoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\FazendaController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\OrdenhaLeiteController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\RacaoController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST (prefixo /api) — autenticação por token Bearer (Laravel Sanctum)
|--------------------------------------------------------------------------
| Fluxo: POST /api/auth/login devolve {token}; enviar nas demais chamadas
| "Authorization: Bearer <token>". Toda resposta é JSON no formato
| {success, message, data} (erros: {success: false, message, errors}) —
| ver App\Http\Middleware\PadronizarRespostaApi.
|
| A maioria das rotas delega ao LegadoController, que executa os Controllers
| web (mesma validação e regra de negócio) e traduz o resultado para JSON —
| ver App\Http\Controllers\Api\LegadoController. O default "alvo" indica
| [Controller, método]; "recurso" faz a resposta trazer o registro afetado.
| Uploads (ex.: foto do perfil) devem ir em multipart via POST.
*/

/** Atalho: rota que delega a um método de Controller web. */
$legado = function (string $metodoHttp, string $uri, string $controller, string $acao, ?string $recurso = null) {
    $rota = Route::match([$metodoHttp], $uri, LegadoController::class)->defaults('alvo', [$controller, $acao]);

    return $recurso ? $rota->defaults('recurso', $recurso) : $rota;
};

// ------------------------------------------------------------------
// Públicas
// ------------------------------------------------------------------
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
// Limite contra tentativa de senha em massa (o login web já tinha: 6/min).
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/auth/google', [AuthController::class, 'google'])->middleware('throttle:10,1');

Route::middleware(['throttle:10,1', 'api.legado'])->group(function () use ($legado) {
    // Recuperação de senha por código enviado por e-mail.
    $legado('post', '/auth/esqueceu-senha', CadastroController::class, 'enviarCodigoSenha');
    $legado('post', '/auth/redefinir-senha', CadastroController::class, 'redefinirSenha');
});

// ------------------------------------------------------------------
// IoT — controlador dos sensores (ESP32). ABERTA (sem chave): ambiente
// escolar, rede local — qualquer dispositivo da rede pode enviar leituras.
// Para exigir a chave X-IoT-Key de novo, volte o middleware
// 'iot.dispositivo' (App\Http\Middleware\AutenticarDispositivoIot).
// A validação dos dados (cocho, sensor cadastrado, valores) continua.
// ------------------------------------------------------------------
Route::post('/iot/leitura', [IotController::class, 'leitura'])->middleware('throttle:iot');

Route::middleware('api.legado')->group(function () use ($legado) {
    // Avaliações da plataforma são públicas (autor opcional).
    $legado('get', '/avaliacoes', ComentarioController::class, 'listar');
    $legado('post', '/avaliacoes', ComentarioController::class, 'salvar');
    $legado('get', '/acessibilidade', AcessibilidadeController::class, 'show');
});

// ------------------------------------------------------------------
// Autenticadas
// ------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'api.legado'])->group(function () use ($legado) {
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/opcoes', [ApoioController::class, 'opcoes']);

    // Perfil
    $legado('get', '/perfil', CadastroController::class, 'perfil');
    $legado('put', '/perfil', CadastroController::class, 'atualizarPerfil');
    $legado('post', '/perfil', CadastroController::class, 'atualizarPerfil'); // multipart (foto)
    $legado('post', '/perfil/confirmar-senha', CadastroController::class, 'confirmarSenha');
    $legado('post', '/perfil/verificar-email/enviar', CadastroController::class, 'enviarCodigoVerificacaoEmail');
    $legado('post', '/perfil/verificar-email', CadastroController::class, 'confirmarVerificacaoEmail');

    // Dashboard e alertas (?fazenda_id=). /dashboard devolve todos os dados
    // da tela web; /dashboard/resumo, só os indicadores (formato enxuto).
    $legado('get', '/dashboard', CadastroController::class, 'dashboard');
    Route::get('/dashboard/resumo', [RelatorioApiController::class, 'resumo']);
    $legado('get', '/alertas/resumo', CadastroController::class, 'alertasResumo');

    // Fazendas
    Route::get('/fazendas', [RecursoController::class, 'fazendasIndex']);
    Route::get('/fazendas/{id}', [RecursoController::class, 'fazendasShow'])->whereNumber('id');
    $legado('post', '/fazendas', FazendaController::class, 'add', 'fazenda');
    $legado('put', '/fazendas/{id}', FazendaController::class, 'update', 'fazenda');
    $legado('delete', '/fazendas/{id}', FazendaController::class, 'deletar');

    // Lotes
    Route::get('/lotes', [RecursoController::class, 'lotesIndex']);
    // Gráfico "Animais por Lote" (?fazenda_id=): nome, quantidade, total e %.
    Route::get('/lotes/animais-por-lote', [RecursoController::class, 'lotesAnimaisPorLote']);
    Route::get('/lotes/{id}', [RecursoController::class, 'lotesShow'])->whereNumber('id');
    $legado('post', '/lotes', LoteController::class, 'add', 'lote');
    $legado('put', '/lotes/{id}', LoteController::class, 'update', 'lote');
    $legado('delete', '/lotes/{id}', LoteController::class, 'deletar');
    Route::get('/lotes/{id}/ocorrencias', [RecursoController::class, 'lotesOcorrencias']);
    Route::get('/lotes/{id}/ordenhas', [RecursoController::class, 'lotesOrdenhas']);
    // Só as rações que podem ser vinculadas a este lote (tipo, sexo,
    // finalidade, fazenda e status) — ver App\Support\CompatibilidadeRacao.
    Route::get('/lotes/{id}/racoes-compativeis', [RecursoController::class, 'lotesRacoesCompativeis'])->whereNumber('id');
    $legado('post', '/lotes/{id}/morte', LoteController::class, 'registrarMorte', 'lote');
    $legado('post', '/lotes/{id}/doenca', LoteController::class, 'registrarDoenca', 'lote');
    $legado('post', '/lotes/{id}/recuperacao', LoteController::class, 'registrarRecuperacao', 'lote');
    $legado('post', '/lotes/{id}/parto', LoteController::class, 'registrarParto', 'lote');
    $legado('post', '/lotes/{id}/eclosao', LoteController::class, 'registrarEclosao', 'lote');
    $legado('post', '/lotes/{id}/postura', LoteController::class, 'registrarPostura', 'lote');
    $legado('post', '/lotes/{id}/ordenhas', LoteController::class, 'registrarOrdenha', 'lote');
    $legado('put', '/lotes/{id}/ordenhas/{ordenhaId}', LoteController::class, 'atualizarOrdenha', 'lote');
    $legado('delete', '/lotes/{id}/ordenhas/{ordenhaId}', LoteController::class, 'excluirOrdenha', 'lote');

    // Cochos (criados/removidos automaticamente pela quantidade_cochos do
    // lote — não existe "adicionar cocho" manual). ?fazenda_id=&lote_id=&status=&alerta=1
    Route::get('/cochos', [RecursoController::class, 'cochosIndex']);
    Route::get('/cochos/{id}', [RecursoController::class, 'cochosShow'])->whereNumber('id');
    Route::get('/cochos/{id}/status', [RecursoController::class, 'cochosStatus'])->whereNumber('id');
    // Monitoramento IoT: situação atual + indicadores, e histórico de
    // leituras para os gráficos (?periodo=hoje|7d|30d).
    Route::get('/cochos/{id}/iot', [IotController::class, 'cocho'])->whereNumber('id');
    Route::get('/cochos/{id}/iot/historico', [IotController::class, 'historico'])->whereNumber('id');
    $legado('put', '/cochos/{id}', CochoController::class, 'update', 'cocho');
    $legado('post', '/cochos/{id}/abastecer', CochoController::class, 'abastecer', 'cocho');
    $legado('post', '/cochos/{id}/consumo', CochoController::class, 'registrarConsumo', 'cocho');
    $legado('post', '/lotes/{id}/abastecer-cochos', CochoController::class, 'abastecerLote', 'lote');

    // Rações
    Route::get('/racoes', [RecursoController::class, 'racoesIndex']);
    Route::get('/racoes/catalogo', [RecursoController::class, 'racoesCatalogo']);
    Route::get('/racoes/{id}', [RecursoController::class, 'racoesShow'])->whereNumber('id');
    $legado('post', '/racoes', RacaoController::class, 'add', 'racao');
    $legado('put', '/racoes/{id}', RacaoController::class, 'update', 'racao');
    $legado('delete', '/racoes/{id}', RacaoController::class, 'deletar');

    // Estoque: consulta, entrada, saída e histórico (?fazenda_id=)
    Route::get('/estoque', [EstoqueApiController::class, 'index']);
    Route::get('/estoque/movimentacoes', [EstoqueApiController::class, 'movimentacoes']);
    Route::get('/estoque/{racaoId}', [EstoqueApiController::class, 'show'])->whereNumber('racaoId');
    Route::post('/estoque/entrada', [EstoqueApiController::class, 'entrada']);
    Route::post('/estoque/saida', [EstoqueApiController::class, 'saida']);

    // Plano alimentar: CRUD (?fazenda_id=&lote_id=&ativo=) + rotas da tela web
    Route::apiResource('planos-alimentares', PlanoAlimentarController::class)
        ->parameters(['planos-alimentares' => 'plano'])
        ->where(['plano' => '[0-9]+']);
    $legado('get', '/plano', PlanoController::class, 'listar');
    $legado('post', '/plano/lotes/{loteId}/vincular-racao', PlanoController::class, 'vincularRacao');
    $legado('post', '/plano/lotes/{loteId}/pesagem', PlanoController::class, 'registrarPesagem');

    // Ordenha e leite (?fazenda_id=&data_inicio=&data_fim=)
    $legado('get', '/ordenha-leite', OrdenhaLeiteController::class, 'listar');

    // Relatórios (?fazenda_id=&data_inicio=&data_fim=)
    $legado('get', '/relatorios', RelatorioController::class, 'listar');
    $legado('get', '/relatorios/pdf', RelatorioController::class, 'exportarPDF');
    Route::get('/relatorios/resumo', [RelatorioApiController::class, 'resumo']);
    Route::get('/relatorios/consumo', [RelatorioApiController::class, 'consumo']);
    Route::get('/relatorios/estoque', [RelatorioApiController::class, 'estoque']);
    Route::get('/relatorios/lotes', [RelatorioApiController::class, 'lotes']);
    Route::get('/relatorios/cochos', [RelatorioApiController::class, 'cochos']);
    Route::get('/relatorios/producao', [RelatorioApiController::class, 'producao']);

    // Acessibilidade (preferências sincronizadas por usuário)
    $legado('put', '/acessibilidade', AcessibilidadeController::class, 'store');

    // Assistente de IA
    Route::post('/assistente-ia/perguntar', [AssistenteIaController::class, 'perguntar']);
});
