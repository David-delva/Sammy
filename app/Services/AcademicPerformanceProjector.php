<?php

namespace App\Services;

use App\Models\AcademicSubjectResult;
use App\Models\AcademicUeResult;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\PenaliteAbsence;
use App\Models\ResultatAnnuel;
use App\Models\ResultatSemestre;
use App\Models\Semestre;
use App\Models\Ue;
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

        $semestreS1 = Semestre::pour($classe->id, $anneeId, Note::SEMESTRE_1);
        $semestreS2 = Semestre::pour($classe->id, $anneeId, Note::SEMESTRE_2);
        $semestreByNumero = [
            Note::SEMESTRE_1 => $semestreS1,
            Note::SEMESTRE_2 => $semestreS2,
        ];

        /** @var EloquentCollection<int, Ue> $ues */
        $ues = Ue::query()
            ->whereIn('semestre_id', [$semestreS1->id, $semestreS2->id])
            ->with('matieres')
            ->get();

        $ueParSemestre = [
            Note::SEMESTRE_1 => $ues->where('semestre_id', $semestreS1->id)->values(),
            Note::SEMESTRE_2 => $ues->where('semestre_id', $semestreS2->id)->values(),
        ];

        $tauxAbsence = PenaliteAbsence::tauxPour($classe->id, $anneeId);
        $soutenanceUe = $ueParSemestre[Note::SEMESTRE_2]->first(
            fn (Ue $ue) => $ue->matieres->contains(fn (Matiere $m) => str_contains(mb_strtolower($m->nom_matiere), 'soutenance'))
        );

        $noteStats = [
            Note::SEMESTRE_1 => $this->aggregateNotes($eleveId, $anneeId, Note::SEMESTRE_1),
            Note::SEMESTRE_2 => $this->aggregateNotes($eleveId, $anneeId, Note::SEMESTRE_2),
        ];
        $absenceStats = [
            Note::SEMESTRE_1 => $this->aggregateAbsences($eleveId, $anneeId, Note::SEMESTRE_1),
            Note::SEMESTRE_2 => $this->aggregateAbsences($eleveId, $anneeId, Note::SEMESTRE_2),
        ];

        // Etape 1 (regle 4.1) : moyenne par matiere, par semestre.
        $subject = [Note::SEMESTRE_1 => [], Note::SEMESTRE_2 => []];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            foreach ($matieres as $matiere) {
                $statsRow = $noteStats[$semestre]->get($matiere->id);
                $coefficient = (int) $matiere->pivot->coefficient;
                $credits = (int) $matiere->pivot->credits;
                $moyenneDevoirs = $this->roundStat($statsRow?->avg_devoir);
                $noteComposition = $this->roundStat($statsRow?->note_composition);
                $noteRattrapage = $this->roundStat($statsRow?->note_rattrapage);
                $notesCount = $statsRow ? (int) $statsRow->total_notes : 0;
                $heuresAbsence = (int) ($absenceStats[$semestre]->get($matiere->id)?->heures ?? 0);

                $base = $this->calculateSubjectAverage($moyenneDevoirs, $noteComposition);
                $rattrapageUtilise = $noteRattrapage !== null;
                $moyenne = $rattrapageUtilise ? $noteRattrapage : $base;

                if ($moyenne !== null && $heuresAbsence > 0) {
                    $moyenne = max(0.0, round($moyenne - $heuresAbsence * $tauxAbsence, 2));
                }

                $subject[$semestre][$matiere->id] = [
                    'coefficient' => $coefficient,
                    'credits' => $credits,
                    'moyenne_devoirs' => $moyenneDevoirs,
                    'note_composition' => $noteComposition,
                    'note_rattrapage' => $noteRattrapage,
                    'rattrapage_utilise' => $rattrapageUtilise,
                    'moyenne_matiere' => $moyenne,
                    'total_notes' => $notesCount,
                    'heures_absence' => $heuresAbsence,
                    'last_recorded_at' => $statsRow?->last_recorded_at,
                ];
            }
        }

        // Totaux "a plat" par semestre (informatifs, affichage uniquement).
        $totalPointsFlat = [];
        $totalCoefFlat = [];
        $evaluatedSubjects = [];
        $totalNotesSem = [];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $totalPointsFlat[$semestre] = 0.0;
            $totalCoefFlat[$semestre] = 0;
            $evaluatedSubjects[$semestre] = 0;
            $totalNotesSem[$semestre] = 0;

            foreach ($matieres as $matiere) {
                $row = $subject[$semestre][$matiere->id];
                $totalNotesSem[$semestre] += $row['total_notes'];

                if ($row['moyenne_matiere'] !== null) {
                    $totalPointsFlat[$semestre] += $row['moyenne_matiere'] * $row['coefficient'];
                    $totalCoefFlat[$semestre] += $row['coefficient'];
                    $evaluatedSubjects[$semestre]++;
                }
            }
        }

        // Etape 2 (regle 4.2) : moyenne par UE, par semestre.
        $ueResultsBySemestre = [Note::SEMESTRE_1 => [], Note::SEMESTRE_2 => []];
        $matiereUeMap = [Note::SEMESTRE_1 => [], Note::SEMESTRE_2 => []];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            foreach ($ueParSemestre[$semestre] as $ue) {
                $totalPointsMatiere = 0.0;
                $totalCoefMatiere = 0;

                foreach ($ue->matieres as $matiere) {
                    $matiereUeMap[$semestre][$matiere->id] = $ue;
                    $row = $subject[$semestre][$matiere->id] ?? null;

                    if ($row && $row['moyenne_matiere'] !== null) {
                        $totalPointsMatiere += $row['moyenne_matiere'] * $row['coefficient'];
                        $totalCoefMatiere += $row['coefficient'];
                    }
                }

                $moyenneUe = $totalCoefMatiere > 0 ? round($totalPointsMatiere / $totalCoefMatiere, 2) : null;

                $ueResultsBySemestre[$semestre][$ue->id] = [
                    'ue' => $ue,
                    'moyenne_ue' => $moyenneUe,
                    'total_matieres' => $ue->matieres->count(),
                    'valide' => false,
                    'compense' => false,
                    'credits_acquis' => 0,
                ];
            }
        }

        // Etape 3 (regle 4.3) : moyenne de semestre = moyenne des UE ponderee par leurs credits.
        // A defaut d'UE configurees pour la classe/annee, on retombe sur la moyenne ponderee classique par matiere.
        $semestreMoyennes = [];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            if ($ueParSemestre[$semestre]->isNotEmpty()) {
                $totalPointsUe = 0.0;
                $totalCreditsUe = 0;

                foreach ($ueResultsBySemestre[$semestre] as $data) {
                    if ($data['moyenne_ue'] !== null) {
                        $totalPointsUe += $data['moyenne_ue'] * $data['ue']->credits;
                        $totalCreditsUe += $data['ue']->credits;
                    }
                }

                $semestreMoyennes[$semestre] = $totalCreditsUe > 0 ? round($totalPointsUe / $totalCreditsUe, 2) : null;
            } else {
                $semestreMoyennes[$semestre] = $totalCoefFlat[$semestre] > 0
                    ? round($totalPointsFlat[$semestre] / $totalCoefFlat[$semestre], 2)
                    : null;
            }
        }

        // Etape 4 (regle 4.5) : validation des UE (acquisition directe ou par compensation).
        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $moyenneSemestre = $semestreMoyennes[$semestre];

            foreach ($ueResultsBySemestre[$semestre] as $ueId => $data) {
                $moyenneUe = $data['moyenne_ue'];

                if ($moyenneUe !== null && $moyenneUe >= 10) {
                    $data['valide'] = true;
                    $data['compense'] = false;
                } elseif ($moyenneUe !== null && $moyenneSemestre !== null && $moyenneSemestre >= 10) {
                    $data['valide'] = true;
                    $data['compense'] = true;
                }

                $data['credits_acquis'] = $data['valide'] ? $data['ue']->credits : 0;
                $ueResultsBySemestre[$semestre][$ueId] = $data;
            }
        }

        // Etape 5 (regle 4.6) : validation du semestre.
        $decisionsSemestre = [];
        $creditsAcquisSemestre = [];
        $totalCreditsSemestre = [];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $ueRows = $ueResultsBySemestre[$semestre];
            $totalCredits = (int) $ueParSemestre[$semestre]->sum('credits');
            $creditsAcquis = (int) collect($ueRows)->sum('credits_acquis');

            $totalCreditsSemestre[$semestre] = $totalCredits;
            $creditsAcquisSemestre[$semestre] = $creditsAcquis;

            if ($totalCredits === 0) {
                // Pas d'UE configuree pour ce semestre : aucune decision de validation n'est calculee.
                $decisionsSemestre[$semestre] = null;

                continue;
            }

            if ($creditsAcquis >= $totalCredits) {
                $decisionsSemestre[$semestre] = ResultatSemestre::DECISION_VALIDE;

                continue;
            }

            if ($semestre === Note::SEMESTRE_2 && $soutenanceUe) {
                $ueNonAcquisesSaufSoutenance = collect($ueRows)
                    ->reject(fn ($row) => $row['valide'] || $row['ue']->id === $soutenanceUe->id);

                $soutenanceRow = $ueRows[$soutenanceUe->id] ?? null;
                $soutenanceNonNotee = $soutenanceUe->matieres->every(
                    fn (Matiere $m) => ($subject[Note::SEMESTRE_2][$m->id]['moyenne_matiere'] ?? null) === null
                );

                if ($ueNonAcquisesSaufSoutenance->isEmpty() && $soutenanceRow && ! $soutenanceRow['valide'] && $soutenanceNonNotee) {
                    $decisionsSemestre[$semestre] = ResultatSemestre::DECISION_ADMISSIBLE_S6;

                    continue;
                }
            }

            $decisionsSemestre[$semestre] = ResultatSemestre::DECISION_NON_VALIDE;
        }

        // Regle 4.4 : moyenne annuelle = moyenne des deux semestres.
        $moyenneS1 = $semestreMoyennes[Note::SEMESTRE_1];
        $moyenneS2 = $semestreMoyennes[Note::SEMESTRE_2];
        $moyenneAnnuelle = ($moyenneS1 !== null && $moyenneS2 !== null)
            ? round(($moyenneS1 + $moyenneS2) / 2, 2)
            : ($moyenneS1 ?? $moyenneS2);

        $creditsAcquisAnnuel = $creditsAcquisSemestre[Note::SEMESTRE_1] + $creditsAcquisSemestre[Note::SEMESTRE_2];
        $totalCreditsAnnuel = $totalCreditsSemestre[Note::SEMESTRE_1] + $totalCreditsSemestre[Note::SEMESTRE_2];

        // Regle 4.7 : decision annuelle du jury.
        $decisionJury = null;

        if ($totalCreditsAnnuel > 0) {
            if ($creditsAcquisAnnuel >= $totalCreditsAnnuel) {
                $decisionJury = ResultatAnnuel::DECISION_DIPLOME;
            } elseif (
                $soutenanceUe
                && ! ($ueResultsBySemestre[Note::SEMESTRE_2][$soutenanceUe->id]['valide'] ?? false)
                && $creditsAcquisAnnuel === $totalCreditsAnnuel - $soutenanceUe->credits
            ) {
                $decisionJury = ResultatAnnuel::DECISION_REPRISE_SOUTENANCE;
            } else {
                $decisionJury = ResultatAnnuel::DECISION_REDOUBLE;
            }
        }

        $mention = $moyenneAnnuelle !== null ? $this->mentionPour($moyenneAnnuelle) : null;

        // --- Construction des lignes a persister ---
        $timestamp = now();
        $subjectRows = [];
        $creditsAcquisMatiereBySem = [Note::SEMESTRE_1 => [], Note::SEMESTRE_2 => []];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            foreach ($matieres as $matiere) {
                $row = $subject[$semestre][$matiere->id];
                $moyXCoef = $row['moyenne_matiere'] !== null ? round($row['moyenne_matiere'] * $row['coefficient'], 2) : null;

                $ueDeLaMatiere = $matiereUeMap[$semestre][$matiere->id] ?? null;
                $creditsAcquisMatiere = 0;

                if ($ueDeLaMatiere && ($ueResultsBySemestre[$semestre][$ueDeLaMatiere->id]['valide'] ?? false)) {
                    $creditsAcquisMatiere = $row['credits'];
                }

                $creditsAcquisMatiereBySem[$semestre][$matiere->id] = $creditsAcquisMatiere;

                $subjectRows[] = [
                    'eleve_id' => $eleveId,
                    'classe_id' => $inscription->classe_id,
                    'annee_academique_id' => $anneeId,
                    'matiere_id' => $matiere->id,
                    'period' => $semestre,
                    'coefficient' => $row['coefficient'],
                    'total_notes' => $row['total_notes'],
                    'moyenne_devoirs' => $row['moyenne_devoirs'],
                    'note_composition' => $row['note_composition'],
                    'note_rattrapage' => $row['note_rattrapage'],
                    'rattrapage_utilise' => $row['rattrapage_utilise'],
                    'moyenne_matiere' => $row['moyenne_matiere'],
                    'moy_x_coef' => $moyXCoef,
                    'credits_acquis' => $creditsAcquisMatiere,
                    'heures_absence' => $row['heures_absence'],
                    'last_recorded_at' => $row['last_recorded_at'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        // Ligne annuelle par matiere : moyenne des deux semestres (regle 4.4 appliquee au niveau matiere).
        foreach ($matieres as $matiere) {
            $rowS1 = $subject[Note::SEMESTRE_1][$matiere->id];
            $rowS2 = $subject[Note::SEMESTRE_2][$matiere->id];
            $m1 = $rowS1['moyenne_matiere'];
            $m2 = $rowS2['moyenne_matiere'];
            $moyenneAnnuelleMatiere = ($m1 !== null && $m2 !== null) ? round(($m1 + $m2) / 2, 2) : ($m1 ?? $m2);
            $coefficient = $rowS1['coefficient'];
            $moyXCoef = $moyenneAnnuelleMatiere !== null ? round($moyenneAnnuelleMatiere * $coefficient, 2) : null;
            $lastRecordedAt = $rowS1['last_recorded_at'] ?? $rowS2['last_recorded_at'];

            if ($rowS1['last_recorded_at'] && $rowS2['last_recorded_at']) {
                $lastRecordedAt = strcmp((string) $rowS1['last_recorded_at'], (string) $rowS2['last_recorded_at']) >= 0
                    ? $rowS1['last_recorded_at']
                    : $rowS2['last_recorded_at'];
            }

            $subjectRows[] = [
                'eleve_id' => $eleveId,
                'classe_id' => $inscription->classe_id,
                'annee_academique_id' => $anneeId,
                'matiere_id' => $matiere->id,
                'period' => 0,
                'coefficient' => $coefficient,
                'total_notes' => $rowS1['total_notes'] + $rowS2['total_notes'],
                'moyenne_devoirs' => null,
                'note_composition' => null,
                'note_rattrapage' => null,
                'rattrapage_utilise' => $rowS1['rattrapage_utilise'] || $rowS2['rattrapage_utilise'],
                'moyenne_matiere' => $moyenneAnnuelleMatiere,
                'moy_x_coef' => $moyXCoef,
                'credits_acquis' => $creditsAcquisMatiereBySem[Note::SEMESTRE_1][$matiere->id] + $creditsAcquisMatiereBySem[Note::SEMESTRE_2][$matiere->id],
                'heures_absence' => $rowS1['heures_absence'] + $rowS2['heures_absence'],
                'last_recorded_at' => $lastRecordedAt,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $resultatSemestreRows = [];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $resultatSemestreRows[] = [
                'eleve_id' => $eleveId,
                'semestre_id' => $semestreByNumero[$semestre]->id,
                'classe_id' => $inscription->classe_id,
                'total_notes' => $totalNotesSem[$semestre],
                'evaluated_subjects' => $evaluatedSubjects[$semestre],
                'total_points' => $totalCoefFlat[$semestre] > 0 ? round($totalPointsFlat[$semestre], 2) : null,
                'total_coefficients' => $totalCoefFlat[$semestre],
                'moyenne_semestre' => $semestreMoyennes[$semestre],
                'credits_acquis' => $creditsAcquisSemestre[$semestre],
                'credits_total' => $totalCreditsSemestre[$semestre],
                'decision' => $decisionsSemestre[$semestre],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $totalNotesAnnuel = $totalNotesSem[Note::SEMESTRE_1] + $totalNotesSem[Note::SEMESTRE_2];
        $totalCoefAnnuel = $totalCoefFlat[Note::SEMESTRE_1] + $totalCoefFlat[Note::SEMESTRE_2];
        $totalPointsAnnuel = $totalPointsFlat[Note::SEMESTRE_1] + $totalPointsFlat[Note::SEMESTRE_2];
        $evaluatedSubjectsAnnuel = $matieres->filter(function (Matiere $matiere) use ($subject) {
            $m1 = $subject[Note::SEMESTRE_1][$matiere->id]['moyenne_matiere'];
            $m2 = $subject[Note::SEMESTRE_2][$matiere->id]['moyenne_matiere'];

            return $m1 !== null || $m2 !== null;
        })->count();

        $resultatAnnuelRow = [
            'eleve_id' => $eleveId,
            'annee_academique_id' => $anneeId,
            'classe_id' => $inscription->classe_id,
            'total_notes' => $totalNotesAnnuel,
            'evaluated_subjects' => $evaluatedSubjectsAnnuel,
            'total_points' => $totalCoefAnnuel > 0 ? round($totalPointsAnnuel, 2) : null,
            'total_coefficients' => $totalCoefAnnuel,
            'moyenne_annuelle' => $moyenneAnnuelle,
            'credits_acquis' => $creditsAcquisAnnuel,
            'credits_total' => $totalCreditsAnnuel,
            'decision_jury' => $decisionJury,
            'mention' => $mention,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];

        $ueRows = [];

        foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            foreach ($ueResultsBySemestre[$semestre] as $ueId => $data) {
                $ueRows[] = [
                    'eleve_id' => $eleveId,
                    'ue_id' => $ueId,
                    'total_matieres' => $data['total_matieres'],
                    'moyenne_ue' => $data['moyenne_ue'],
                    'valide' => $data['valide'],
                    'compense' => $data['compense'],
                    'credits_acquis' => $data['credits_acquis'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        $matiereIds = $matieres->modelKeys();
        $ueIds = $ues->modelKeys();

        DB::transaction(function () use (
            $eleveId,
            $anneeId,
            $subjectRows,
            $resultatSemestreRows,
            $resultatAnnuelRow,
            $ueRows,
            $matiereIds,
            $ueIds,
            $semestreS1,
            $semestreS2
        ) {
            AcademicSubjectResult::query()
                ->where('eleve_id', $eleveId)
                ->where('annee_academique_id', $anneeId)
                ->whereNotIn('matiere_id', $matiereIds)
                ->delete();

            AcademicSubjectResult::query()->upsert(
                $subjectRows,
                ['eleve_id', 'annee_academique_id', 'matiere_id', 'period'],
                ['classe_id', 'coefficient', 'total_notes', 'moyenne_devoirs', 'note_composition', 'note_rattrapage', 'rattrapage_utilise', 'moyenne_matiere', 'moy_x_coef', 'credits_acquis', 'heures_absence', 'last_recorded_at', 'updated_at']
            );

            ResultatSemestre::query()->upsert(
                $resultatSemestreRows,
                ['eleve_id', 'semestre_id'],
                ['classe_id', 'total_notes', 'evaluated_subjects', 'total_points', 'total_coefficients', 'moyenne_semestre', 'credits_acquis', 'credits_total', 'decision', 'updated_at']
            );

            ResultatAnnuel::query()->upsert(
                [$resultatAnnuelRow],
                ['eleve_id', 'annee_academique_id'],
                ['classe_id', 'total_notes', 'evaluated_subjects', 'total_points', 'total_coefficients', 'moyenne_annuelle', 'credits_acquis', 'credits_total', 'decision_jury', 'mention', 'updated_at']
            );

            AcademicUeResult::query()
                ->where('eleve_id', $eleveId)
                ->whereHas('ue', fn ($query) => $query->whereIn('semestre_id', [$semestreS1->id, $semestreS2->id]))
                ->when($ueIds !== [], fn ($query) => $query->whereNotIn('ue_id', $ueIds), fn ($query) => $query->whereNotNull('id'))
                ->delete();

            if ($ueRows !== []) {
                AcademicUeResult::query()->upsert(
                    $ueRows,
                    ['eleve_id', 'ue_id'],
                    ['total_matieres', 'moyenne_ue', 'valide', 'compense', 'credits_acquis', 'updated_at']
                );
            }
        });
    }

    private function aggregateNotes(int $eleveId, int $anneeId, int $semestre): Collection
    {
        return DB::table('notes')
            ->select(
                'matiere_id',
                DB::raw('COUNT(*) as total_notes'),
                DB::raw("AVG(CASE WHEN type_devoir = 'devoir' THEN note END) as avg_devoir"),
                DB::raw("MAX(CASE WHEN type_devoir = 'composition' THEN note END) as note_composition"),
                DB::raw("MAX(CASE WHEN type_devoir = 'rattrapage' THEN note END) as note_rattrapage"),
                DB::raw('MAX(created_at) as last_recorded_at')
            )
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->where('semestre', $semestre)
            ->groupBy('matiere_id')
            ->get()
            ->keyBy('matiere_id');
    }

    private function aggregateAbsences(int $eleveId, int $anneeId, int $semestre): Collection
    {
        return DB::table('absences')
            ->select('matiere_id', DB::raw('SUM(heures) as heures'))
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->where('semestre', $semestre)
            ->groupBy('matiere_id')
            ->get()
            ->keyBy('matiere_id');
    }

    private function calculateSubjectAverage(?float $moyenneDevoirs, ?float $noteComposition): ?float
    {
        if ($moyenneDevoirs === null && $noteComposition === null) {
            return null;
        }

        // Regle 4.1 : si une seule note disponible, elle est retenue sans ponderation
        if ($moyenneDevoirs === null) {
            return round($noteComposition, 2);
        }

        if ($noteComposition === null) {
            return round($moyenneDevoirs, 2);
        }

        // Regle 4.1 : formule CC x 40% + Examen x 60%
        return round($moyenneDevoirs * 0.4 + $noteComposition * 0.6, 2);
    }

    private function mentionPour(float $moyenne): string
    {
        return match (true) {
            $moyenne >= 16 => 'Très Bien',
            $moyenne >= 14 => 'Bien',
            $moyenne >= 12 => 'Assez Bien',
            $moyenne >= 10 => 'Passable',
            default => 'Insuffisant',
        };
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

        ResultatAnnuel::query()
            ->where('eleve_id', $eleveId)
            ->where('annee_academique_id', $anneeId)
            ->delete();

        ResultatSemestre::query()
            ->where('eleve_id', $eleveId)
            ->whereHas('semestre', fn ($query) => $query->where('annee_academique_id', $anneeId))
            ->delete();

        AcademicUeResult::query()
            ->where('eleve_id', $eleveId)
            ->whereHas('ue.semestre', fn ($query) => $query->where('annee_academique_id', $anneeId))
            ->delete();
    }
}
