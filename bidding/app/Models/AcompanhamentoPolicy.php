<?php

namespace App\Policies;

use App\Models\Acompanhamento;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcompanhamentoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true; // Todos os usuários podem ver acompanhamentos
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Acompanhamento $acompanhamento)
    {
        // O usuário pode ver seus próprios acompanhamentos
        if ($acompanhamento->user_id === $user->id) {
            return true;
        }

        // Admin pode ver qualquer acompanhamento
        if ($user->isAdmin()) {
            return true;
        }

        // Acompanhamentos públicos podem ser vistos por usuários da mesma empresa
        if ($acompanhamento->is_public && $user->empresa_id) {
            $acompanhamentoUser = $acompanhamento->user;

            if ($acompanhamentoUser && $acompanhamentoUser->empresa_id === $user->empresa_id) {
                return true;
            }

            // Usuários do mesmo grupo empresarial também podem ver
            if ($user->empresa && $user->empresa->grupo_id) {
                if ($acompanhamentoUser && $acompanhamentoUser->empresa &&
                    $acompanhamentoUser->empresa->grupo_id === $user->empresa->grupo_id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return true; // Todos os usuários podem criar acompanhamentos
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Acompanhamento $acompanhamento)
    {
        // Apenas o criador do acompanhamento pode editá-lo
        return $acompanhamento->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Acompanhamento $acompanhamento)
    {
        // O usuário pode excluir seus próprios acompanhamentos
        if ($acompanhamento->user_id === $user->id) {
            return true;
        }

        // Admin pode excluir qualquer acompanhamento
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
