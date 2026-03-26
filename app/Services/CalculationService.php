<?php

namespace App\Services;

use App\Models\AcademicResult;
use App\Models\AcademicSubjectResult;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use Illuminate\Support\Collection;

class CalculationService
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
        private readonly AcademicPerformanceProjector $projector,
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
                $subjectResult = $this->getAcademicSubjectResult($eleve, $matiere->id, $annee, $semestre);

                return $subjectResult?->moyenne_matiere;
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

        return $this->getAcademicResult($eleve, $annee, $semestre)?->moyenne_generale;
    }

    public function calculateRang(Eleve $eleve, ?AnneeAcademique $annee = null, ?int $semestre = null): array
    {
        $annee = $annee ?? currentAcademicYear();
        $semestre = $this->normalizeSemestre($semestre);

        if (! $annee) {
            return ['rang' => null, 'total' => 0];
        }

        $inscription = $eleve->inscriptions()
            ->with('classe')
            ->where('annee_academique_id', $annee->id)
            ->first();

        if (! $inscription || ! $inscription->classe) {
            return ['rang' => null, 'total' => 0];
        }

        $totalEleves = Inscription::query()
            ->where('classe_id', $inscription->classe_id)
            ->where('annee_academique_id', $annee->id)
            ->count();

        $classement = $this->getClassementForClass($inscription->classe, $annee, $semestre);
        $entry = $classement->first(fn (array $item) => (int) $item['eleve']->id === (int) $eleve->id);

        return [
            'rang' => $entry['rang'] ?? null,
            'total' => $totalEleves,
        ];
    }

    public function getClassementForClass(Classe $classe, ?AnneeAcademique $annee = null, ?int $semestre = null): Collection
    {
        $annee = $annee ?? currentAcademicYear();
        $semestre = $this->normalizeSemestre($semestre);

        if (! $annee) {
            return collect();
        }

        $period = AcademicResult::periodFromSemestre($semestre);
        $totalEleves = Inscription::query()
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->count();

        if ($totalEleves === 0) {
            return collect();
        }

        $projectionCount = AcademicResult::query()
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->count();

        if ($projectionCount !== $totalEleves) {
            $this->projector->rebuildClassYear($classe->id, $annee->id);
        }

        $results = AcademicResult::query()
            ->with('eleve')
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->whereNotNull('moyenne_generale')
            ->orderByDesc('moyenne_generale')
            ->orderBy('eleve_id')
            ->get();

        return $this->applyRanks(
            $results->map(function (AcademicResult $result) {
                return [
                    'eleve' => $result->eleve,
                    'moyenne' => $result->moyenne_generale,
                    'mention' => $this->getMention($result->moyenne_generale),
                ];
            })->values()
        );
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

        $subjectResults = $this->getAcademicSubjectResultsForBulletin($eleve, $annee, $semestre, $matieres->count());
        $lignes = [];
        $totalPoints = 0.0;
        $totalCoefficients = 0;

        foreach ($matieres as $matiere) {
            $row = $subjectResults->get($matiere->id);
            $coefficient = $row ? (int) $row->coefficient : (int) $matiere->pivot->coefficient;
            $moyenneMatiere = $row?->moyenne_matiere;
            $moyXCoef = $row?->moy_x_coef;

            if ($moyenneMatiere !== null) {
                $totalPoints += $moyXCoef ?? 0;
                $totalCoefficients += $coefficient;
            }

            $lignes[] = [
                'matiere' => $matiere->nom_matiere,
                'coefficient' => $coefficient,
                'moyenne_devoirs' => $this->formatNote($row?->moyenne_devoirs),
                'note_composition' => $this->formatNote($row?->note_composition),
                'moyenne' => $this->formatNote($moyenneMatiere),
                'moy_x_coef' => $this->formatNote($moyXCoef),
                'appreciation' => $this->getAppreciation($moyenneMatiere),
            ];
        }

        $moyenneSemestre1 = $this->getAcademicResult($eleve, $annee, Note::SEMESTRE_1)?->moyenne_generale;
        $moyenneSemestre2 = $this->getAcademicResult($eleve, $annee, Note::SEMESTRE_2)?->moyenne_generale;
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

    protected function getAcademicResult(Eleve $eleve, AnneeAcademique $annee, ?int $semestre): ?AcademicResult
    {
        $period = AcademicResult::periodFromSemestre($semestre);

        $result = AcademicResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->first();

        if ($result) {
            return $result;
        }

        $this->projector->rebuildStudentYear($eleve->id, $annee->id);

        return AcademicResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->first();
    }

    protected function getAcademicSubjectResult(Eleve $eleve, int $matiereId, AnneeAcademique $annee, ?int $semestre): ?AcademicSubjectResult
    {
        $period = AcademicResult::periodFromSemestre($semestre);

        $result = AcademicSubjectResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('matiere_id', $matiereId)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->first();

        if ($result) {
            return $result;
        }

        $this->projector->rebuildStudentYear($eleve->id, $annee->id);

        return AcademicSubjectResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('matiere_id', $matiereId)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->first();
    }

    protected function getAcademicSubjectResultsForBulletin(Eleve $eleve, AnneeAcademique $annee, int $semestre, int $expectedCount): Collection
    {
        $period = AcademicResult::periodFromSemestre($semestre);

        $results = AcademicSubjectResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->get()
            ->keyBy('matiere_id');

        if ($results->count() >= $expectedCount) {
            return $results;
        }

        $this->projector->rebuildStudentYear($eleve->id, $annee->id);

        return AcademicSubjectResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $period)
            ->get()
            ->keyBy('matiere_id');
    }

    protected function applyRanks(Collection $classement): Collection
    {
        $currentRank = 0;
        $lastMoyenne = null;
        $skip = 0;

        return $classement->map(function (array $item) use (&$currentRank, &$lastMoyenne, &$skip) {
            if ($item['moyenne'] === $lastMoyenne) {
                $skip++;
            } else {
                $currentRank += 1 + $skip;
                $skip = 0;
            }

            $lastMoyenne = $item['moyenne'];
            $item['rang'] = $currentRank;

            return $item;
        });
    }
}
