<?php

namespace App\Observers;

use App\Models\Note;
use App\Services\AcademicCacheService;
use App\Services\AcademicPerformanceProjector;

class NoteObserver
{
    public function __construct(
        private readonly AcademicPerformanceProjector $projector,
        private readonly AcademicCacheService $academicCache,
    ) {}

    public function created(Note $note): void
    {
        $this->syncTargets([
            $this->payload((int) $note->eleve_id, (int) $note->matiere_id, (int) $note->annee_academique_id),
        ]);
    }

    public function updated(Note $note): void
    {
        $targets = [
            $this->payload((int) $note->eleve_id, (int) $note->matiere_id, (int) $note->annee_academique_id),
        ];

        $originalEleveId = (int) $note->getOriginal('eleve_id');
        $originalMatiereId = (int) $note->getOriginal('matiere_id');
        $originalAnneeId = (int) $note->getOriginal('annee_academique_id');

        if ($originalEleveId > 0 && $originalMatiereId > 0 && $originalAnneeId > 0) {
            $targets[] = $this->payload($originalEleveId, $originalMatiereId, $originalAnneeId);
        }

        $this->syncTargets($targets);
    }

    public function deleted(Note $note): void
    {
        $this->syncTargets([
            $this->payload((int) $note->eleve_id, (int) $note->matiere_id, (int) $note->annee_academique_id),
        ]);
    }

    private function syncTargets(array $targets): void
    {
        foreach (collect($targets)->unique(fn (array $target) => implode(':', $target)) as $target) {
            $this->projector->rebuildStudentYear($target['eleve_id'], $target['annee_id']);
            $this->forgetNoteCaches($target['eleve_id'], $target['matiere_id'], $target['annee_id']);
        }
    }

    private function forgetNoteCaches(int $eleveId, int $matiereId, int $anneeId): void
    {
        foreach ([null, Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $this->academicCache->forget(
                $this->academicCache->noteAverageKey($eleveId, $matiereId, $anneeId, $semestre)
            );
        }
    }

    private function payload(int $eleveId, int $matiereId, int $anneeId): array
    {
        return [
            'eleve_id' => $eleveId,
            'matiere_id' => $matiereId,
            'annee_id' => $anneeId,
        ];
    }
}
