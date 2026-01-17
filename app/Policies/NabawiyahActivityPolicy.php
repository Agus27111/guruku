<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NabawiyahActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class NabawiyahActivityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NabawiyahActivity');
    }

    public function view(AuthUser $authUser, NabawiyahActivity $nabawiyahActivity): bool
    {
        return $authUser->can('View:NabawiyahActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NabawiyahActivity');
    }

    public function update(AuthUser $authUser, NabawiyahActivity $nabawiyahActivity): bool
    {
        return $authUser->can('Update:NabawiyahActivity');
    }

    public function delete(AuthUser $authUser, NabawiyahActivity $nabawiyahActivity): bool
    {
        return $authUser->can('Delete:NabawiyahActivity');
    }

    public function restore(AuthUser $authUser, NabawiyahActivity $nabawiyahActivity): bool
    {
        return $authUser->can('Restore:NabawiyahActivity');
    }

    public function forceDelete(AuthUser $authUser, NabawiyahActivity $nabawiyahActivity): bool
    {
        return $authUser->can('ForceDelete:NabawiyahActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NabawiyahActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NabawiyahActivity');
    }

    public function replicate(AuthUser $authUser, NabawiyahActivity $nabawiyahActivity): bool
    {
        return $authUser->can('Replicate:NabawiyahActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NabawiyahActivity');
    }

}