<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\LicencaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\PlanoController;
use App\Http\Controllers\Admin\RecursoController;
use App\Http\Controllers\Admin\RelatorioController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\SincronizacaoController;

// Dashboard Administrativo
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/download', [AdminDashboardController::class, 'downloadRelatorio'])->name('dashboard.download');

// Gerenciamento de licenças
Route::resource('licencas', LicencaController::class);
Route::post('/licencas/{licenca}/renovar', [LicencaController::class, 'renovar'])->name('licencas.renovar');
Route::post('/licencas/{licenca}/cancelar', [LicencaController::class, 'cancelar'])->name('licencas.cancelar');
Route::get('/licencas/{licenca}/historico', [LicencaController::class, 'historico'])->name('licencas.historico');

// Gerenciamento de usuários
Route::resource('usuarios', UsuarioController::class);
Route::post('/usuarios/{usuario}/ativar', [UsuarioController::class, 'ativar'])->name('usuarios.ativar');
Route::post('/usuarios/{usuario}/desativar', [UsuarioController::class, 'desativar'])->name('usuarios.desativar');
Route::get('/usuarios/{usuario}/licencas', [UsuarioController::class, 'licencas'])->name('usuarios.licencas');

// Gerenciamento de empresas
Route::resource('empresas', EmpresaController::class);
Route::get('/empresas/{empresa}/usuarios', [EmpresaController::class, 'usuarios'])->name('empresas.usuarios');
Route::get('/empresas/{empresa}/licencas', [EmpresaController::class, 'licencas'])->name('empresas.licencas');

// Gerenciamento de grupos empresariais
Route::resource('grupos', GrupoController::class);
Route::get('/grupos/{grupo}/empresas', [GrupoController::class, 'empresas'])->name('grupos.empresas');
Route::get('/grupos/{grupo}/usuarios', [GrupoController::class, 'usuarios'])->name('grupos.usuarios');

// Gerenciamento de planos
Route::resource('planos', PlanoController::class);
Route::post('/planos/{plano}/recursos', [PlanoController::class, 'atualizarRecursos'])->name('planos.recursos.atualizar');

// Gerenciamento de recursos
Route::resource('recursos', RecursoController::class);

// Relatórios administrativos
Route::get('/relatorios/financeiro', [RelatorioController::class, 'financeiro'])->name('relatorios.financeiro');
Route::get('/relatorios/usuarios', [RelatorioController::class, 'usuarios'])->name('relatorios.usuarios');
Route::get('/relatorios/licencas', [RelatorioController::class, 'licencas'])->name('relatorios.licencas');
Route::get('/relatorios/atividade', [RelatorioController::class, 'atividade'])->name('relatorios.atividade');
Route::get('/relatorios/download/{tipo}', [RelatorioController::class, 'download'])->name('relatorios.download');

// Configurações do sistema
Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes');
Route::post('/configuracoes', [ConfiguracaoController::class, 'salvar'])->name('configuracoes.salvar');
Route::get('/configuracoes/sistema', [ConfiguracaoController::class, 'sistema'])->name('configuracoes.sistema');
Route::post('/configuracoes/sistema', [ConfiguracaoController::class, 'salvarSistema'])->name('configuracoes.sistema.salvar');
Route::get('/configuracoes/email', [ConfiguracaoController::class, 'email'])->name('configuracoes.email');
Route::post('/configuracoes/email', [ConfiguracaoController::class, 'salvarEmail'])->name('configuracoes.email.salvar');
Route::post('/configuracoes/email/testar', [ConfiguracaoController::class, 'testarEmail'])->name('configuracoes.email.testar');

// Sincronização de licitações
Route::get('/sincronizacao', [SincronizacaoController::class, 'index'])->name('sincronizacao');
Route::post('/sincronizacao', [SincronizacaoController::class, 'executar'])->name('sincronizacao.executar');
Route::get('/sincronizacao/logs', [SincronizacaoController::class, 'logs'])->name('sincronizacao.logs');
Route::get('/sincronizacao/logs/{id}', [SincronizacaoController::class, 'showLog'])->name('sincronizacao.logs.show');
Route::get('/sincronizacao/config', [SincronizacaoController::class, 'config'])->name('sincronizacao.config');
Route::post('/sincronizacao/config', [SincronizacaoController::class, 'salvarConfig'])->name('sincronizacao.config.salvar');
