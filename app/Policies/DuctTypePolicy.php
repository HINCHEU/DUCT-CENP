<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DuctType;
use Illuminate\Auth\Access\HandlesAuthorization;

class DuctTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DuctType');
    }

    public function view(AuthUser $authUser, DuctType $ductType): bool
    {
        return $authUser->can('View:DuctType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DuctType');
    }

    public function update(AuthUser $authUser, DuctType $ductType): bool
    {
        return $authUser->can('Update:DuctType');
    }

    public function delete(AuthUser $authUser, DuctType $ductType): bool
    {
        return $authUser->can('Delete:DuctType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DuctType');
    }

    public function restore(AuthUser $authUser, DuctType $ductType): bool
    {
        return $authUser->can('Restore:DuctType');
    }

    public function forceDelete(AuthUser $authUser, DuctType $ductType): bool
    {
        return $authUser->can('ForceDelete:DuctType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DuctType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DuctType');
    }

    public function replicate(AuthUser $authUser, DuctType $ductType): bool
    {
        return $authUser->can('Replicate:DuctType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DuctType');
    }

}