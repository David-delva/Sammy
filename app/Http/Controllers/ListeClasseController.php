<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ListeClasseController extends Controller
{
    public function generatePdf(Classe $classe)
    {
        $annee = currentAcademicYear();
        if (!$annee) {
            return back()->with('error', 'Aucune année académique active.');
        }

        // Récupérer les élèves inscrits dans cette classe pour cette année
        $eleves = Inscription::where('classe_id', $classe->id)
            ->where('annee_academique_id', $annee->id)
            ->with('eleve')
            ->get()
            ->map(fn($ins) => $ins->eleve)
            ->sortBy('nom');

        // Récupérer les matières de la classe
        $matieres = Matiere::where('classe_id', $classe->id)->get();

        $pdf = Pdf::loadView('classes.liste-pdf', compact('classe', 'eleves', 'matieres', 'annee'))
            ->setPaper('a4', 'landscape'); // Format paysage pour la feuille d'appel

        return $pdf->download("liste_classe_{$classe->nom_classe}_{$annee->libelle}.pdf");
    }
}
