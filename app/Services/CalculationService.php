<?php

namespace App\Services;

use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CalculationService
{
    /**
     * Calcule la moyenne d'un élève pour une matière précise.
     */
    public function calculateMoyenneMatiere(Eleve $eleve, Matiere $matiere, ?AnneeAcademique $annee = null): ?float
    {
        $annee = $annee ?? currentAcademicYear();
        if (!$annee) return null;

        $cacheKey = "moyenne:eleve:{$eleve->id}:matiere:{$matiere->id}:annee:{$annee->id}";

        return Cache::remember($cacheKey, 300, function () use ($eleve, $matiere, $annee) {
            $notes = DB::table('notes')
                ->where('eleve_id', $eleve->id)
                ->where('matiere_id', $matiere->id)
                ->where('annee_academique_id', $annee->id)
                ->get();

            if ($notes->isEmpty()) return null;

            $avgDevoirs = $notes->where('type_devoir', 'devoir')->avg('note');
            $noteComposition = $notes->where('type_devoir', 'composition')->first()?->note;

            if ($avgDevoirs === null && $noteComposition === null) return null;

            // Logique standard : (Moyenne Devoirs + Composition) / 2
            $mDevoirs = $avgDevoirs ?? 0;
            $mComp = $noteComposition ?? $mDevoirs; // Si pas de compo, on prend la moyenne devoirs

            return round(($mDevoirs + $mComp) / 2, 2);
        });
    }

    /**
     * Calcule la moyenne générale de l'élève pour une année donnée.
     */
    public function calculateMoyenneGenerale(Eleve $eleve, ?AnneeAcademique $annee = null): ?float
    {
        $annee = $annee ?? currentAcademicYear();
        if (!$annee) return null;

        $inscription = $eleve->inscriptions()->where('annee_academique_id', $annee->id)->first();
        if (!$inscription || !$inscription->classe) return null;

        $matieres = $inscription->classe->matieres;
        $totalPoints = 0;
        $totalCoeffs = 0;

        foreach ($matieres as $matiere) {
            $moyenne = $this->calculateMoyenneMatiere($eleve, $matiere, $annee);
            if ($moyenne !== null) {
                $totalPoints += ($moyenne * $matiere->coefficient);
                $totalCoeffs += $matiere->coefficient;
            }
        }

        return $totalCoeffs > 0 ? round($totalPoints / $totalCoeffs, 2) : null;
    }

    /**
     * Calcule le rang de l'élève dans sa classe.
     */
    public function calculateRang(Eleve $eleve, ?AnneeAcademique $annee = null): ?int
    {
        $annee = $annee ?? currentAcademicYear();
        if (!$annee) return null;

        $inscription = $eleve->inscriptions()->where('annee_academique_id', $annee->id)->first();
        if (!$inscription) return null;

        $classeId = $inscription->classe_id;

        // Récupérer tous les élèves de la classe pour cette année
        $eleveIds = DB::table('inscriptions')
            ->where('classe_id', $classeId)
            ->where('annee_academique_id', $annee->id)
            ->pluck('eleve_id');

        $moyennes = [];
        foreach ($eleveIds as $id) {
            $e = Eleve::find($id);
            $moy = $this->calculateMoyenneGenerale($e, $annee);
            if ($moy !== null) {
                $moyennes[$id] = $moy;
            }
        }

        arsort($moyennes);

        $rank = 1;
        foreach ($moyennes as $id => $moy) {
            if ($id == $eleve->id) return $rank;
            $rank++;
        }

        return null;
    }

    public function getMention(float $moyenne): string
    {
        if ($moyenne >= 16) return 'Excellent';
        if ($moyenne >= 14) return 'Très Bien';
        if ($moyenne >= 12) return 'Bien';
        if ($moyenne >= 10) return 'Assez Bien';
        return 'Insuffisant';
    }
}
