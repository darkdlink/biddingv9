<?php

namespace App\Policies;

use App\Models\Segmento;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SegmentoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true; // Todos os usuários podem ver segmentos
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Segmento $segmento)
    {
        // O usuário pode ver seus próprios segmentos
        if ($segmento->user_id === $user->id) {
            return true;
        }

        // Admin pode ver qualquer segmento
        if ($user->isAdmin()) {
            return true;
        }

        // Usuário de empresa pode ver segmentos da empresa
        if ($user->empresa_id && $segmento->empresa_id === $user->empresa_id) {
            return true;
        }

        // Admin de Grupo pode ver segmentos das empresas do grupo
        if ($user->isAdminGrupo() && $user->empresa_id) {
            $empresa = $user->empresa;

            if ($empresa && $empresa->grupo_id && $segmento->empresa_id) {
                $segmentoEmpresa = $segmento->empresa;

                if ($segmentoEmpresa && $segmentoEmpresa->grupo_id === $empresa->grupo_id) {
                    return true;
                }
            }
        }

        // Verificar se o segmento está associado ao usuário
        if ($user->segmentos()->where('segmento_id', $segmento->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        // Verificar se o usuário tem licença ativa
        $licenca = $user->licenca;

        if (!$licenca || !$licenca->isAtiva()) {
            return false;
        }

        // Verificar se já atingiu o limite de segmentos pelo plano
        $plano = $licenca->plano;

        if ($plano && $plano->max_segmentos) {
            $segmentosCount = $user->segmentos()->count();

            if ($segmentosCount >= $plano->max_segmentos) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Segmento $segmento)
    {
        // O usuário pode editar seus próprios segmentos
        if ($segmento->user_id === $user->id) {
            return true;
        }

        // Admin pode editar qualquer segmento
        if ($user->isAdmin()) {
            return true;
        }

        // Usuário Master pode editar segmentos da empresa
        if ($user->isUsuarioMaster() && $user->empresa_id && $segmento->empresa_id === $user->empresa_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Segmento $segmento)
    {
        // O usuário pode excluir seus próprios segmentos
        if ($segmento->user_id === $user->id) {
            return true;
        }

        // Admin pode excluir qualquer segmento
        if ($user->isAdmin()) {
            return true;
        }

        // Usuário Master pode excluir segmentos da empresa
        if ($user->isUsuarioMaster() && $user->empresa_id && $segmento->empresa_id === $user->empresa_id) {
            return true;
        }

        return false;
    }
}
