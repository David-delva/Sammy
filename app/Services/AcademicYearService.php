<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AcademicYearService
{
    /**
     * Retourne l'année académique correspondant à une date donnée.
     * Fallback sur l'année active si aucune date n'est fournie ou trouvée.
     */
    public function forDate(?string $date = null): ?AnneeAcademique
    {
        $date = $date ?? session('academic_year_date') ?? now()->toDateString();
        $label = AnneeAcademique::labelForDate($date);

        return Cache::remember("academic_year_for_{$label}", 300, function () use ($label) {
            return AnneeAcademique::where('libelle', $label)->first() 
                ?? AnneeAcademique::where('active', true)->first();
        });
    }

    /**
     * Vérifie si l'année sélectionnée est l'année académique réellement en cours.
     */
    public function isCurrentYear(): bool
    {
        $selected = $this->forDate();
        $realCurrentLabel = AnneeAcademique::labelForDate(now()->toDateString());

        return $selected && $selected->libelle === $realCurrentLabel;
    }

    /**
     * Retourne la date de référence (Y-m-d).
     */
    public function referenceDate(): string
    {
        return session('academic_year_date') ?? now()->toDateString();
    }
}
