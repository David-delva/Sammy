<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\AnneeAcademique;
use App\Services\CalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    protected $calculationService;

    public function __construct(CalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * Génère le bulletin au format PDF.
     * Cette méthode correspond à la route /bulletin/pdf/{id}
     */
    public function generatePdf($id)
    {
        $eleve = Eleve::findOrFail($id);
        $annee = currentAcademicYear();

        if (!$annee) {
            return back()->with('error', 'Aucune année académique active.');
        }

        $inscription = $eleve->inscriptions()->where('annee_academique_id', $annee->id)->first();

        if (!$inscription) {
            return back()->with('error', "L'élève n'est pas inscrit pour cette année.");
        }

        $classe = $inscription->classe;
        $matieres = $classe->matieres;

        $results = [];
        foreach ($matieres as $matiere) {
            $results[] = [
                'matiere' => $matiere,
                'moyenne' => $this->calculationService->calculateMoyenneMatiere($eleve, $matiere, $annee)
            ];
        }

        $moyenneGenerale = $this->calculationService->calculateMoyenneGenerale($eleve, $annee);
        $rang = $this->calculationService->calculateRang($eleve, $annee);
        $totalEleves = $inscription->classe->inscriptions()->where('annee_academique_id', $annee->id)->count();
        $mention = $moyenneGenerale ? $this->calculationService->getMention($moyenneGenerale) : null;

        $pdf = Pdf::loadView('bulletins.pdf', compact(
            'eleve', 'annee', 'classe', 'results', 'moyenneGenerale', 'rang', 'totalEleves', 'mention'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("bulletin_{$eleve->nom}_{$eleve->prenom}_{$annee->libelle}.pdf");
    }

    /**
     * Affiche un aperçu (optionnel)
     */
    public function show($id)
    {
        $eleve = Eleve::findOrFail($id);
        return $this->generatePdf($id);
    }
}
