<?php

namespace App\Services;

use App\Models\AcademicResult;
use App\Models\AcademicSubjectResult;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AcademicPerformanceProjector
{
    public function rebuildAll(?int $anneeId = null, ?int $classeId = null, ?int $eleveId = null): int
    {
        $query = Inscription::query()
            ->select(['id', 'eleve_id', 'annee_academique_id'])
            ->when($anneeId !== null, fn ($builder) => $builder->where('annee_academique_id', $anneeId))
            ->when($classeId !== null, fn ($builder) => $builder->where('classe_id', $classeId))
            ->when($eleveId !== null, fn ($builder) => $builder->where('eleve_id', $eleveId));

        $processed = 0;

        $query->orderBy('id')->chunkById(100, function (EloquentCollection $inscriptions) use (&$processed) {
            foreach ($inscriptions as $inscription) {
                $this->rebuildStudentYear((int) $inscription->eleve_id, (int) $inscription->annee_academique_id);
                $processed++;
            }
        });

        if ($processed === 0 && $anneeId !== null && $eleveId !== null) {
            $this->deleteStudentYear($eleveId, $anneeId);
        }

        return $processed;
    }

    public function rebuildClassYear(int $classeId, int $anneeId): int
    {
        return $this->rebuildAll($anneeId, $classeId);
    }

    public function rebuildStudentsForYear(iterable $eleveIds, int $anneeId): int
    {
        $processed = 0;

        foreach (collect($eleveIds)->map(fn ($value) => (int) $value)->filter()->unique()->values() as $eleveId) {
            $this->rebuildStudentYear($eleveId, $anneeId);
            $processed++;
        }

        return $processed;
    }

    public function rebuildStudentYear(int $eleveId, int $anneeId): void
    {
        $inscription = Inscription::query()
            ->with('classe')
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->first();

        if (! $inscription || ! $inscription->classe) {
            $this->deleteStudentYear($eleveId, $anneeId);

            return;
        }

        /** @var Classe $classe */
        $classe = $inscription->classe;
        /** @var EloquentCollection<int, Matiere> $matieres */
        $matieres = $classe->matieresForAnnee($anneeId)->get();

        if ($matieres->isEmpty()) {
            $this->deleteStudentYear($eleveId, $anneeId);

            return;
        }

        $annualStats = $this->aggregateNotes($eleveId, $anneeId);
        $semesterStats = [
            AcademicResult::PERIOD_SEMESTRE_1 => $this->aggregateNotes($eleveId, $anneeId, Note::SEMESTRE_1),
            AcademicResult::PERIOD_SEMESTRE_2 => $this->aggregateNotes($eleveId, $anneeId, Note::SEMESTRE_2),
        ];

        $timestamp = now();
        $subjectRows = [];
        $resultRows = [];

        foreach (AcademicResult::periods() as $period) {
            $stats = $period === AcademicResult::PERIOD_ANNUAL
                ? $annualStats
                : $semesterStats[$period];

            $totalPoints = 0.0;
            $totalCoefficients = 0;
            $totalNotes = 0;
            $evaluatedSubjects = 0;

            foreach ($matieres as $matiere) {
                $statsRow = $stats->get($matiere->id);
                $coefficient = (int) $matiere->pivot->coefficient;
                $moyenneDevoirs = $this->roundStat($statsRow?->avg_devoir);
                $noteComposition = $this->roundStat($statsRow?->note_composition);
                $moyenneMatiere = $this->calculateSubjectAverage($moyenneDevoirs, $noteComposition);
                $moyXCoef = $moyenneMatiere !== null ? round($moyenneMatiere * $coefficient, 2) : null;
                $notesCount = $statsRow ? (int) $statsRow->total_notes : 0;

                if ($moyenneMatiere !== null) {
                    $totalPoints += $moyXCoef;
                    $totalCoefficients += $coefficient;
                    $evaluatedSubjects++;
                }

                $totalNotes += $notesCount;

                $subjectRows[] = [
                    'eleve_id' => $eleveId,
                    'classe_id' => $inscription->classe_id,
                    'annee_academique_id' => $anneeId,
                    'matiere_id' => $matiere->id,
                    'period' => $period,
                    'coefficient' => $coefficient,
                    'total_notes' => $notesCount,
                    'moyenne_devoirs' => $moyenneDevoirs,
                    'note_composition' => $noteComposition,
                    'moyenne_matiere' => $moyenneMatiere,
                    'moy_x_coef' => $moyXCoef,
                    'last_recorded_at' => $statsRow?->last_recorded_at,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            $resultRows[] = [
                'eleve_id' => $eleveId,
                'classe_id' => $inscription->classe_id,
                'annee_academique_id' => $anneeId,
                'period' => $period,
                'total_notes' => $totalNotes,
                'evaluated_subjects' => $evaluatedSubjects,
                'total_points' => $totalCoefficients > 0 ? round($totalPoints, 2) : null,
                'total_coefficients' => $totalCoefficients,
                'moyenne_generale' => $totalCoefficients > 0 ? round($totalPoints / $totalCoefficients, 2) : null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $matiereIds = $matieres->modelKeys();

        DB::transaction(function () use ($eleveId, $anneeId, $subjectRows, $resultRows, $matiereIds) {
            AcademicSubjectResult::query()
                ->where('eleve_id', $eleveId)
                ->where('annee_academique_id', $anneeId)
                ->whereNotIn('matiere_id', $matiereIds)
                ->delete();

            AcademicSubjectResult::query()->upsert(
                $subjectRows,
                ['eleve_id', 'annee_academique_id', 'matiere_id', 'period'],
                ['classe_id', 'coefficient', 'total_notes', 'moyenne_devoirs', 'note_composition', 'moyenne_matiere', 'moy_x_coef', 'last_recorded_at', 'updated_at']
            );

            AcademicResult::query()->upsert(
                $resultRows,
                ['eleve_id', 'annee_academique_id', 'period'],
                ['classe_id', 'total_notes', 'evaluated_subjects', 'total_points', 'total_coefficients', 'moyenne_generale', 'updated_at']
            );
        });
    }

    private function aggregateNotes(int $eleveId, int $anneeId, ?int $semestre = null): Collection
    {
        return DB::table('notes')
            ->select(
                'matiere_id',
                DB::raw('COUNT(*) as total_notes'),
                DB::raw("AVG(CASE WHEN type_devoir = 'devoir' THEN note END) as avg_devoir"),
                DB::raw("MAX(CASE WHEN type_devoir = 'composition' THEN note END) as note_composition"),
                DB::raw('MAX(created_at) as last_recorded_at')
            )
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->when($semestre !== null, fn ($query) => $query->where('semestre', $semestre))
            ->groupBy('matiere_id')
            ->get()
            ->keyBy('matiere_id');
    }

    private function calculateSubjectAverage(?float $moyenneDevoirs, ?float $noteComposition): ?float
    {
        if ($moyenneDevoirs === null && $noteComposition === null) {
            return null;
        }

        $baseDevoirs = $moyenneDevoirs ?? 0;
        $baseComposition = $noteComposition ?? $moyenneDevoirs ?? 0;

        return round(($baseDevoirs + $baseComposition) / 2, 2);
    }

    private function roundStat(mixed $value): ?float
    {
        return $value !== null ? round((float) $value, 2) : null;
    }

    private function deleteStudentYear(int $eleveId, int $anneeId): void
    {
        AcademicSubjectResult::query()
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->delete();

        AcademicResult::query()
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->delete();
    }
}
