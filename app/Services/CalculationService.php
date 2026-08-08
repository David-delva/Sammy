<?php

namespace App\Services;

use App\Models\AcademicSubjectResult;
use App\Models\AcademicUeResult;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\ResultatAnnuel;
use App\Models\ResultatSemestre;
use App\Models\Semestre;
use App\Models\Ue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        if ($semestre === null) {
            return $this->getResultatAnnuel($eleve, $annee)?->moyenne_annuelle;
        }

        return $this->getResultatSemestre($eleve, $annee, $semestre)?->moyenne_semestre;
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

        $totalEleves = Inscription::query()
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->count();

        if ($totalEleves === 0) {
            return collect();
        }

        if ($semestre === null) {
            $projectionCount = ResultatAnnuel::query()
                ->where('classe_id', $classe->id)
                ->where('annee_academique_id', $annee->id)
                ->count();

            if ($projectionCount !== $totalEleves) {
                $this->projector->rebuildClassYear($classe->id, $annee->id);
            }

            $results = ResultatAnnuel::query()
                ->with('eleve')
                ->where('classe_id', $classe->id)
                ->where('annee_academique_id', $annee->id)
                ->whereNotNull('moyenne_annuelle')
                ->orderByDesc('moyenne_annuelle')
                ->orderBy('eleve_id')
                ->get();

            return $this->applyRanks(
                $results->map(fn (ResultatAnnuel $result) => [
                    'eleve' => $result->eleve,
                    'moyenne' => $result->moyenne_annuelle,
                    'mention' => $this->getMention($result->moyenne_annuelle),
                ])->values()
            );
        }

        $semestreEntity = Semestre::pour($classe->id, $annee->id, $semestre);

        $projectionCount = ResultatSemestre::query()
            ->where('semestre_id', $semestreEntity->id)
            ->count();

        if ($projectionCount !== $totalEleves) {
            $this->projector->rebuildClassYear($classe->id, $annee->id);
        }

        $results = ResultatSemestre::query()
            ->with('eleve')
            ->where('semestre_id', $semestreEntity->id)
            ->whereNotNull('moyenne_semestre')
            ->orderByDesc('moyenne_semestre')
            ->orderBy('eleve_id')
            ->get();

        return $this->applyRanks(
            $results->map(fn (ResultatSemestre $result) => [
                'eleve' => $result->eleve,
                'moyenne' => $result->moyenne_semestre,
                'mention' => $this->getMention($result->moyenne_semestre),
            ])->values()
        );
    }

    public function getMention(float $moyenne): string
    {
        if ($moyenne >= 16) {
            return 'Très Bien';
        }

        if ($moyenne >= 14) {
            return 'Bien';
        }

        if ($moyenne >= 12) {
            return 'Assez Bien';
        }

        if ($moyenne >= 10) {
            return 'Passable';
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

        $semestreEntity = Semestre::where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->where('numero', $semestre)
            ->first();

        /** @var \Illuminate\Database\Eloquent\Collection<int, Ue> $ues */
        $ues = $semestreEntity
            ? Ue::query()->where('semestre_id', $semestreEntity->id)->with('matieres')->get()
            : collect();

        $ueResults = $ues->isNotEmpty()
            ? AcademicUeResult::query()
                ->where('eleve_id', $eleve->id)
                ->whereIn('ue_id', $ues->modelKeys())
                ->get()
                ->keyBy('ue_id')
            : collect();

        $lignes = [];
        $totalPoints = 0.0;
        $totalCoefficients = 0;
        $matiereToUe = [];

        foreach ($ues as $ue) {
            foreach ($ue->matieres as $matiere) {
                $matiereToUe[$matiere->id] = $ue;
            }
        }

        foreach ($matieres as $matiere) {
            $row = $subjectResults->get($matiere->id);
            $coefficient = $row ? (int) $row->coefficient : (int) $matiere->pivot->coefficient;
            $moyenneMatiere = $row?->moyenne_matiere;
            $moyXCoef = $row?->moy_x_coef;

            if ($moyenneMatiere !== null) {
                $totalPoints += $moyXCoef ?? 0;
                $totalCoefficients += $coefficient;
            }

            $ueDeLaMatiere = $matiereToUe[$matiere->id] ?? null;

            $lignes[] = [
                'matiere' => $matiere->nom_matiere,
                'coefficient' => $coefficient,
                'credits' => (int) $matiere->pivot->credits,
                'credits_acquis' => (int) ($row?->credits_acquis ?? 0),
                'moyenne_devoirs' => $this->formatNote($row?->moyenne_devoirs),
                'note_composition' => $this->formatNote($row?->note_composition),
                'rattrapage' => (bool) ($row?->rattrapage_utilise ?? false),
                'moyenne' => $this->formatNote($moyenneMatiere),
                'moy_x_coef' => $this->formatNote($moyXCoef),
                'appreciation' => $this->getAppreciation($moyenneMatiere),
                'ue' => $ueDeLaMatiere?->libelle,
                'ue_code' => $ueDeLaMatiere?->code,
            ];
        }

        // Regroupement par UE pour affichage (regles 4.2 et 4.5), si la classe a des UE configurees.
        $ueGroupes = [];

        foreach ($ues as $ue) {
            $ueResult = $ueResults->get($ue->id);

            $ueGroupes[] = [
                'code' => $ue->code,
                'libelle' => $ue->libelle,
                'credits' => $ue->credits,
                'moyenne' => $this->formatNote($ueResult?->moyenne_ue),
                'valide' => $ueResult?->valide ?? false,
                'compense' => $ueResult?->compense ?? false,
                'credits_acquis' => $ueResult?->credits_acquis ?? 0,
                'matieres' => $ue->matieres->pluck('nom_matiere')->all(),
            ];
        }

        // Moyennes de classe (par matiere et par UE), pour la colonne "Moyenne de classe" du bulletin.
        $classAverageByMatiere = $ues->isNotEmpty()
            ? DB::table('academic_subject_results')
                ->select('matiere_id', DB::raw('AVG(moyenne_matiere) as moyenne'))
                ->where('classe_id', $classe->id)
                ->where('annee_academique_id', $annee->id)
                ->where('period', $semestre)
                ->whereNotNull('moyenne_matiere')
                ->groupBy('matiere_id')
                ->pluck('moyenne', 'matiere_id')
            : collect();

        $classAverageByUe = $ues->isNotEmpty()
            ? DB::table('academic_ue_results')
                ->select('ue_id', DB::raw('AVG(moyenne_ue) as moyenne'))
                ->whereIn('ue_id', $ues->modelKeys())
                ->whereNotNull('moyenne_ue')
                ->groupBy('ue_id')
                ->pluck('moyenne', 'ue_id')
            : collect();

        $matiereCredits = $matieres->pluck('pivot.credits', 'id');
        $matiereCoefficients = $matieres->pluck('pivot.coefficient', 'id');

        $ueBulletin = $ues->map(function (Ue $ue) use ($subjectResults, $ueResults, $classAverageByMatiere, $classAverageByUe, $matiereCredits, $matiereCoefficients) {
            $ueResult = $ueResults->get($ue->id);
            $coefficientsTotal = 0;

            $matieresBulletin = $ue->matieres->map(function (Matiere $matiere) use ($subjectResults, $classAverageByMatiere, $matiereCredits, $matiereCoefficients, &$coefficientsTotal) {
                $row = $subjectResults->get($matiere->id);
                $coef = $row ? (int) $row->coefficient : (int) ($matiereCoefficients->get($matiere->id) ?? 1);
                $coefficientsTotal += $coef;

                return (object) [
                    'nom' => $matiere->nom_matiere,
                    'credit' => (int) ($matiereCredits->get($matiere->id) ?? 0),
                    'coef' => $coef,
                    'note' => $row?->moyenne_matiere,
                    'moy_classe' => $classAverageByMatiere->has($matiere->id) ? round((float) $classAverageByMatiere->get($matiere->id), 2) : null,
                ];
            });

            return (object) [
                'code' => $ue->code,
                'nom' => $ue->libelle,
                'matieres' => $matieresBulletin,
                'moyenne' => $ueResult?->moyenne_ue,
                'moy_classe' => $classAverageByUe->has($ue->id) ? round((float) $classAverageByUe->get($ue->id), 2) : null,
                'credits_acquis' => $ueResult?->credits_acquis ?? 0,
                'credits_total' => $ue->credits,
                'coefficients_total' => $coefficientsTotal,
            ];
        })->values();

        $heuresAbsenceTotal = (int) DB::table('academic_subject_results')
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $semestre)
            ->sum('heures_absence');

        $moyenneGeneraleClasse = $semestreEntity
            ? DB::table('resultat_semestres')
                ->where('semestre_id', $semestreEntity->id)
                ->whereNotNull('moyenne_semestre')
                ->avg('moyenne_semestre')
            : null;

        $resultatSemestre = $this->getResultatSemestre($eleve, $annee, $semestre);
        $moyenneSemestre1 = $this->getResultatSemestre($eleve, $annee, Note::SEMESTRE_1)?->moyenne_semestre;
        $moyenneSemestre2 = $this->getResultatSemestre($eleve, $annee, Note::SEMESTRE_2)?->moyenne_semestre;
        $moyenneSelectionnee = $semestre === Note::SEMESTRE_1 ? $moyenneSemestre1 : $moyenneSemestre2;
        $moyenneAnnuelle = ($moyenneSemestre1 !== null && $moyenneSemestre2 !== null)
            ? round(($moyenneSemestre1 + $moyenneSemestre2) / 2, 2)
            : null;
        $resultatAnnuel = $this->getResultatAnnuel($eleve, $annee);

        $rangData = $this->calculateRang($eleve, $annee, $semestre);
        $semestreLibelle = $semestreEntity?->libelle ?: (string) $semestre;
        $bulletinTitre = 'Bulletin de Notes du '.$semestreLibelle;

        $creditsTotalSemestre = (int) $ues->sum('credits');

        return [
            'eleve' => $eleve,
            'classe' => $classe,
            'annee' => $annee,
            'semestre' => $semestre,
            'semestre_libelle' => $semestreLibelle,
            'bulletin_titre' => $bulletinTitre,
            'lignes' => $lignes,
            'ue_groupes' => $ueGroupes,
            'ues' => $ueBulletin,
            'total_points' => round($totalPoints, 2),
            'total_points_formatted' => $this->formatNote($totalCoefficients > 0 ? $totalPoints : null),
            'total_coefficients' => $totalCoefficients,
            'moyenne_generale' => $moyenneSelectionnee,
            'moyenne_generale_classe' => $moyenneGeneraleClasse !== null ? round((float) $moyenneGeneraleClasse, 2) : null,
            'moyenne_semestre_1' => $moyenneSemestre1,
            'moyenne_semestre_2' => $moyenneSemestre2,
            'moyenne_annuelle' => $moyenneAnnuelle,
            'heures_absence_total' => $heuresAbsenceTotal,
            'credits_acquis' => $resultatSemestre?->credits_acquis ?? 0,
            'credits_total' => $creditsTotalSemestre,
            'decision_semestre' => $resultatSemestre?->decision,
            'decision' => $this->decisionSemestreLabel($resultatSemestre?->decision, $semestreLibelle),
            'credits_acquis_annuel' => $resultatAnnuel?->credits_acquis ?? 0,
            'decision_jury' => $resultatAnnuel?->decision_jury,
            'rang' => $rangData['rang'],
            'total_eleves' => $rangData['total'],
            'mention' => $moyenneSelectionnee !== null ? $this->getMention($moyenneSelectionnee) : '',
        ];
    }

    protected function decisionSemestreLabel(?string $decision, string $semestreLibelle): string
    {
        return match ($decision) {
            ResultatSemestre::DECISION_VALIDE => $semestreLibelle.' validé',
            ResultatSemestre::DECISION_NON_VALIDE => $semestreLibelle.' non validé',
            ResultatSemestre::DECISION_ADMISSIBLE_S6 => 'Admissible S6 (soutenance en attente)',
            default => '',
        };
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

    protected function getResultatSemestre(Eleve $eleve, AnneeAcademique $annee, int $semestre): ?ResultatSemestre
    {
        $inscription = $eleve->inscriptionForAcademicYear($annee);

        if (! $inscription || ! $inscription->classe_id) {
            return null;
        }

        $semestreEntity = Semestre::pour((int) $inscription->classe_id, (int) $annee->id, $semestre);

        $result = ResultatSemestre::query()
            ->where('eleve_id', $eleve->id)
            ->where('semestre_id', $semestreEntity->id)
            ->first();

        if ($result) {
            return $result;
        }

        $this->projector->rebuildStudentYear($eleve->id, $annee->id);

        return ResultatSemestre::query()
            ->where('eleve_id', $eleve->id)
            ->where('semestre_id', $semestreEntity->id)
            ->first();
    }

    protected function getResultatAnnuel(Eleve $eleve, AnneeAcademique $annee): ?ResultatAnnuel
    {
        $result = ResultatAnnuel::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->first();

        if ($result) {
            return $result;
        }

        $this->projector->rebuildStudentYear($eleve->id, $annee->id);

        return ResultatAnnuel::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->first();
    }

    protected function getAcademicSubjectResult(Eleve $eleve, int $matiereId, AnneeAcademique $annee, ?int $semestre): ?AcademicSubjectResult
    {
        $period = $semestre ?? 0;

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
        $results = AcademicSubjectResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $semestre)
            ->get()
            ->keyBy('matiere_id');

        if ($results->count() >= $expectedCount) {
            return $results;
        }

        $this->projector->rebuildStudentYear($eleve->id, $annee->id);

        return AcademicSubjectResult::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->where('period', $semestre)
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
