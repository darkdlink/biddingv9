<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LicitacaoController;
use App\Http\Controllers\Api\PropostaController;
use App\Http\Controllers\Api\SegmentoController;

// Rotas de autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rotas protegidas por token
Route::middleware('auth:sanctum')->group(function () {
    // Usuário atual
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Licitações
    Route::apiResource('licitacoes', LicitacaoController::class);
    Route::post('/licitacoes/sincronizar', [LicitacaoController::class, 'sincronizar'])
        ->middleware('can:sincronizar-licitacoes');
    Route::post('/licitacoes/{licitacao}/interesse', [LicitacaoController::class, 'marcarInteresse']);

    // Propostas
    Route::apiResource('propostas', PropostaController::class);

    // Segmentos
    Route::apiResource('segmentos', SegmentoController::class)
        ->middleware('can:manage-segmento');

    // Estatísticas do Dashboard
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);

    // Verificar se o usuário tem determinado recurso em seu plano
    Route::get('/check-recurso/{recursoNome}', function (Request $request, $recursoNome) {
        return ['has_recurso' => $request->user()->hasRecurso($recursoNome)];
    });
});
