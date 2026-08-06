<?php

declare(strict_types=1);

namespace App\Support\AcademicContext;

use App\Models\AnneeAcademique;
use App\Models\Facture;
use App\Models\Inscription;
use App\Services\AcademicYearService;

final class AcademicContext
{
    public function __construct(
        private readonly AcademicYearService $academicYears,
        private readonly SchoolContext $schoolContext,
    ) {}

    public function currentYear(): ?AnneeAcademique
    {
        return $this->academicYears->forDate();
    }

    public function referenceDate(): string
    {
        return $this->academicYears->referenceDate();
    }

    public function schoolKey(): string
    {
        return $this->schoolContext->schoolKey();
    }

    public function selectedYearMatches(?AnneeAcademique $annee): bool
    {
        $selectedYear = $this->currentYear();

        if (! $selectedYear || ! $annee) {
            return true;
        }

        return (int) $selectedYear->id === (int) $annee->id;
    }

    public function selectedYearMatchesFacture(Facture $facture): bool
    {
        /** @var Inscription|null $inscription */
        $inscription = $facture->inscription;
        /** @var AnneeAcademique|null $annee */
        $annee = $inscription?->anneeAcademique;

        return $this->selectedYearMatches($annee);
    }

    public function mismatchMessageForFacture(Facture $facture): string
    {
        /** @var Inscription|null $inscription */
        $inscription = $facture->inscription;
        /** @var AnneeAcademique|null $annee */
        $annee = $inscription?->anneeAcademique;
        $libelle = $annee?->libelle;

        return $libelle
            ? "Selectionnez d'abord l'annee {$libelle} pour modifier cette facture."
            : "Selectionnez d'abord l'annee academique de cette facture pour la modifier.";
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function routeParameters(array $extra = []): array
    {
        $date = request()->query('date');

        if (! filled($date)) {
            $date = session('academic_year_date');
        }

        $sanitizedDate = $this->academicYears->sanitizeDate($date);

        return array_filter(
            array_merge([
                'date' => $sanitizedDate,
            ], $extra),
            static fn (mixed $value): bool => filled($value),
        );
    }
}
