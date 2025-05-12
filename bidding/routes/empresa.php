<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Empresa\EmpresaController;
use App\Http\Controllers\Empresa\UsuarioController;
use App\Http\Controllers\Empresa\RelatorioController;
use App\Http\Controllers\Empresa\SegmentoController;

// Dashboard da empresa
Route::get('/', [EmpresaController::class, 'show'])->name('show');
Route::put('/', [EmpresaController::class, 'update'])->name('update');

// Gerenciamento de usuários da empresa
Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/{user}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
Route::put('/usuarios/{user}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
Route::post('/usuarios/{user}/ativar', [UsuarioController::class, 'ativar'])->name('usuarios.ativar');
Route::post('/usuarios/{user}/desativar', [UsuarioController::class, 'desativar'])->name('usuarios.desativar');

// Gerenciamento de segmentos da empresa
Route::get('/segmentos', [SegmentoController::class, 'index'])->name('segmentos');
Route::get('/segmentos/create', [SegmentoController::class, 'create'])->name('segmentos.create');
Route::post('/segmentos', [SegmentoController::class, 'store'])->name('segmentos.store');
Route::get('/segmentos/{segmento}/edit', [SegmentoController::class, 'edit'])->name('segmentos.edit');
Route::put('/segmentos/{segmento}', [SegmentoController::class, 'update'])->name('segmentos.update');
Route::delete('/segmentos/{segmento}', [SegmentoController::class, 'destroy'])->name('segmentos.destroy');

// Relatórios da empresa
Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios');
Route::get('/relatorios/licitacoes', [RelatorioController::class, 'licitacoes'])->name('relatorios.licitacoes');
Route::get('/relatorios/propostas', [RelatorioController::class, 'propostas'])->name('relatorios.propostas');
Route::get('/relatorios/atividade', [RelatorioController::class, 'atividade'])->name('relatorios.atividade');
Route::get('/relatorios/download/{tipo}', [RelatorioController::class, 'download'])->name('relatorios.download');

// Configurações da licença
Route::get('/licenca', [EmpresaController::class, 'licenca'])->name('licenca');
Route::get('/licenca/upgrade', [EmpresaController::class, 'upgradeOptions'])->name('licenca.upgrade');
Route::post('/licenca/upgrade', [EmpresaController::class, 'requestUpgrade'])->name('licenca.upgrade.request');
