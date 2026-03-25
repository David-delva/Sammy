<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\Schema;

class AcademicYearService
{
    /**
     * Retourne l'annee academique correspondant a une date donnee.
     * Fallback sur l'annee active si aucune annee n'existe pour cette date.
     */
    public function forDate(?string $date = null): ?AnneeAcademique
    {
        if (! Schema::hasTable('annee_academiques')) {
            return null;
        }

        $date = $date ?? $this->selectedDate() ?? now()->toDateString();

        return AnneeAcademique::forDate($date, false) ?? $this->activeYear();
    }

    /**
     * Retourne l'annee academique active.
     */
    public function activeYear(): ?AnneeAcademique
    {
        if (! Schema::hasTable('annee_academiques')) {
            return null;
        }

        return AnneeAcademique::query()->where('active', true)->first();
    }

    /**
     * Verifie si l'annee selectionnee correspond a l'annee issue du calendrier actuel.
     */
    public function isCurrentYear(): bool
    {
        $selected = $this->forDate();
        $realCurrentLabel = AnneeAcademique::labelForDate(now()->toDateString());

        return $selected && $selected->libelle === $realCurrentLabel;
    }

    /**
     * Retourne la date de reference (Y-m-d).
     */
    public function referenceDate(): string
    {
        return $this->selectedDate() ?? now()->toDateString();
    }

    protected function selectedDate(): ?string
    {
        $date = request()->query('date');

        if (filled($date)) {
            return $date;
        }

        $sessionDate = session('academic_year_date');

        return filled($sessionDate) ? $sessionDate : null;
    }
}