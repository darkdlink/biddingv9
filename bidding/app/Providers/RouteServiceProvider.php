<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Configurar limites de taxa para API
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Configurar limites de taxa para sincronização de licitações
        RateLimiter::for('sincronizacao', function (Request $request) {
            return Limit::perMinute(1)->by($request->user()?->id ?: $request->ip());
        });

        // Definir padrões globais de rota
        Route::pattern('id', '[0-9]+');
        Route::pattern('slug', '[a-z0-9-]+');

        $this->routes(function () {
            // Rotas de API - Versão 1
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Rotas Web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Rotas Admin
            Route::middleware(['web', 'auth', 'can:access-admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // Rotas de Empresa
            Route::middleware(['web', 'auth', 'can:manage-empresa'])
                ->prefix('empresa')
                ->name('empresa.')
                ->group(base_path('routes/empresa.php'));

            // Rotas de Grupo
            Route::middleware(['web', 'auth', 'can:manage-grupo'])
                ->prefix('grupo')
                ->name('grupo.')
                ->group(base_path('routes/grupo.php'));
        });
    }
}
