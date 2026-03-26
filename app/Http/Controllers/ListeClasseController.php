<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Inscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ListeClasseController extends Controller
{
    public function generatePdf(Classe $classe)
    {
        $annee = currentAcademicYear();
        if (! $annee) {
            return back()->with('error', 'Aucune année académique active.');
        }

        // Récupérer les élèves inscrits dans cette classe pour cette année
        $eleves = Inscription::where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->with('eleve')
            ->get()
            ->map(fn ($ins) => $ins->eleve)
            ->sortBy('nom');

        // Récupérer les matières de la classe AVEC coefficients via classe_matiere
        $matieresData = DB::table('classe_matiere')
            ->where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->select('matieres.id', 'matieres.nom_matiere', 'classe_matiere.coefficient')
            ->get();

        $pdf = Pdf::loadView('classes.liste-pdf', compact('classe', 'eleves', 'matieresData', 'annee'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("liste_classe_{$classe->nom_classe}_{$annee->libelle}.pdf");
    }
}
