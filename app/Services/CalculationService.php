<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Note;
use Illuminate\Support\Facades\DB;

class CalculationService
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
    ) {}

    public function calculateMoyenneMatiere(Eleve $eleve, Matiere $matiere, ?AnneeAcademique $annee = null, ?int $semestre = null): ?float
    {
        $annee = $annee ?? currentAcademicYear();
        $semestre = $this->normalizeSemestre($semestre);

        if (! $annee) {
            return null;
        }

        return $this->academicCache->remember(
            $this->academicCache->noteAverageKey($eleve->id, $matiere->id, $annee->id, $semestre),
            300,
            function () use ($eleve, $matiere, $annee, $semestre) {
                $notes = DB::table('notes')
                    ->where('eleve_id', $eleve->id)
                    ->where('matiere_id', $matiere->id)
                    ->where('annee_academique_id', $annee->id)
                    ->when($semestre !== null, fn ($query) => $query->where('semestre', $semestre))
                    ->get();

                if ($notes->isEmpty()) {
                    return null;
                }

                $moyenneDevoirs = $notes->where('type_devoir', 'devoir')->avg('note');
                $noteComposition = $notes->where('type_devoir', 'composition')->max('note');

                if ($moyenneDevoirs === null && $noteComposition === null) {
                    return null;
                }

                $baseDevoirs = $moyenneDevoirs ?? 0;
                $baseComposition = $noteComposition ?? $moyenneDevoirs ?? 0;

                return round(($baseDevoirs + $baseComposition) / 2, 2);
            }
        );
    }

    public function calculateMoyenneGenerale(Eleve $eleve, ?AnneeAcademique $annee = null, ?int $semestre = null): ?float
    {
        $annee = $annee ?? currentAcademicYear();
        $semestre = $this->normalizeSemestre($semestre);

        if (! $annee) {
            return null;
        }

        $inscription = $eleve->inscriptions()
            ->where('annee_academique_id', $annee->id)
            ->first();

        if (! $inscription || ! $inscription->classe) {
            return null;
        }

        $matieres = $inscription->classe->matieresForAnnee($annee->id)->get();

        $totalPoints = 0;
        $totalCoefficients = 0;

        foreach ($matieres as $matiere) {
            $coefficient = (int) $matiere->pivot->coefficient;
            $moyenne = $this->calculateMoyenneMatiere($eleve, $matiere, $annee, $semestre);

            if ($moyenne !== null) {
                $totalPoints += $moyenne * $coefficient;
                $totalCoefficients += $coefficient;
            }
        }

        return $totalCoefficients > 0
            ? round($totalPoints / $totalCoefficients, 2)
            : null;
    }

    public function calculateRang(Eleve $eleve, ?AnneeAcademique $annee = null, ?int $semestre = null): array
    {
        $annee = $annee ?? currentAcademicYear();
        $semestre = $this->normalizeSemestre($semestre);

        if (! $annee) {
            return ['rang' => null, 'total' => 0];
        }

        $inscription = $eleve->inscriptions()
            ->where('annee_academique_id', $annee->id)
            ->first();

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

            $moyenne = $this->calculateMoyenneGenerale($currentEleve, $annee, $semestre);

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

    public function getBulletinData(Eleve $eleve, int $semestre = Note::SEMESTRE_1): array
    {
        $semestre = $this->normalizeSemestre($semestre) ?? Note::SEMESTRE_1;
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
            ->where('semestre', $semestre)
            ->groupBy('matiere_id')
            ->get()
            ->keyBy('matiere_id');

        $lignes = [];
        $totalPoints = 0;
        $totalCoefficients = 0;

        foreach ($matieres as $matiere) {
            $coefficient = (int) $matiere->pivot->coefficient;
            $row = $notesParMatiere->get($matiere->id);

            $moyenneDevoirs = $row && $row->avg_devoir !== null ? round((float) $row->avg_devoir, 2) : null;
            $noteComposition = $row && $row->note_composition !== null ? round((float) $row->note_composition, 2) : null;

            $moyenneMatiere = ($moyenneDevoirs === null && $noteComposition === null)
                ? null
                : round((($moyenneDevoirs ?? 0) + ($noteComposition ?? $moyenneDevoirs ?? 0)) / 2, 2);

            $moyXCoef = $moyenneMatiere !== null
                ? round($moyenneMatiere * $coefficient, 2)
                : null;

            if ($moyenneMatiere !== null) {
                $totalPoints += $moyXCoef;
                $totalCoefficients += $coefficient;
            }

            $lignes[] = [
                'matiere' => $matiere->nom_matiere,
                'coefficient' => $coefficient,
                'moyenne_devoirs' => $this->formatNote($moyenneDevoirs),
                'note_composition' => $this->formatNote($noteComposition),
                'moyenne' => $this->formatNote($moyenneMatiere),
                'moy_x_coef' => $this->formatNote($moyXCoef),
                'appreciation' => $this->getAppreciation($moyenneMatiere),
            ];
        }

        $moyenneSemestre1 = $this->calculateMoyenneGenerale($eleve, $annee, Note::SEMESTRE_1);
        $moyenneSemestre2 = $this->calculateMoyenneGenerale($eleve, $annee, Note::SEMESTRE_2);
        $moyenneSelectionnee = $semestre === Note::SEMESTRE_1 ? $moyenneSemestre1 : $moyenneSemestre2;
        $moyenneAnnuelle = ($moyenneSemestre1 !== null && $moyenneSemestre2 !== null)
            ? round(($moyenneSemestre1 + $moyenneSemestre2) / 2, 2)
            : null;

        $rangData = $this->calculateRang($eleve, $annee, $semestre);
        $bulletinTitre = $semestre === Note::SEMESTRE_1
            ? 'BULLETIN DU 1° SEMESTRE'
            : 'BULLETIN DU 2° SEMESTRE';

        return [
            'eleve' => $eleve,
            'classe' => $classe,
            'annee' => $annee,
            'semestre' => $semestre,
            'bulletin_titre' => $bulletinTitre,
            'lignes' => $lignes,
            'total_points' => round($totalPoints, 2),
            'total_points_formatted' => $this->formatNote($totalCoefficients > 0 ? $totalPoints : null),
            'total_coefficients' => $totalCoefficients,
            'moyenne_generale' => $moyenneSelectionnee,
            'moyenne_semestre_1' => $moyenneSemestre1,
            'moyenne_semestre_2' => $moyenneSemestre2,
            'moyenne_annuelle' => $moyenneAnnuelle,
            'rang' => $rangData['rang'],
            'total_eleves' => $rangData['total'],
            'mention' => $moyenneSelectionnee !== null ? $this->getMention($moyenneSelectionnee) : '',
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

    protected function normalizeSemestre(?int $semestre): ?int
    {
        return in_array($semestre, [Note::SEMESTRE_1, Note::SEMESTRE_2], true)
            ? $semestre
            : null;
    }
}
