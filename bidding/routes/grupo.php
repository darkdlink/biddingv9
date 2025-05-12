<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Grupo\GrupoController;
use App\Http\Controllers\Grupo\EmpresaController;
use App\Http\Controllers\Grupo\RelatorioController;
use App\Http\Controllers\Grupo\UsuarioController;

// Dashboard do grupo
Route::get('/', [GrupoController::class, 'show'])->name('show');
Route::put('/', [GrupoController::class, 'update'])->name('update');

// Gerenciamento de empresas do grupo
Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas');
Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
Route::get('/empresas/{empresa}', [EmpresaController::class, 'show'])->name('empresas.show');
Route::get('/empresas/{empresa}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->name('empresas.update');
Route::delete('/empresas/{empresa}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');

// Gerenciamento de usuários do grupo
Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/{user}', [UsuarioController::class, 'show'])->name('usuarios.show');
Route::get('/usuarios/{user}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
Route::put('/usuarios/{user}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

// Relatórios consolidados do grupo
Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios');
Route::get('/relatorios/licitacoes', [RelatorioController::class, 'licitacoes'])->name('relatorios.licitacoes');
Route::get('/relatorios/propostas', [RelatorioController::class, 'propostas'])->name('relatorios.propostas');
Route::get('/relatorios/empresas', [RelatorioController::class, 'empresasComparativo'])->name('relatorios.empresas');
Route::get('/relatorios/atividade', [RelatorioController::class, 'atividade'])->name('relatorios.atividade');
Route::get('/relatorios/download/{tipo}', [RelatorioController::class, 'download'])->name('relatorios.download');

// Licença do grupo
Route::get('/licenca', [GrupoController::class, 'licenca'])->name('licenca');
Route::get('/licenca/upgrade', [GrupoController::class, 'upgradeOptions'])->name('licenca.upgrade');
Route::post('/licenca/upgrade', [GrupoController::class, 'requestUpgrade'])->name('licenca.upgrade.request');
Route::get('/licenca/empresas', [GrupoController::class, 'licencaEmpresas'])->name('licenca.empresas');
Route::post('/licenca/empresas/adicionar', [GrupoController::class, 'adicionarEmpresaLicenca'])->name('licenca.empresas.adicionar');
