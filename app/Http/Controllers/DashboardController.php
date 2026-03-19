<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Inscription;
use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Resolve the current academic year (bound in AppServiceProvider)
        if (app()->bound('currentAcademicYear')) {
            $annee = app('currentAcademicYear');
        } elseif (function_exists('currentAcademicYear')) {
            $annee = currentAcademicYear();
        } else {
            $annee = AnneeAcademique::getActive();
        }

        if ($annee) {
            $cacheKey = "dashboard:stats:{$annee->id}";
            $cached = Cache::get($cacheKey);
            if ($cached) {
                [$total_classes, $total_eleves, $total_matieres, $total_notes] = $cached;
            } else {
                // Classes present in this academic year (via inscriptions)
                $total_classes = Inscription::where('annee_academique_id', $annee->id)->distinct('classe_id')->count('classe_id');

                // Unique students enrolled in this academic year
                $total_eleves = Inscription::where('annee_academique_id', $annee->id)->distinct('eleve_id')->count('eleve_id');

                // Matieres linked to classes that have inscriptions this academic year
                $classeIds = Inscription::where('annee_academique_id', $annee->id)->distinct('classe_id')->pluck('classe_id');
                $total_matieres = Matiere::whereIn('classe_id', $classeIds)->count();

                // Notes recorded for this academic year
                $total_notes = Note::where('annee_academique_id', $annee->id)->count();

                Cache::put($cacheKey, [$total_classes, $total_eleves, $total_matieres, $total_notes], 300);
            }
        } else {
            // Fallback to global counts
            $total_classes = Classe::count();
            $total_eleves = Eleve::count();
            $total_matieres = Matiere::count();
            $total_notes = Note::count();
        }

        $stats = [
            'total_classes' => $total_classes,
            'total_eleves' => $total_eleves,
            'total_matieres' => $total_matieres,
            'total_notes' => $total_notes,
        ];

        return view('dashboard', compact('stats'));
    }
}
