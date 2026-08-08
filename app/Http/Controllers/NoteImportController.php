<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\AcademicCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;

class NoteImportController extends Controller
{
    public function __construct(private readonly AcademicCacheService $academicCache) {}

    public function showForm()
    {
        $annee    = currentAcademicYear();
        $matieres = Matiere::orderBy('nom_matiere')->get();

        return view('notes.import', compact('annee', 'matieres'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'fichier'     => 'required|file|max:5120',
            'matiere_id'  => 'required|exists:matieres,id',
            'semestre'    => 'required|in:1,2',
            'type_devoir' => 'required|in:devoir,composition,rattrapage',
        ]);

        $annee = currentAcademicYear();
        if (! $annee) {
            return back()->withErrors(['general' => 'Aucune annee academique active.']);
        }

        $file      = $request->file('fichier');
        $extension = strtolower($file->getClientOriginalExtension());
        $path      = $file->getRealPath();

        try {
            $rows = $this->readFile($path, $extension);
        } catch (\Throwable $e) {
            return back()->withErrors(['fichier' => 'Impossible de lire le fichier : ' . $e->getMessage()]);
        }

        array_shift($rows); // ignorer l'en-tête

        $matiereId  = $request->integer('matiere_id');
        $semestre   = $request->integer('semestre');
        $typeDevoir = $request->input('type_devoir');

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        DB::transaction(function () use ($rows, $annee, $matiereId, $semestre, $typeDevoir, &$imported, &$skipped, &$errors) {
            foreach ($rows as $i => $row) {
                $line      = $i + 2;
                $matricule = trim((string) ($row[0] ?? ''));
                $noteVal   = $row[1] ?? null;

                if ($matricule === '' && $noteVal === null) {
                    continue;
                }

                if ($matricule === '') {
                    $errors[] = "Ligne {$line} : matricule manquant.";
                    $skipped++;
                    continue;
                }

                $noteVal = str_replace(',', '.', (string) $noteVal);
                if (! is_numeric($noteVal) || (float) $noteVal < 0 || (float) $noteVal > 20) {
                    $errors[] = "Ligne {$line} ({$matricule}) : note invalide « {$noteVal} » (doit etre entre 0 et 20).";
                    $skipped++;
                    continue;
                }

                $eleve = Eleve::where('matricule', $matricule)->first();
                if (! $eleve) {
                    $errors[] = "Ligne {$line} : eleve introuvable avec le matricule « {$matricule} ».";
                    $skipped++;
                    continue;
                }

                $inscrit = Inscription::where('eleve_id', $eleve->id)
                    ->where('annee_academique_id', $annee->id)
                    ->exists();

                if (! $inscrit) {
                    $errors[] = "Ligne {$line} ({$matricule}) : eleve non inscrit pour l'annee {$annee->libelle}.";
                    $skipped++;
                    continue;
                }

                Note::create([
                    'eleve_id'            => $eleve->id,
                    'matiere_id'          => $matiereId,
                    'note'                => (float) $noteVal,
                    'type_devoir'         => $typeDevoir,
                    'semestre'            => $semestre,
                    'annee_academique_id' => $annee->id,
                ]);

                foreach ([null, Note::SEMESTRE_1, Note::SEMESTRE_2] as $s) {
                    $this->academicCache->forget(
                        $this->academicCache->noteAverageKey($eleve->id, $matiereId, $annee->id, $s)
                    );
                }

                $imported++;
            }
        });

        $message = "{$imported} note(s) importee(s) avec succes.";
        if ($skipped > 0) {
            $message .= " {$skipped} ligne(s) ignoree(s).";
        }

        return redirect()
            ->route('notes.index')
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
