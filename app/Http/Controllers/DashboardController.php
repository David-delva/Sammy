<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Note;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $annee = currentAcademicYear();
        $anneeId = $annee ? $annee->id : 'global';

        $stats = Cache::remember("dashboard_stats_annee_{$anneeId}", 300, function () use ($annee) {
            if ($annee) {
                $elevesIds = Inscription::where('annee_academique_id', $annee->id)->distinct('eleve_id')->pluck('eleve_id');
                
                return [
                    'total_eleves' => $elevesIds->count(),
                    'total_classes' => Classe::count(),
                    'total_notes'   => Note::where('annee_academique_id', $annee->id)->count(),
                    'moyenne_generale' => round(Note::where('annee_academique_id', $annee->id)->avg('note'), 2)
                ];
            }

            return [
                'total_eleves' => Eleve::count(),
                'total_classes' => Classe::count(),
                'total_notes'   => Note::count(),
                'moyenne_generale' => round(Note::avg('note'), 2)
            ];
        });

        return view('dashboard', compact('stats', 'annee'));
    }
}
