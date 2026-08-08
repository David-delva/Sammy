<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Services\AcademicCacheService;
use App\Services\AcademicPerformanceProjector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class AbsenceImportController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
        private readonly AcademicPerformanceProjector $projector,
    ) {}

    public function showForm()
    {
        $annee = currentAcademicYear();
        $matieres = Matiere::orderBy('nom_matiere')->get();

        return view('absences.import', compact('annee', 'matieres'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|max:5120',
            'matiere_id' => 'required|exists:matieres,id',
            'semestre' => 'required|in:1,2',
        ]);

        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->withErrors(['general' => 'Aucune année académique active.']);
        }

        $file = $request->file('fichier');
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        try {
            $rows = $this->readFile($path, $extension);
        } catch (\Throwable $e) {
            return back()->withErrors(['fichier' => 'Impossible de lire le fichier : '.$e->getMessage()]);
        }

        array_shift($rows); // ignorer l'en-tête

        $matiereId = $request->integer('matiere_id');
        $semestre = $request->integer('semestre');

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $eleveIdsAffectes = [];

        DB::transaction(function () use ($rows, $annee, $matiereId, $semestre, &$imported, &$skipped, &$errors, &$eleveIdsAffectes) {
            foreach ($rows as $i => $row) {
                $line = $i + 2;
                $matricule = trim((string) ($row[0] ?? ''));
                $heuresVal = $row[1] ?? null;

                if ($matricule === '' && $heuresVal === null) {
                    continue;
                }

                if ($matricule === '') {
                    $errors[] = "Ligne {$line} : matricule manquant.";
                    $skipped++;

                    continue;
                }

                $heuresVal = trim((string) $heuresVal);

                if (! is_numeric($heuresVal) || (int) $heuresVal < 0 || (int) $heuresVal > 500) {
                    $errors[] = "Ligne {$line} ({$matricule}) : heures d'absence invalides « {$heuresVal} » (doit être un entier entre 0 et 500).";
                    $skipped++;

                    continue;
                }

                $eleve = Eleve::where('matricule', $matricule)->first();

                if (! $eleve) {
                    $errors[] = "Ligne {$line} : élève introuvable avec le matricule « {$matricule} ».";
                    $skipped++;

                    continue;
                }

                $inscrit = Inscription::where('eleve_id', $eleve->id)
                    ->where('annee_academique_id', $annee->id)
                    ->exists();

                if (! $inscrit) {
                    $errors[] = "Ligne {$line} ({$matricule}) : élève non inscrit pour l'année {$annee->libelle}.";
                    $skipped++;

                    continue;
                }

                Absence::updateOrCreate(
                    [
                        'eleve_id' => $eleve->id,
                        'matiere_id' => $matiereId,
                        'annee_academique_id' => $annee->id,
                        'semestre' => $semestre,
                    ],
                    ['heures' => (int) $heuresVal]
                );

                $eleveIdsAffectes[$eleve->id] = $eleve->id;
                $imported++;
            }
        });

        foreach ($eleveIdsAffectes as $eleveId) {
            $this->projector->rebuildStudentYear($eleveId, $annee->id);
        }

        $this->academicCache->bust();

        $message = "{$imported} absence(s) importée(s) avec succès.";

        if ($skipped > 0) {
            $message .= " {$skipped} ligne(s) ignorée(s).";
        }

        return redirect()
            ->route('absences.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    private function readFile(string $path, string $extension): array
    {
        if ($extension === 'csv') {
            $reader = new CsvReader();
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);
        } elseif ($extension === 'xls') {
            $reader = new XlsReader();
        } else {
            $reader = new XlsxReader();
        }

        $spreadsheet = $reader->load($path);

        return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
    }
}
