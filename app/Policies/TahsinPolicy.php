<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tahsin;
use Illuminate\Auth\Access\HandlesAuthorization;

class TahsinPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Tahsin');
    }

    public function view(AuthUser $authUser, Tahsin $tahsin): bool
    {
        return $authUser->can('View:Tahsin');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Tahsin');
    }

    public function update(AuthUser $authUser, Tahsin $tahsin): bool
    {
        return $authUser->can('Update:Tahsin');
    }

    public function delete(AuthUser $authUser, Tahsin $tahsin): bool
    {
        return $authUser->can('Delete:Tahsin');
    }

    public function restore(AuthUser $authUser, Tahsin $tahsin): bool
    {
        return $authUser->can('Restore:Tahsin');
    }

    public function forceDelete(AuthUser $authUser, Tahsin $tahsin): bool
    {
        return $authUser->can('ForceDelete:Tahsin');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Tahsin');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Tahsin');
    }

    public function replicate(AuthUser $authUser, Tahsin $tahsin): bool
    {
        return $authUser->can('Replicate:Tahsin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Tahsin');
    }

}