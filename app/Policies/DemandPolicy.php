<?php

namespace App\Policies;

use App\Models\Demand;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DemandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return in_array($user->role, ['admin', 'assessor']);
        return $user->is_admin === '1' || in_array($user->role, ['user', 'assessor']);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Demand $demand): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //return $user->role === 'citizen';

        return $user->is_admin === '1' || in_array($user->role, ['user', 'assessor']);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Demand $demand): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Demand $demand): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Demand $demand): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Demand $demand): bool
    {
        //
    }
}
