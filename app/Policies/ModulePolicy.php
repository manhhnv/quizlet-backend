<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function view(User $user, Module $module)
    {
        return (
            $module->public == Module::PUBLIC_FLAG ||
            ($user && (
                    $user->id == $module->user_id || $user->hasPermission('view_module')
                ))
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function update(User $user, Module $module)
    {
        return (
            $user->id === $module->user_id || $user->hasPermission('update_module')
        );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function delete(User $user, Module $module)
    {
        return (
            $user->id == $module->user_id || $user->hasPermission('delete_module')
        );
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function restore(User $user, Module $module)
    {
        return (
            $user->id == $module->user_id
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function forceDelete(User $user, Module $module)
    {
        return (
            $user->id == $module->user_id
        );
    }
}
