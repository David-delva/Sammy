<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;

class EleveImportController extends Controller
{
    public function showForm()
    {
        $classes = Classe::orderBy('nom_classe')->get();
        $annee   = currentAcademicYear();

        return view('eleves.import', compact('classes', 'annee'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'fichier'   => 'required|file|max:5120',
            'classe_id' => 'required|exists:classes,id',
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

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        DB::transaction(function () use ($rows, $annee, $request, &$imported, &$skipped, &$errors) {
            foreach ($rows as $i => $row) {
                $line          = $i + 2;
                $matricule     = trim((string) ($row[0] ?? ''));
                $nom           = trim((string) ($row[1] ?? ''));
                $prenom        = trim((string) ($row[2] ?? ''));
                $sexe          = strtoupper(trim((string) ($row[3] ?? '')));
                $dateNaissance = trim((string) ($row[4] ?? ''));
                $lieuNaissance = trim((string) ($row[5] ?? ''));

                if ($matricule === '' && $nom === '') {
                    continue;
                }

                if ($nom === '' || $prenom === '') {
                    $errors[] = "Ligne {$line} : nom ou prenom manquant.";
                    $skipped++;
                    continue;
                }

                if (! in_array($sexe, ['M', 'F'])) {
                    $sexe = 'M';
                }

                if ($matricule === '') {
                    $matricule = strtoupper(substr($nom, 0, 3) . substr($prenom, 0, 2)) . rand(100, 999);
                }

                $parsedDate = null;
                if ($dateNaissance !== '') {
                    try {
                        $parsedDate = \Carbon\Carbon::parse($dateNaissance)->format('Y-m-d');
                    } catch (\Exception) {
                        $parsedDate = null;
                    }
                }

                $eleve = Eleve::firstOrCreate(
                    ['matricule' => $matricule],
                    [
                        'nom'            => $nom,
                        'prenom'         => $prenom,
                        'sexe'           => $sexe,
                        'date_naissance' => $parsedDate,
                        'lieu_naissance' => $lieuNaissance ?: null,
                    ]
                );

                $alreadyInscrit = Inscription::where('eleve_id', $eleve->id)
                    ->where('annee_academique_id', $annee->id)
                    ->exists();

                if ($alreadyInscrit) {
                    $skipped++;
                    continue;
                }

                Inscription::create([
                    'eleve_id'            => $eleve->id,
                    'classe_id'           => $request->integer('classe_id'),
                    'annee_academique_id' => $annee->id,
                ]);

                $imported++;
            }
        });

        $message = "{$imported} eleve(s) importe(s) avec succes.";
        if ($skipped > 0) {
            $message .= " {$skipped} ligne(s) ignoree(s) (deja inscrit ou donnees manquantes).";
        }

        return redirect()
            ->route('eleves.index')
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
