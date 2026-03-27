<?php

namespace App\Services;

use App\Models\Inscription;
use Illuminate\Support\Facades\DB;

class AcademicIntegrityService
{
    public function validateStudentSubjectForYear(int $eleveId, int $matiereId, int $anneeId): ?string
    {
        $classeId = $this->getStudentClassIdForYear($eleveId, $anneeId);

        if ($classeId === null) {
            return "L'eleve selectionne n'est pas inscrit pour cette annee academique.";
        }

        if (! $this->isSubjectAssignedToClassForYear($matiereId, $classeId, $anneeId)) {
            return "La matiere selectionnee n'est pas assignee a la classe de cet eleve pour cette annee academique.";
        }

        return null;
    }

    public function validateClassSubjectForYear(int $classeId, int $matiereId, int $anneeId): ?string
    {
        if ($this->isSubjectAssignedToClassForYear($matiereId, $classeId, $anneeId)) {
            return null;
        }

        return "La matiere selectionnee n'est pas assignee a cette classe pour cette annee academique.";
    }

    /**
     * @param  array<int|string, mixed>  $eleveIds
     * @return array<int, int|string>
     */
    public function invalidStudentIdsForClassYear(array $eleveIds, int $classeId, int $anneeId): array
    {
        $normalizedIds = collect($eleveIds)
            ->map(fn ($value) => is_numeric($value) ? (int) $value : $value)
            ->unique()
            ->values();

        $invalidIds = $normalizedIds
            ->filter(fn ($value) => ! is_int($value) || $value <= 0)
            ->values()
            ->all();

        $validIds = Inscription::query()
            ->where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeId)
            ->whereIn('eleve_id', $normalizedIds->filter(fn ($value) => is_int($value) && $value > 0)->all())
            ->pluck('eleve_id')
            ->map(fn ($value) => (int) $value)
            ->all();

        return array_values(array_unique([
            ...$invalidIds,
            ...$normalizedIds
                ->filter(fn ($value) => is_int($value) && $value > 0 && ! in_array($value, $validIds, true))
                ->all(),
        ]));
    }

    public function getStudentClassIdForYear(int $eleveId, int $anneeId): ?int
    {
        $classeId = Inscription::query()
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->value('classe_id');

        return $classeId !== null ? (int) $classeId : null;
    }

    public function isSubjectAssignedToClassForYear(int $matiereId, int $classeId, int $anneeId): bool
    {
        return DB::table('classe_matiere')
            ->where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->where('annee_academique_id', $anneeId)
            ->exists();
    }
}
