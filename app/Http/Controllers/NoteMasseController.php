<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasseNoteRequest;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NoteMasseController extends Controller
{
    public function index(Request $request)
    {
        $annee = currentAcademicYear();
        if (!$annee) return redirect()->route('annees.index')->with('error', 'Créez une année active.');

        $classes = Classe::orderBy('nom_classe')->get();
        $matieres = collect();
        $eleves = collect();

        $selectedClasse = $request->query('classe_id');
        $selectedMatiere = $request->query('matiere_id');
        $selectedType = $request->query('type_devoir');

        if ($selectedClasse) {
            $matieres = Matiere::where('classe_id', $selectedClasse)->orderBy('nom_matiere')->get();
            
            if ($selectedMatiere && $selectedType) {
                $eleves = Inscription::with(['eleve', 'notes' => function($q) use ($selectedMatiere, $selectedType, $annee) {
                    $q->where('matiere_id', $selectedMatiere)
                      ->where('type_devoir', $selectedType)
                      ->where('annee_academique_id', $annee->id);
                }])
                ->where('classe_id', $selectedClasse)
                ->where('annee_academique_id', $annee->id)
                ->get()
                ->sortBy(fn($ins) => $ins->eleve->nom);
            }
        }

        return view('notes.masse', compact('classes', 'matieres', 'eleves', 'selectedClasse', 'selectedMatiere', 'selectedType', 'annee'));
    }

    public function store(StoreMasseNoteRequest $request)
    {
        $annee = currentAcademicYear();
        
        DB::transaction(function() use ($request, $annee) {
            foreach ($request->notes as $eleveId => $valeur) {
                if ($valeur === null || $valeur === '') continue;

                Note::updateOrCreate(
                    [
                        'eleve_id'            => $eleveId,
                        'matiere_id'          => $request->matiere_id,
                        'type_devoir'         => $request->type_devoir,
                        'annee_academique_id' => $annee->id,
                    ],
                    ['note' => $valeur]
                );

                // Invalider les caches de moyenne
                Cache::forget("moyenne:eleve:{$eleveId}:matiere:{$request->matiere_id}:annee:{$annee->id}");
                Cache::forget("moyenne_generale:eleve:{$eleveId}:annee:{$annee->id}");
            }
        });

        return redirect()->back()->with('success', 'Les notes ont été enregistrées avec succès.');
    }
}
