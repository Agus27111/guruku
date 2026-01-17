<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StudentDevelopment;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentDevelopmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentDevelopment');
    }

    public function view(AuthUser $authUser, StudentDevelopment $studentDevelopment): bool
    {
        return $authUser->can('View:StudentDevelopment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentDevelopment');
    }

    public function update(AuthUser $authUser, StudentDevelopment $studentDevelopment): bool
    {
        return $authUser->can('Update:StudentDevelopment');
    }

    public function delete(AuthUser $authUser, StudentDevelopment $studentDevelopment): bool
    {
        return $authUser->can('Delete:StudentDevelopment');
    }

    public function restore(AuthUser $authUser, StudentDevelopment $studentDevelopment): bool
    {
        return $authUser->can('Restore:StudentDevelopment');
    }

    public function forceDelete(AuthUser $authUser, StudentDevelopment $studentDevelopment): bool
    {
        return $authUser->can('ForceDelete:StudentDevelopment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentDevelopment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentDevelopment');
    }

    public function replicate(AuthUser $authUser, StudentDevelopment $studentDevelopment): bool
    {
        return $authUser->can('Replicate:StudentDevelopment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentDevelopment');
    }

}