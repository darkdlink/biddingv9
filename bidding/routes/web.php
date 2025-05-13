<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicitacaoController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SegmentoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Página inicial (landing page)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rotas de autenticação (necessárias para resolver o erro Route [login] not defined)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Toggle do estado da sidebar
Route::post('/sidebar/toggle', function () {
    session(['sidebar_collapsed' => !session('sidebar_collapsed', false)]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('sidebar.toggle');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    // Dashboard do usuário
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas para licitações
    Route::prefix('licitacoes')->group(function () {
        Route::get('/', [LicitacaoController::class, 'index'])->name('licitacoes.index');
        Route::get('/{licitacao}', [LicitacaoController::class, 'show'])->name('licitacoes.show');
        Route::post('/sincronizar', [LicitacaoController::class, 'sincronizar'])
            ->middleware('can:sincronizar-licitacoes')
            ->name('licitacoes.sincronizar');
        Route::post('/{licitacao}/interesse', [LicitacaoController::class, 'marcarInteresse'])->name('licitacoes.interesse');
        Route::post('/{licitacao}/analisada', [LicitacaoController::class, 'marcarAnalisada'])->name('licitacoes.analisada');
    });

    // Rotas para propostas
    Route::prefix('propostas')->group(function () {
        Route::get('/', [PropostaController::class, 'index'])->name('propostas.index');
        Route::get('/create/{licitacao?}', [PropostaController::class, 'create'])
            ->middleware('can:create-proposta,licitacao')
            ->name('propostas.create');
        Route::post('/', [PropostaController::class, 'store'])->name('propostas.store');
        Route::get('/{proposta}', [PropostaController::class, 'show'])
            ->middleware('can:manage-proposta,proposta')
            ->name('propostas.show');
        Route::get('/{proposta}/edit', [PropostaController::class, 'edit'])
            ->middleware('can:manage-proposta,proposta')
            ->name('propostas.edit');
        Route::put('/{proposta}', [PropostaController::class, 'update'])
            ->middleware('can:manage-proposta,proposta')
            ->name('propostas.update');
        Route::delete('/{proposta}', [PropostaController::class, 'destroy'])
            ->middleware('can:manage-proposta,proposta')
            ->name('propostas.destroy');
    });

    // Rotas para perfil do usuário
    Route::prefix('perfil')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('perfil.show');
        Route::put('/', [UserController::class, 'updateProfile'])->name('perfil.update');
        Route::get('/senha', [UserController::class, 'editPassword'])->name('perfil.senha');
        Route::put('/senha', [UserController::class, 'updatePassword'])->name('perfil.senha.update');
    });

    // Rota para segmentos
    Route::resource('segmentos', SegmentoController::class)->middleware('can:manage-segmento');
});


