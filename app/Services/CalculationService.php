<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\Matiere;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CalculationService
{
    public function calculateMoyenneMatiere(Eleve $eleve, Matiere $matiere, ?AnneeAcademique $annee = null): ?float
    {
        $annee = $annee ?? currentAcademicYear();

        if (! $annee) {
            return null;
        }

        $cacheKey = "moyenne:eleve:{$eleve->id}:matiere:{$matiere->id}:annee:{$annee->id}";

        return Cache::remember($cacheKey, 300, function () use ($eleve, $matiere, $annee) {
            $notes = DB::table('notes')
                ->where('eleve_id', $eleve->id)
                ->where('matiere_id', $matiere->id)
                ->where('annee_academique_id', $annee->id)
                ->get();

            if ($notes->isEmpty()) {
                return null;
            }

            $avgDevoirs = $notes->where('type_devoir', 'devoir')->avg('note');
            $noteComposition = $notes->where('type_devoir', 'composition')->first()?->note;

            if ($avgDevoirs === null && $noteComposition === null) {
                return null;
            }

            $mDevoirs = $avgDevoirs ?? 0;
            $mComp = $noteComposition ?? $avgDevoirs ?? 0;

            return round(($mDevoirs + $mComp) / 2, 2);
        });
    }

    public function calculateMoyenneGenerale(Eleve $eleve, ?AnneeAcademique $annee = null): ?float
    {
        $annee = $annee ?? currentAcademicYear();

        if (! $annee) {
            return null;
        }

        $inscription = $eleve->inscriptions()->where('annee_academique_id', $annee->id)->first();

        if (! $inscription || ! $inscription->classe) {
            return null;
        }

        $matieres = $inscription->classe->matieresForAnnee($annee->id)->get();

        $totalPoints = 0;
        $totalCoeffs = 0;

        foreach ($matieres as $matiere) {
            $coefficient = $matiere->pivot->coefficient;
            $moyenne = $this->calculateMoyenneMatiere($eleve, $matiere, $annee);

            if ($moyenne !== null) {
                $totalPoints += $moyenne * $coefficient;
                $totalCoeffs += $coefficient;
            }
        }

        return $totalCoeffs > 0 ? round($totalPoints / $totalCoeffs, 2) : null;
    }

    public function calculateRang(Eleve $eleve, ?AnneeAcademique $annee = null): array
    {
        $annee = $annee ?? currentAcademicYear();

        if (! $annee) {
            return ['rang' => null, 'total' => 0];
        }

        $inscription = $eleve->inscriptions()->where('annee_academique_id', $annee->id)->first();

        if (! $inscription) {
            return ['rang' => null, 'total' => 0];
        }

        $eleveIds = DB::table('inscriptions')
            ->where('classe_id', $inscription->classe_id)
            ->where('annee_academique_id', $annee->id)
            ->pluck('eleve_id');

        if ($eleveIds->isEmpty()) {
            return ['rang' => null, 'total' => 0];
        }

        $moyennes = [];

        foreach ($eleveIds as $eleveId) {
            $currentEleve = Eleve::find($eleveId);

            if (! $currentEleve) {
                continue;
            }

            $moyenne = $this->calculateMoyenneGenerale($currentEleve, $annee);

            if ($moyenne !== null) {
                $moyennes[$eleveId] = $moyenne;
            }
        }

        $totalEleves = $eleveIds->count();

        if ($moyennes === []) {
            return ['rang' => null, 'total' => $totalEleves];
        }

        arsort($moyennes);

        $rang = 1;

        foreach ($moyennes as $eleveId => $moyenne) {
            if ((int) $eleveId === (int) $eleve->id) {
                return ['rang' => $rang, 'total' => $totalEleves];
            }

            $rang++;
        }

        return ['rang' => null, 'total' => $totalEleves];
    }

    public function getMention(float $moyenne): string
    {
        if ($moyenne >= 16) {
            return 'Excellent';
        }

        if ($moyenne >= 14) {
            return 'Tres Bien';
        }

        if ($moyenne >= 12) {
            return 'Bien';
        }

        if ($moyenne >= 10) {
            return 'Assez Bien';
        }

        return 'Insuffisant';
    }

    public function getBulletinData(Eleve $eleve): array
    {
        $annee = currentAcademicYear();
        $date = currentAcademicDate();
        $classe = $eleve->classeForDate($date);

        if (! $classe) {
            throw new \RuntimeException("L'eleve n'est assigne a aucune classe pour cette annee.");
        }

        if (! $annee) {
            throw new \RuntimeException('Aucune annee academique active.');
        }

        $matieres = $classe->matieresForAnnee($annee->id)->get();

        if ($matieres->isEmpty()) {
            throw new \RuntimeException("La classe {$classe->nom_classe} n'a aucune matiere pour {$annee->libelle}.");
        }

        $notesParMatiere = DB::table('notes')
            ->select(
                'matiere_id',
                DB::raw("AVG(CASE WHEN type_devoir = 'devoir' THEN note END) as avg_devoir"),
                DB::raw("MAX(CASE WHEN type_devoir = 'composition' THEN note END) as note_composition")
            )
            ->where('eleve_id', $eleve->id)
            ->whereIn('matiere_id', $matieres->pluck('id'))
            ->where('annee_academique_id', $annee->id)
            ->groupBy('matiere_id')
            ->get()
            ->keyBy('matiere_id');

        $lignes = [];
        $totalPoints = 0;
        $totalCoefficients = 0;

        foreach ($matieres as $matiere) {
            $coefficient = (int) $matiere->pivot->coefficient;
            $row = $notesParMatiere->get($matiere->id);

            $avgDevoir = $row && $row->avg_devoir !== null ? round((float) $row->avg_devoir, 2) : null;
            $noteComposition = $row && $row->note_composition !== null ? round((float) $row->note_composition, 2) : null;

            $moyenneMatiere = ($avgDevoir === null && $noteComposition === null)
                ? null
                : round((($avgDevoir ?? 0) + ($noteComposition ?? $avgDevoir ?? 0)) / 2, 2);

            $moyXCoef = $moyenneMatiere !== null ? round($moyenneMatiere * $coefficient, 2) : null;

            if ($moyenneMatiere !== null) {
                $totalPoints += $moyXCoef;
                $totalCoefficients += $coefficient;
            }

            $lignes[] = [
                'matiere' => $matiere->nom_matiere,
                'coefficient' => $coefficient,
                'devoir_value' => $avgDevoir,
                'composition_value' => $noteComposition,
                'moyenne_value' => $moyenneMatiere,
                'moy_x_coef_value' => $moyXCoef,
                'moyenne_devoirs' => $this->formatNote($avgDevoir),
                'note_composition' => $this->formatNote($noteComposition),
                'moyenne' => $this->formatNote($moyenneMatiere),
                'moy_x_coef' => $this->formatNote($moyXCoef),
                'appreciation' => $this->getAppreciation($moyenneMatiere),
            ];
        }

        $moyenneGenerale = $totalCoefficients > 0
            ? round($totalPoints / $totalCoefficients, 2)
            : null;

        $rangData = $this->calculateRang($eleve, $annee);

        return [
            'eleve' => $eleve,
            'classe' => $classe,
            'annee' => $annee,
            'lignes' => $lignes,
            'total_points' => round($totalPoints, 2),
            'total_points_formatted' => $this->formatNote($totalPoints > 0 ? round($totalPoints, 2) : null),
            'total_coefficients' => $totalCoefficients,
            'moyenne_generale' => $moyenneGenerale,
            'moyenne_semestre_1' => $moyenneGenerale,
            'moyenne_semestre_2' => null,
            'moyenne_annuelle' => null,
            'rang' => $rangData['rang'],
            'total_eleves' => $rangData['total'],
            'mention' => $moyenneGenerale !== null ? $this->getMention($moyenneGenerale) : '',
        ];
    }

    protected function formatNote(?float $value): string
    {
        return $value !== null ? number_format($value, 2, ',', ' ') : '';
    }

    protected function getAppreciation(?float $moyenne): string
    {
        return $moyenne !== null ? $this->getMention($moyenne) : '';
    }
}