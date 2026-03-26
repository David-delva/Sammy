<?php

namespace App\Observers;

use App\Models\Inscription;
use App\Services\AcademicPerformanceProjector;

class InscriptionObserver
{
    public function __construct(
        private readonly AcademicPerformanceProjector $projector,
    ) {}

    public function created(Inscription $inscription): void
    {
        $this->projector->rebuildStudentYear((int) $inscription->eleve_id, (int) $inscription->annee_academique_id);
    }

    public function updated(Inscription $inscription): void
    {
        $targets = collect([
            [(int) $inscription->eleve_id, (int) $inscription->annee_academique_id],
            [(int) $inscription->getOriginal('eleve_id'), (int) $inscription->getOriginal('annee_academique_id')],
        ])->filter(fn (array $target) => $target[0] > 0 && $target[1] > 0)
            ->unique(fn (array $target) => implode(':', $target));

        foreach ($targets as [$eleveId, $anneeId]) {
            $this->projector->rebuildStudentYear($eleveId, $anneeId);
        }
    }

    public function deleted(Inscription $inscription): void
    {
        $this->projector->rebuildStudentYear((int) $inscription->eleve_id, (int) $inscription->annee_academique_id);
    }
}
