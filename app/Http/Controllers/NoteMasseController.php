<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasseNoteRequest;
use App\Models\Classe;
use App\Models\Note;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NoteMasseController extends Controller
{
    public function index(Request $request)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return redirect()->route('annees.index')->with('error', 'Creez une annee active.');
        }

        $classes = Classe::orderBy('nom_classe')->get();
        $matieres = collect();
        $eleves = collect();

        $selectedClasse = $request->query('classe_id');
        $selectedMatiere = $request->query('matiere_id');
        $selectedType = $request->query('type_devoir');
        $selectedSemestre = in_array((int) $request->query('semestre'), array_keys(Note::semestreOptions()), true)
            ? (int) $request->query('semestre')
            : null;

        if ($selectedClasse) {
            $matieres = DB::table('classe_matiere')
                ->where('classe_id', $selectedClasse)
                ->where('annee_academique_id', $annee->id)
                ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
                ->select('matieres.id', 'matieres.nom_matiere')
                ->orderBy('matieres.nom_matiere')
                ->get();
        }

        if ($selectedClasse && $selectedMatiere && $selectedType && $selectedSemestre) {
            $eleves = Inscription::with([
                'eleve',
                'notes' => function ($query) use ($selectedMatiere, $selectedType, $selectedSemestre, $annee) {
                    $query->where('matiere_id', $selectedMatiere)
                        ->where('type_devoir', $selectedType)
                        ->where('semestre', $selectedSemestre)
                        ->where('annee_academique_id', $annee->id);
                },
            ])
                ->where('classe_id', $selectedClasse)
                ->where('annee_academique_id', $annee->id)
                ->get()
                ->sortBy(fn ($inscription) => $inscription->eleve->nom);
        }

        return view('notes.masse', compact(
            'classes',
            'matieres',
            'eleves',
            'selectedClasse',
            'selectedMatiere',
            'selectedType',
            'selectedSemestre',
            'annee'
        ));
    }

    public function store(StoreMasseNoteRequest $request)
    {
        $annee = currentAcademicYear();

        DB::transaction(function () use ($request, $annee) {
            foreach ($request->notes as $eleveId => $valeur) {
                if ($valeur === null || $valeur === '') {
                    continue;
                }

                Note::updateOrCreate(
                    [
                        'eleve_id' => $eleveId,
                        'matiere_id' => $request->matiere_id,
                        'type_devoir' => $request->type_devoir,
                        'annee_academique_id' => $annee->id,
                        'semestre' => (int) $request->semestre,
                    ],
                    ['note' => $valeur]
                );

                $this->forgetNoteCaches((int) $eleveId, (int) $request->matiere_id, $annee->id);
            }
        });

        return redirect()->back()->with('success', 'Les notes ont ete enregistrees avec succes.');
    }

    protected function forgetNoteCaches(int $eleveId, int $matiereId, int $anneeId): void
    {
        $keys = [
            "moyenne:eleve:{$eleveId}:matiere:{$matiereId}:annee:{$anneeId}:annuel",
            "moyenne:eleve:{$eleveId}:matiere:{$matiereId}:annee:{$anneeId}:semestre:1",
            "moyenne:eleve:{$eleveId}:matiere:{$matiereId}:annee:{$anneeId}:semestre:2",
            "moyenne_generale:eleve:{$eleveId}:annee:{$anneeId}:annuel",
            "moyenne_generale:eleve:{$eleveId}:annee:{$anneeId}:semestre:1",
            "moyenne_generale:eleve:{$eleveId}:annee:{$anneeId}:semestre:2",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}