<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EleveExportController extends Controller
{
    public function export(Request $request)
    {
        $annee = currentAcademicYear();
        $classeFilter = $request->query('classe');

        $query = Eleve::query()->orderBy('nom')->orderBy('prenom');

        if ($annee) {
            $query->whereIn('id', Inscription::query()
                ->where('annee_academique_id', $annee->id)
                ->when($classeFilter, fn ($q) => $q->where('classe_id', $classeFilter))
                ->pluck('eleve_id'));
        }

        $eleves = $query->get();
        $classesById = Classe::all()->keyBy('id');
        $classeParEleve = $annee
            ? Inscription::where('annee_academique_id', $annee->id)->pluck('classe_id', 'eleve_id')
            : collect();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Eleves');

        $headers = ['Matricule', 'Nom', 'Prenom', 'Sexe', 'Date de naissance', 'Lieu de naissance', 'Bac', 'Provenance', 'Classe'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $row = 2;
        foreach ($eleves as $eleve) {
            $classeId = $classeParEleve->get($eleve->id);
            $sheet->fromArray([
                $eleve->matricule,
                $eleve->nom,
                $eleve->prenom,
                $eleve->sexe,
                optional($eleve->date_naissance)->format('Y-m-d'),
                $eleve->lieu_naissance,
                $eleve->bac,
                $eleve->provenance,
                $classeId ? ($classesById->get($classeId)?->nom_classe ?? '') : '',
            ], null, "A{$row}");
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'eleves_'.($annee ? str_replace(['/', ' '], '-', $annee->libelle) : 'export').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
