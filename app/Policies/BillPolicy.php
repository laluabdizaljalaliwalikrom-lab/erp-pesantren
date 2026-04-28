<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Bill;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillPolicy
{
    use HandlesAuthorization;
    
    public function viewAny($authUser): bool
    {
        if ($authUser instanceof \App\Models\Guardian) {
            return true;
        }
        
        return $authUser->can('ViewAny:Bill');
    }

    public function view(AuthUser $authUser, Bill $bill): bool
    {
        return $authUser->can('View:Bill');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Bill');
    }

    public function update(AuthUser $authUser, Bill $bill): bool
    {
        return $authUser->can('Update:Bill');
    }

    public function delete(AuthUser $authUser, Bill $bill): bool
    {
        return $authUser->can('Delete:Bill');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Bill');
    }

    public function restore(AuthUser $authUser, Bill $bill): bool
    {
        return $authUser->can('Restore:Bill');
    }

    public function forceDelete(AuthUser $authUser, Bill $bill): bool
    {
        return $authUser->can('ForceDelete:Bill');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Bill');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Bill');
    }

    public function replicate(AuthUser $authUser, Bill $bill): bool
    {
        return $authUser->can('Replicate:Bill');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Bill');
    }

}