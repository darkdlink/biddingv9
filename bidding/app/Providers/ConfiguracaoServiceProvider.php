<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuracao;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ConfiguracaoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Tentar carregar as configurações do banco de dados
        try {
            // Verificar se a tabela existe
            if (!\Schema::hasTable('configuracoes')) {
                return;
            }

            // Carregar todas as configurações
            $configuracoes = Configuracao::all();

            foreach ($configuracoes as $config) {
                Config::set('app.' . $config->chave, $config->valor);
            }

        } catch (\Exception $e) {
            // Se ocorrer um erro, registre-o, mas continue a execução do aplicativo
            \Log::error('Erro ao carregar configurações: ' . $e->getMessage());
        }
    }
}
