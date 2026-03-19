<?php

namespace App\Services;

use App\Models\Eleve;
use App\Models\Matiere;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CalculationService
{
    public function calculateMoyenneMatiere(Eleve $eleve, Matiere $matiere): ?float
    {
        // Resolve academic year
        $annee = null;
        if (function_exists('currentAcademicYear')) {
            $annee = currentAcademicYear();
        } elseif (app()->bound('currentAcademicYear')) {
            $annee = app('currentAcademicYear');
        }

        $anneeId = $annee ? $annee->id : 'global';
        $cacheKey = "moyenne:eleve:{$eleve->id}:matiere:{$matiere->id}:annee:{$anneeId}";

        return Cache::remember($cacheKey, 300, function () use ($eleve, $matiere, $annee) {
            // Compute averages directly in SQL to avoid loading collections
            $q = DB::table('notes')
                ->where('eleve_id', $eleve->id)
                ->where('matiere_id', $matiere->id);

            if ($annee) {
                $q->where('annee_academique_id', $annee->id);
            }

            $avgDevoir = (float) $q->clone()->where('type_devoir', 'devoir')->avg('note');
            $compositionNote = $q->clone()->where('type_devoir', 'composition')->orderByDesc('id')->value('note');

            if ($avgDevoir === 0.0 && $compositionNote === null) {
                return null;
            }

            $moyenneDevoirs = $avgDevoir ?: 0;
            $noteComposition = $compositionNote ? (float) $compositionNote : 0.0;

            return ($moyenneDevoirs + $noteComposition) / 2;
        });
    }

    public function calculateMoyenneGenerale(Eleve $eleve): ?float
    {
        // Resolve academic year and classe for date
        $date = null;
        if (function_exists('currentAcademicDate')) {
            $date = currentAcademicDate();
        }

        $annee = function_exists('currentAcademicYear') ? currentAcademicYear() : (app()->bound('currentAcademicYear') ? app('currentAcademicYear') : null);
        $classe = $eleve->classeForDate($date) ?? $eleve->classe;

        if (!$classe) {
            return null;
        }

        $matieres = $classe->matieres;
        if ($matieres->isEmpty()) {
            return null;
        }

        $anneeId = $annee ? $annee->id : null;
        $mIds = $matieres->pluck('id')->toArray();

        // Get per-matiere aggregated notes for this eleve in one query
        $rows = DB::table('notes')
            ->select('matiere_id', DB::raw("AVG(CASE WHEN type_devoir='devoir' THEN note END) as avg_devoir"), DB::raw("MAX(CASE WHEN type_devoir='composition' THEN note END) as comp_note"))
            ->where('eleve_id', $eleve->id)
            ->whereIn('matiere_id', $mIds)
            ->when($anneeId, fn($q) => $q->where('annee_academique_id', $anneeId))
            ->groupBy('matiere_id')
            ->get()
            ->keyBy('matiere_id');

        $totalPoints = 0;
        $totalCoefficients = 0;

        foreach ($matieres as $matiere) {
            $row = $rows->get($matiere->id);
            $avgDevoir = $row ? (float) $row->avg_devoir : 0.0;
            $comp = $row ? $row->comp_note : null;
            if ($avgDevoir === 0.0 && $comp === null) {
                continue;
            }

            $moyenneMatiere = ($avgDevoir + ($comp !== null ? (float) $comp : 0.0)) / 2;
            $totalPoints += $moyenneMatiere * $matiere->coefficient;
            $totalCoefficients += $matiere->coefficient;
        }

        $result = $totalCoefficients > 0 ? round($totalPoints / $totalCoefficients, 2) : null;
        $globalKey = "moyenne_generale:eleve:{$eleve->id}:annee:" . ($annee ? $annee->id : 'global');
        Cache::put($globalKey, $result, 300);
        return $result;
    }

    public function calculateRang(Eleve $eleve): ?int
    {
        // Resolve inscription (prefer current academic year)
        $date = function_exists('currentAcademicDate') ? currentAcademicDate() : null;
        $inscription = $eleve->inscriptionForDate($date) ?? $eleve->latestInscription;
        if (!$inscription) return null;

        $anneeId = $inscription->annee_academique_id;
        $classeId = $inscription->classe_id;

        // Get class matieres and coefficients
        $matieres = Matiere::whereIn('classe_id', [$classeId])->get();
        $coeffs = $matieres->pluck('coefficient', 'id')->toArray();

        $matiereIds = array_keys($coeffs);
        if (empty($matiereIds)) return null;

        // Aggregate notes per student & per matiere in one query
        $rows = DB::table('notes')
            ->select('eleve_id', 'matiere_id', DB::raw("AVG(CASE WHEN type_devoir='devoir' THEN note END) as avg_devoir"), DB::raw("MAX(CASE WHEN type_devoir='composition' THEN note END) as comp_note"))
            ->whereIn('matiere_id', $matiereIds)
            ->where('annee_academique_id', $anneeId)
            ->groupBy('eleve_id', 'matiere_id')
            ->get();

        // accumulate
        $points = [];
        $coeffSum = [];
        foreach ($rows as $r) {
            $mId = $r->matiere_id;
            $eId = $r->eleve_id;
            $avgDevoir = $r->avg_devoir ? (float) $r->avg_devoir : 0.0;
            $comp = $r->comp_note !== null ? (float) $r->comp_note : null;
            if ($avgDevoir === 0.0 && $comp === null) continue;
            $moy = ($avgDevoir + ($comp !== null ? $comp : 0.0)) / 2.0;
            $coef = $coeffs[$mId] ?? 1;
            $points[$eId] = ($points[$eId] ?? 0) + ($moy * $coef);
            $coeffSum[$eId] = ($coeffSum[$eId] ?? 0) + $coef;
        }

        // compute averages
        $moyennes = [];
        foreach ($points as $eId => $pt) {
            $moyennes[] = ['id' => $eId, 'moyenne' => $coeffSum[$eId] > 0 ? round($pt / $coeffSum[$eId], 2) : null];
        }

        // sort
        usort($moyennes, function($a, $b) { return $b['moyenne'] <=> $a['moyenne']; });

        // find rank
        $rank = null;
        foreach ($moyennes as $i => $row) {
            if ($row['id'] == $eleve->id) { $rank = $i + 1; break; }
        }

        return $rank;
    }

    public function getMention(float $moyenne): string
    {
        if ($moyenne >= 16) {
            return 'Excellente';
        } elseif ($moyenne >= 14) {
            return 'Très Bien';
        } elseif ($moyenne >= 12) {
            return 'Bien';
        } elseif ($moyenne >= 10) {
            return 'Assez Bien';
        } else {
            return 'Insuffisant';
        }
    }

   public function getBulletinData(Eleve $eleve): array
    {
        // ← ajoute 'classe.matieres' pour charger les matières de la classe
        $annee = function_exists('currentAcademicYear') ? currentAcademicYear() : (app()->bound('currentAcademicYear') ? app('currentAcademicYear') : null);

        // Prefer classe linked to the selected academic year when present
        $date = function_exists('currentAcademicDate') ? currentAcademicDate() : null;
        $classe = $eleve->classeForDate($date) ?? $eleve->classe;

        $eleve->load(['notes.matiere']);

        // ← vérifie que l'élève a bien une classe avant de continuer
        if (!$classe) {
            throw new \Exception("L'élève {$eleve->nom} {$eleve->prenom} n'est assigné à aucune classe pour l'année académique sélectionnée.");
        }

        // ← vérifie que la classe a des matières
        if ($classe->matieres->isEmpty()) {
            throw new \Exception("La classe {$classe->nom_classe} n'a aucune matière associée.");
        }

        $matieres  = $classe->matieres;
        $resultats = [];

        foreach ($matieres as $matiere) {
            // filter notes to the selected academic year when available
            $notesQuery = $eleve->notes()->where('matiere_id', $matiere->id);
            if ($annee) {
                $notesQuery->where('annee_academique_id', $annee->id);
            }
            $notes = $notesQuery->get();

            $devoir      = $notes->where('type_devoir', 'devoir')->first();
            $composition = $notes->where('type_devoir', 'composition')->first();

            $moyenneMatiere = $this->calculateMoyenneMatiere($eleve, $matiere);

            $resultats[] = [
                'matiere'     => $matiere,
                'devoir'      => $devoir?->note,
                'composition' => $composition?->note,
                'moyenne'     => $moyenneMatiere,
                'moy_x_coef'  => $moyenneMatiere ? round($moyenneMatiere * $matiere->coefficient, 2) : null,
            ];
        }

        return $resultats;
    }
}
