<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\AcademicCacheService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
    ) {
    }

    public function index()
    {
        $annee = currentAcademicYear();
        $dateParam = request()->query('date');

        $stats = $this->academicCache->remember(
            $this->academicCache->dashboardStatsKey($dateParam, $annee?->id),
            300,
            function () use ($annee) {
                if ($annee) {
                    $elevesIds = Inscription::where('annee_academique_id', $annee->id)
                        ->distinct('eleve_id')
                        ->pluck('eleve_id');

                    return [
                        'total_eleves' => $elevesIds->count(),
                        'total_classes' => Classe::count(),
                        'total_matieres' => Matiere::count(),
                        'total_notes' => Note::where('annee_academique_id', $annee->id)->count(),
                        'moyenne_generale' => round(Note::where('annee_academique_id', $annee->id)->avg('note'), 2),
                    ];
                }

                return [
                    'total_eleves' => Eleve::count(),
                    'total_classes' => Classe::count(),
                    'total_matieres' => Matiere::count(),
                    'total_notes' => Note::count(),
                    'moyenne_generale' => round(Note::avg('note'), 2),
                ];
            }
        );

        return view('dashboard', compact('stats', 'annee'));
    }
}