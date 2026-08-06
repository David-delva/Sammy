<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Eleve;
use App\Models\User;
use App\Services\AcademicWriteAccessService;

final class ElevePolicy
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

    public function view(User $user, Eleve $eleve): bool
    {
        return $this->viewAny($user);
    }

    public function viewHistory(User $user, Eleve $eleve): bool
    {
        return $this->view($user, $eleve);
    }

    public function create(User $user): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }

    public function update(User $user, Eleve $eleve): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }

    public function delete(User $user, Eleve $eleve): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }
}
