<?php

namespace App\Policies;

use App\Models\Licitacao;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LicitacaoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true; // Todos os usuários podem ver licitações
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Licitacao $licitacao)
    {
        return true; // Todos os usuários podem ver detalhes de licitações
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Licitacao $licitacao)
    {
        return $user->isAdmin(); // Apenas admin pode editar licitações
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Licitacao $licitacao)
    {
        return $user->isAdmin(); // Apenas admin pode excluir licitações
    }
}
