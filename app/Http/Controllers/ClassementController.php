<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Inscription;
use App\Services\CalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClassementController extends Controller
{
    protected $calculationService;

    public function __construct(CalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function index(Request $request)
    {
        $annee = currentAcademicYear();
        if (! $annee) {
            return redirect()->route('annees.index')->with('error', 'Aucune année active.');
        }

        $classes = Classe::orderBy('nom_classe')->get();
        $selectedClasseId = $request->query('classe_id');

        $classement = collect();
        $classe = null;

        if ($selectedClasseId) {
            $classe = Classe::findOrFail($selectedClasseId);
            $inscriptions = Inscription::where('classe_id', $selectedClasseId)
                ->where('annee_academique_id', $annee->id)
                ->with('eleve')
                ->get();

            foreach ($inscriptions as $ins) {
                $moyenne = $this->calculationService->calculateMoyenneGenerale($ins->eleve, $annee);
                if ($moyenne !== null) {
                    $classement->push([
                        'eleve' => $ins->eleve,
                        'moyenne' => $moyenne,
                        'mention' => $this->calculationService->getMention($moyenne),
                    ]);
                }
            }

            // Tri par moyenne décroissante
            $classement = $classement->sortByDesc('moyenne')->values();

            // Gestion du rang avec ex-aequo
            $currentRank = 0;
            $lastMoyenne = null;
            $skip = 0;

            $classement = $classement->map(function ($item, $index) use (&$currentRank, &$lastMoyenne, &$skip) {
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

        return view('classement.index', compact('classes', 'classement', 'selectedClasseId', 'classe', 'annee'));
    }

    public function exportPdf($classe_id)
    {
        $annee = currentAcademicYear();
        $classe = Classe::findOrFail($classe_id);

        $inscriptions = Inscription::where('classe_id', $classe_id)
            ->where('annee_academique_id', $annee->id)
            ->with('eleve')
            ->get();

        $classement = collect();
        foreach ($inscriptions as $ins) {
            $moyenne = $this->calculationService->calculateMoyenneGenerale($ins->eleve, $annee);
            if ($moyenne !== null) {
                $classement->push([
                    'eleve' => $ins->eleve,
                    'moyenne' => $moyenne,
                    'mention' => $this->calculationService->getMention($moyenne),
                ]);
            }
        }

        $classement = $classement->sortByDesc('moyenne')->values();
        $currentRank = 0;
        $lastMoyenne = null;
        $skip = 0;

        $classement = $classement->map(function ($item, $index) use (&$currentRank, &$lastMoyenne, &$skip) {
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

        $pdf = Pdf::loadView('classement.pdf', compact('classement', 'classe', 'annee'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("classement_{$classe->nom_classe}_{$annee->libelle}.pdf");
    }
}
