<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Classe;
use App\Models\User;
use App\Services\AcademicWriteAccessService;

final class ClassePolicy
{
    public function __construct(
        private readonly AcademicWriteAccessService $academicWriteAccess,
    ) {}

    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isSecretariat();
    }

    public function view(User $user, Classe $classe): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }

    public function update(User $user, Classe $classe): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }

    public function delete(User $user, Classe $classe): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }
}
