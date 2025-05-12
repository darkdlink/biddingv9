<?php

namespace App\Policies;

use App\Models\Proposta;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropostaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true; // Todos os usuários podem ver suas propostas
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposta $proposta)
    {
        // O usuário pode ver suas próprias propostas
        if ($proposta->user_id === $user->id) {
            return true;
        }

        // Admin pode ver qualquer proposta
        if ($user->isAdmin()) {
            return true;
        }

        // Usuário Master pode ver propostas da empresa
        if ($user->isUsuarioMaster() && $user->empresa_id) {
            $propostaUser = $proposta->user;

            if ($propostaUser && $propostaUser->empresa_id === $user->empresa_id) {
                return true;
            }
        }

        // Admin de Grupo pode ver propostas do grupo
        if ($user->isAdminGrupo() && $user->empresa_id) {
            $empresa = $user->empresa;

            if ($empresa && $empresa->grupo_id) {
                $propostaUser = $proposta->user;

                if ($propostaUser && $propostaUser->empresa &&
                    $propostaUser->empresa->grupo_id === $empresa->grupo_id) {
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
        // Verificar se o usuário tem acesso ao recurso "criar_proposta"
        return $user->hasRecurso('criar_proposta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposta $proposta)
    {
        // Apenas o criador da proposta pode editá-la
        return $proposta->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proposta $proposta)
    {
        // O usuário pode excluir suas próprias propostas
        if ($proposta->user_id === $user->id) {
            return true;
        }

        // Admin pode excluir qualquer proposta
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
