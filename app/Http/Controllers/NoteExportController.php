<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NoteExportController extends Controller
{
    public function export(Request $request)
    {
        $annee = currentAcademicYear();
        $selectedSemestre = in_array((int) $request->query('semestre'), array_keys(Note::semestreOptions()), true)
            ? (int) $request->query('semestre')
            : null;
        $selectedClasse = $request->query('classe');
        $selectedMatiere = $request->query('matiere');
        $selectedType = $request->query('type_devoir');
        $search = $request->query('search');

        $notes = Note::with(['eleve', 'matiere', 'eleve.inscriptions.classe'])
            ->when($annee, fn ($query) => $query->where('annee_academique_id', $annee->id))
            ->when($selectedSemestre, fn ($query) => $query->where('semestre', $selectedSemestre))
            ->when($selectedClasse && $annee, function ($query) use ($selectedClasse, $annee) {
                return $query->whereHas('eleve.inscriptions', function ($q) use ($selectedClasse, $annee) {
                    $q->where('classe_id', $selectedClasse)
                        ->where('annee_academique_id', $annee->id);
                });
            })
            ->when($selectedMatiere, fn ($query) => $query->where('matiere_id', $selectedMatiere))
            ->when($selectedType, fn ($query) => $query->where('type_devoir', $selectedType))
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->whereHas('eleve', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('matricule', 'like', "%{$search}%");
                    })->orWhereHas('matiere', function ($q) use ($search) {
                        $q->where('nom_matiere', 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('semestre')
            ->orderBy('created_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Notes');

        $headers = ['Matricule', 'Nom', 'Prenom', 'Classe', 'Matiere', 'Semestre', 'Type', 'Note', 'Date de saisie'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $row = 2;
        foreach ($notes as $note) {
            $classeNom = $annee
                ? optional($note->eleve->inscriptions->firstWhere('annee_academique_id', $annee->id))->classe?->nom_classe
                : optional($note->eleve->inscriptions->first())->classe?->nom_classe;

            $sheet->fromArray([
                $note->eleve->matricule ?? '',
                $note->eleve->nom ?? '',
                $note->eleve->prenom ?? '',
                $classeNom ?? '',
                $note->matiere->nom_matiere ?? '',
                $note->semestre_label,
                ucfirst($note->type_devoir),
                $note->note,
                optional($note->created_at)->format('Y-m-d H:i'),
            ], null, "A{$row}");
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'notes_'.($annee ? str_replace(['/', ' '], '-', $annee->libelle) : 'export').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
