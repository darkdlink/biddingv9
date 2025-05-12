<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Grupo;
use App\Models\Licitacao;
use App\Models\Proposta;
use App\Models\Segmento;
use App\Models\LicencaUsuario;
use App\Models\LicencaPlano;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Define suas políticas aqui, se necessário
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Permissão para acesso à área administrativa
        Gate::define('access-admin', function (User $user) {
            return $user->isAdmin();
        });

        // Permissão para gerenciar empresa
        Gate::define('manage-empresa', function (User $user) {
            return $user->isUsuarioMaster();
        });

        // Permissão para gerenciar grupo empresarial
        Gate::define('manage-grupo', function (User $user) {
            return $user->isAdminGrupo();
        });

        // Permissão para visualizar uma licitação
        Gate::define('view-licitacao', function (User $user, Licitacao $licitacao) {
            // Administradores podem ver todas as licitações
            if ($user->isAdmin()) {
                return true;
            }

            // Verificar se o usuário tem acesso ao recurso 'licitacoes' em seu plano
            if (!$user->hasRecurso('licitacoes')) {
                return false;
            }

            // Verificar se a licitação pertence a algum dos segmentos do usuário
            $segmentosIds = $user->segmentos()->pluck('segmentos.id')->toArray();

            return $licitacao->segmentos()->whereIn('segmento_id', $segmentosIds)->exists() || $licitacao->interesse;
        });

        // Permissão para criar proposta
        Gate::define('create-proposta', function (User $user, Licitacao $licitacao = null) {
            // Verificar se o usuário tem acesso ao recurso 'propostas' em seu plano
            if (!$user->hasRecurso('propostas')) {
                return false;
            }

            // Verificar limite de propostas para planos básicos de pessoa física
            if ($user->isPessoaFisica() && $user->licenca && $user->licenca->plano->tier === 'basico') {
                $limite = 20; // Limite definido para o plano básico
                $totalPropostas = $user->propostas()->count();

                if ($totalPropostas >= $limite) {
                    return false;
                }
            }

            return true;
        });

        // Permissão para gerenciar proposta
        Gate::define('manage-proposta', function (User $user, Proposta $proposta) {
            // Administradores podem gerenciar todas as propostas
            if ($user->isAdmin()) {
                return true;
            }

            // Usuários só podem gerenciar suas próprias propostas
            if ($user->id === $proposta->user_id) {
                return true;
            }

            // Usuários Master podem gerenciar propostas da sua empresa
            if ($user->isUsuarioMaster() && $user->empresa_id) {
                $propostaUser = $proposta->user;
                return $propostaUser && $propostaUser->empresa_id === $user->empresa_id;
            }

            // Admins de Grupo podem gerenciar propostas das empresas do grupo
            if ($user->isAdminGrupo() && $user->empresa && $user->empresa->grupo_id) {
                $propostaUser = $proposta->user;
                if ($propostaUser && $propostaUser->empresa) {
                    return $propostaUser->empresa->grupo_id === $user->empresa->grupo_id;
                }
            }

            return false;
        });

        // Permissão para gerenciar segmentos
        Gate::define('manage-segmento', function (User $user, Segmento $segmento = null) {
            // Administradores podem gerenciar todos os segmentos
            if ($user->isAdmin()) {
                return true;
            }

            // Verificar se o usuário tem licença válida
            if (!$user->licenca || !$user->licenca->isAtiva()) {
                return false;
            }

            // Verificar limite de segmentos pelo plano
            $maxSegmentos = $user->licenca->plano->max_segmentos ?? 1;
            $totalSegmentos = $user->segmentos()->count();

            // Se estamos criando um novo segmento, verificar o limite
            if (!$segmento && $totalSegmentos >= $maxSegmentos) {
                return false;
            }

            // Se estamos editando um segmento existente, verificar se é do usuário
            if ($segmento) {
                if ($user->isPessoaFisica()) {
                    return $segmento->user_id === $user->id;
                } elseif ($user->isUsuarioMaster() || $user->isAdminGrupo()) {
                    return $segmento->empresa_id === $user->empresa_id;
                }
            }

            return true;
        });

        // Permissão para sincronizar licitações
        Gate::define('sincronizar-licitacoes', function (User $user) {
            return $user->isAdmin() || $user->hasRecurso('sincronizacao');
        });

        // Permissão para visualizar relatórios avançados
        Gate::define('view-relatorios-avancados', function (User $user) {
            return $user->hasRecurso('relatorios_avancados');
        });

        // Permissão para exportar dados
        Gate::define('export-data', function (User $user) {
            return $user->hasRecurso('exportacao');
        });

        // Permissão para acessar análise de concorrência
        Gate::define('access-analise-concorrencia', function (User $user) {
            // Apenas disponível em planos avançados
            if (!$user->licenca || !$user->licenca->plano) {
                return false;
            }

            return $user->licenca->plano->tier === 'avancado' && $user->hasRecurso('analise_concorrencia');
        });
    }
}
