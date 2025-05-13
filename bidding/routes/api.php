<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LicitacaoController;
use App\Http\Controllers\Api\PropostaController;
use App\Http\Controllers\Api\SegmentoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\GrupoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas de API para o Sistema Bidding.
| As rotas são carregadas pelo RouteServiceProvider e todas elas serão
| atribuídas ao grupo de middleware "api".
|
*/

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Verificar versão da API e status
Route::get('/status', function() {
    return response()->json([
        'status' => 'online',
        'version' => config('app.version', '1.0.0'),
        'environment' => config('app.env'),
        'server_time' => now()->toIso8601String()
    ]);
});

// Rotas protegidas por token
Route::middleware('auth:sanctum')->group(function () {
    // Usuário e autenticação
    Route::get('/user', [UserController::class, 'current']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Verificação de recursos no plano
    Route::get('/check-recurso/{recursoNome}', [UserController::class, 'checkRecurso']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/licitacoes-recentes', [DashboardController::class, 'licitacoesRecentes']);
    Route::get('/dashboard/propostas-recentes', [DashboardController::class, 'propostasRecentes']);
    Route::get('/dashboard/alertas', [DashboardController::class, 'alertas']);

    // Licitações
    Route::prefix('licitacoes')->group(function () {
        Route::get('/', [LicitacaoController::class, 'index']);
        Route::get('/{licitacao}', [LicitacaoController::class, 'show']);
        Route::post('/sincronizar', [LicitacaoController::class, 'sincronizar'])
            ->middleware('can:sincronizar-licitacoes');
        Route::post('/{licitacao}/interesse', [LicitacaoController::class, 'marcarInteresse']);
        Route::post('/{licitacao}/analisada', [LicitacaoController::class, 'marcarAnalisada']);
        Route::get('/export', [LicitacaoController::class, 'export'])
            ->middleware('can:export-licitacoes');
    });

    // Propostas
    Route::apiResource('propostas', PropostaController::class);
    Route::post('/propostas/{proposta}/submeter', [PropostaController::class, 'submeter']);
    Route::post('/propostas/{proposta}/cancelar', [PropostaController::class, 'cancelar']);
    Route::post('/propostas/{proposta}/arquivos', [PropostaController::class, 'uploadArquivo']);
    Route::delete('/propostas/{proposta}/arquivos/{arquivo}', [PropostaController::class, 'deleteArquivo']);

    // Segmentos
    Route::apiResource('segmentos', SegmentoController::class)
        ->middleware('can:manage-segmento');
    Route::get('/segmentos/{segmento}/licitacoes', [SegmentoController::class, 'licitacoes']);
    Route::post('/segmentos/{segmento}/palavras-chave', [SegmentoController::class, 'updatePalavrasChave']);
        // Rota para segmentos
    Route::resource('segmentos', SegmentoController::class)->middleware('can:manage-segmento');


    // Acompanhamentos
    Route::post('/licitacoes/{licitacao}/acompanhamentos', [LicitacaoController::class, 'storeAcompanhamento']);
    Route::put('/acompanhamentos/{acompanhamento}', [LicitacaoController::class, 'updateAcompanhamento']);
    Route::delete('/acompanhamentos/{acompanhamento}', [LicitacaoController::class, 'deleteAcompanhamento']);

    // Alertas
    Route::get('/alertas', [UserController::class, 'alertas']);
    Route::put('/alertas/marcar-como-lido', [UserController::class, 'marcarAlertasComoLidos']);
    Route::put('/alertas/{alerta}/marcar-como-lido', [UserController::class, 'marcarAlertaComoLido']);

    // Empresas (usuário master)
    Route::prefix('empresa')->middleware('can:manage-empresa')->group(function () {
        Route::get('/', [EmpresaController::class, 'show']);
        Route::put('/', [EmpresaController::class, 'update']);
        Route::get('/usuarios', [EmpresaController::class, 'usuarios']);
        Route::post('/usuarios', [EmpresaController::class, 'storeUsuario']);
        Route::put('/usuarios/{usuario}', [EmpresaController::class, 'updateUsuario']);
        Route::delete('/usuarios/{usuario}', [EmpresaController::class, 'deleteUsuario']);
        Route::get('/estatisticas', [EmpresaController::class, 'stats']);
    });

    // Grupos (admin grupo)
    Route::prefix('grupo')->middleware('can:manage-grupo')->group(function () {
        Route::get('/', [GrupoController::class, 'show']);
        Route::put('/', [GrupoController::class, 'update']);
        Route::get('/empresas', [GrupoController::class, 'empresas']);
        Route::post('/empresas', [GrupoController::class, 'storeEmpresa']);
        Route::put('/empresas/{empresa}', [GrupoController::class, 'updateEmpresa']);
        Route::delete('/empresas/{empresa}', [GrupoController::class, 'deleteEmpresa']);
        Route::get('/estatisticas', [GrupoController::class, 'stats']);
    });

    // Rotas apenas para administradores
    Route::middleware('can:admin')->prefix('admin')->group(function () {
        Route::get('/usuarios', [AdminController::class, 'usuarios']);
        Route::get('/usuarios/{usuario}', [AdminController::class, 'showUsuario']);
        Route::put('/usuarios/{usuario}', [AdminController::class, 'updateUsuario']);
        Route::delete('/usuarios/{usuario}', [AdminController::class, 'deleteUsuario']);
        Route::post('/usuarios/{usuario}/toggle-status', [AdminController::class, 'toggleStatusUsuario']);

        Route::get('/empresas', [AdminController::class, 'empresas']);
        Route::get('/empresas/{empresa}', [AdminController::class, 'showEmpresa']);
        Route::put('/empresas/{empresa}', [AdminController::class, 'updateEmpresa']);
        Route::delete('/empresas/{empresa}', [AdminController::class, 'deleteEmpresa']);

        Route::get('/grupos', [AdminController::class, 'grupos']);
        Route::get('/grupos/{grupo}', [AdminController::class, 'showGrupo']);
        Route::put('/grupos/{grupo}', [AdminController::class, 'updateGrupo']);
        Route::delete('/grupos/{grupo}', [AdminController::class, 'deleteGrupo']);

        Route::get('/licencas', [AdminController::class, 'licencas']);
        Route::get('/licencas/{licenca}', [AdminController::class, 'showLicenca']);
        Route::put('/licencas/{licenca}', [AdminController::class, 'updateLicenca']);
        Route::delete('/licencas/{licenca}', [AdminController::class, 'deleteLicenca']);
        Route::post('/licencas/{licenca}/renovar', [AdminController::class, 'renovarLicenca']);
        Route::post('/licencas/{licenca}/cancelar', [AdminController::class, 'cancelarLicenca']);

        Route::get('/planos', [AdminController::class, 'planos']);
        Route::get('/planos/{plano}', [AdminController::class, 'showPlano']);
        Route::put('/planos/{plano}', [AdminController::class, 'updatePlano']);

        Route::get('/estatisticas', [AdminController::class, 'estatisticas']);
        Route::get('/logs', [AdminController::class, 'logs']);
        Route::get('/sistema/configuracoes', [AdminController::class, 'configuracoesSistema']);
        Route::put('/sistema/configuracoes', [AdminController::class, 'atualizarConfiguracoesSistema']);
    });
});

// Fallback para rotas não encontradas
Route::fallback(function(){
    return response()->json([
        'message' => 'Rota não encontrada',
    ], 404);
});
