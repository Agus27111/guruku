<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Read;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Read');
    }

    public function view(AuthUser $authUser, Read $read): bool
    {
        return $authUser->can('View:Read');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Read');
    }

    public function update(AuthUser $authUser, Read $read): bool
    {
        return $authUser->can('Update:Read');
    }

    public function delete(AuthUser $authUser, Read $read): bool
    {
        return $authUser->can('Delete:Read');
    }

    public function restore(AuthUser $authUser, Read $read): bool
    {
        return $authUser->can('Restore:Read');
    }

    public function forceDelete(AuthUser $authUser, Read $read): bool
    {
        return $authUser->can('ForceDelete:Read');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Read');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Read');
    }

    public function replicate(AuthUser $authUser, Read $read): bool
    {
        return $authUser->can('Replicate:Read');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Read');
    }

}