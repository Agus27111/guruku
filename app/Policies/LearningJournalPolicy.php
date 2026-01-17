<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LearningJournal;
use Illuminate\Auth\Access\HandlesAuthorization;

class LearningJournalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LearningJournal');
    }

    public function view(AuthUser $authUser, LearningJournal $learningJournal): bool
    {
        return $authUser->can('View:LearningJournal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LearningJournal');
    }

    public function update(AuthUser $authUser, LearningJournal $learningJournal): bool
    {
        return $authUser->can('Update:LearningJournal');
    }

    public function delete(AuthUser $authUser, LearningJournal $learningJournal): bool
    {
        return $authUser->can('Delete:LearningJournal');
    }

    public function restore(AuthUser $authUser, LearningJournal $learningJournal): bool
    {
        return $authUser->can('Restore:LearningJournal');
    }

    public function forceDelete(AuthUser $authUser, LearningJournal $learningJournal): bool
    {
        return $authUser->can('ForceDelete:LearningJournal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LearningJournal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LearningJournal');
    }

    public function replicate(AuthUser $authUser, LearningJournal $learningJournal): bool
    {
        return $authUser->can('Replicate:LearningJournal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LearningJournal');
    }

}