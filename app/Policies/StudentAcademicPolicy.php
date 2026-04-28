<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StudentAcademic;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentAcademicPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentAcademic');
    }

    public function view(AuthUser $authUser, StudentAcademic $studentAcademic): bool
    {
        return $authUser->can('View:StudentAcademic');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentAcademic');
    }

    public function update(AuthUser $authUser, StudentAcademic $studentAcademic): bool
    {
        return $authUser->can('Update:StudentAcademic');
    }

    public function delete(AuthUser $authUser, StudentAcademic $studentAcademic): bool
    {
        return $authUser->can('Delete:StudentAcademic');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentAcademic');
    }

    public function restore(AuthUser $authUser, StudentAcademic $studentAcademic): bool
    {
        return $authUser->can('Restore:StudentAcademic');
    }

    public function forceDelete(AuthUser $authUser, StudentAcademic $studentAcademic): bool
    {
        return $authUser->can('ForceDelete:StudentAcademic');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentAcademic');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentAcademic');
    }

    public function replicate(AuthUser $authUser, StudentAcademic $studentAcademic): bool
    {
        return $authUser->can('Replicate:StudentAcademic');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentAcademic');
    }

}