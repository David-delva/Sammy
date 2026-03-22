<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AcademicYearService
{
    /**
     * Retourne l'annee academique correspondant a une date donnee.
     * Fallback sur l'annee active si aucune date n'est fournie ou trouvee.
     */
    public function forDate(?string $date = null): ?AnneeAcademique
    {
        if (! Schema::hasTable('annee_academiques')) {
            return null;
        }

        $date = $date ?? request()->query('date') ?? session('academic_year_date') ?? now()->toDateString();
        $label = AnneeAcademique::labelForDate($date);

        return Cache::remember("academic_year_for_{$label}", 300, function () use ($label) {
            return AnneeAcademique::query()->where('libelle', $label)->first()
                ?? AnneeAcademique::query()->where('active', true)->first();
        });
    }

    /**
     * Verifie si l'annee selectionnee est l'annee academique reellement en cours.
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
        return request()->query('date') ?? session('academic_year_date') ?? now()->toDateString();
    }
}