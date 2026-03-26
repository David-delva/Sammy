<?php

use App\Models\AnneeAcademique;
use App\Services\AcademicYearService;

if (! function_exists('currentAcademicYear')) {
    function currentAcademicYear(): ?AnneeAcademique
    {
        return app(AcademicYearService::class)->forDate();
    }
}

if (! function_exists('currentAcademicDate')) {
    function currentAcademicDate(): string
    {
        return app(AcademicYearService::class)->referenceDate();
    }
}

if (! function_exists('currentAcademicLabel')) {
    function currentAcademicLabel(): ?string
    {
        $y = currentAcademicYear();

        return $y ? $y->libelle : null;
    }
}
