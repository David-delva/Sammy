<?php

use App\Models\AnneeAcademique;

if (! function_exists('currentAcademicYear')) {
    /**
     * Return the currently selected academic year instance.
     */
    function currentAcademicYear(): ?AnneeAcademique
    {
        $y = app()->has('currentAcademicYear') ? app('currentAcademicYear') : null;
        if ($y) return $y;

        // fallback: try session or DB
        $date = session('academic_year_date');
        return AnneeAcademique::getActiveByDate($date);
    }
}

if (! function_exists('currentAcademicDate')) {
    /**
     * Return the date string used to compute the current academic year (Y-m-d).
     */
    function currentAcademicDate(): string
    {
        return session('academic_year_date') ?? now()->toDateString();
    }
}

if (! function_exists('currentAcademicLabel')) {
    function currentAcademicLabel(): ?string
    {
        $y = currentAcademicYear();
        return $y ? $y->libelle : null;
    }
}
