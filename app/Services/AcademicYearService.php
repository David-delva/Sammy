<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use Carbon\Carbon;
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

        $date = $this->sanitizeDate($date) ?? $this->selectedDate() ?? now()->toDateString();

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

    public function sanitizeDate(mixed $date): ?string
    {
        if (! is_string($date)) {
            return null;
        }

        $date = trim($date);

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Throwable) {
            return null;
        }

        return $parsed->format('Y-m-d') === $date ? $date : null;
    }

    protected function selectedDate(): ?string
    {
        $date = $this->sanitizeDate(request()->query('date'));

        if (filled($date)) {
            return $date;
        }

        $sessionDate = session('academic_year_date');
        $sanitizedSessionDate = $this->sanitizeDate($sessionDate);

        if ($sessionDate !== null && $sanitizedSessionDate === null) {
            session()->forget('academic_year_date');
        }

        return $sanitizedSessionDate;
    }
}
