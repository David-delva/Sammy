<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AnneeAcademique;
use App\Models\Facture;
use App\Models\Inscription;
use App\Models\User;
use App\Services\AcademicWriteAccessService;

final class FacturePolicy
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

    public function view(User $user, Facture $facture): bool
    {
        return $this->viewAny($user);
    }

    public function viewPdf(User $user, Facture $facture): bool
    {
        return $this->view($user, $facture);
    }

    public function create(User $user): bool
    {
        return $this->academicWriteAccess->canManageSelectedYear($user);
    }

    public function updateStatus(User $user, Facture $facture): bool
    {
        return $this->canManageInvoiceYear($user, $facture);
    }

    public function storePayment(User $user, Facture $facture): bool
    {
        return $this->canManageInvoiceYear($user, $facture);
    }

    public function deletePayment(User $user, Facture $facture): bool
    {
        return $this->canManageInvoiceYear($user, $facture);
    }

    private function canManageInvoiceYear(User $user, Facture $facture): bool
    {
        if (! $user->isSecretariat()) {
            return false;
        }

        /** @var Inscription|null $inscription */
        $inscription = $facture->inscription;
        /** @var AnneeAcademique|null $annee */
        $annee = $inscription?->anneeAcademique;

        return $this->academicWriteAccess->canManageYear($user, $annee);
    }
}
