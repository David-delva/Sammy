<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AnneeAcademique;
use App\Models\User;

final class AnneeAcademiquePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AnneeAcademique $annee): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, AnneeAcademique $annee): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, AnneeAcademique $annee): bool
    {
        return $user->isAdmin();
    }

    public function grantWriteAccess(User $user, AnneeAcademique $annee): bool
    {
        return $user->isAdmin();
    }

    public function revokeWriteAccess(User $user, AnneeAcademique $annee): bool
    {
        return $user->isAdmin();
    }
}
