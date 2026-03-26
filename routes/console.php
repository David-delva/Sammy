<?php

use App\Services\AcademicPerformanceProjector;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('academic-results:rebuild {--year=} {--class=} {--student=}', function (AcademicPerformanceProjector $projector) {
    $processed = $projector->rebuildAll(
        $this->option('year') !== null ? (int) $this->option('year') : null,
        $this->option('class') !== null ? (int) $this->option('class') : null,
        $this->option('student') !== null ? (int) $this->option('student') : null,
    );

    $this->info("{$processed} projection(s) reconstruite(s).");
})->purpose('Rebuild academic result projections for rankings and bulletins');
