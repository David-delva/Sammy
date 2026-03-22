<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NoteController extends Controller
{
    public function index()
    {
        $annee = currentAcademicYear();

        $notes = Note::with(['eleve', 'matiere', 'anneeAcademique'])
            ->when($annee, function ($q) use ($annee) {
                $q->where('annee_academique_id', $annee->id);
            })
            ->latest()
            ->paginate(30);

        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        $annee = currentAcademicYear();
        if (!$annee) {
            return back()->with('error', "Aucune année académique active. Veuillez en créer une.");
        }

        // Récupérer les élèves inscrits cette année avec leur classe résolue
        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('annee_academique_id', $annee->id)
            ->get();

        $eleves = $inscriptions->map(function ($ins) {
            $ins->eleve->resolved_classe_id = $ins->classe_id;
            $ins->eleve->resolved_classe_nom = $ins->classe->nom_classe;
            return $ins->eleve;
        })->sortBy('nom');

        // Récupérer toutes les matières (catalogue)
        $matieres = Matiere::orderBy('nom_matiere')->get();

        return view('notes.create', compact('eleves', 'matieres', 'annee'));
    }

    public function store(StoreNoteRequest $request)
    {
        $annee = currentAcademicYear();
        if (!$annee) return back()->with('error', "Pas d'année active.");

        $data = $request->validated();
        $data['annee_academique_id'] = $annee->id;

        Note::create($data);

        // Invalidation du cache de moyenne pour cet élève
        Cache::forget("moyenne:eleve:{$request->eleve_id}:matiere:{$request->matiere_id}:annee:{$annee->id}");
        Cache::forget("moyenne_generale:eleve:{$request->eleve_id}:annee:{$annee->id}");

        return redirect()->route('notes.index')->with('success', 'Note ajoutée avec succès.');
    }

    public function edit(Note $note)
    {
        $annee = currentAcademicYear();

        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('annee_academique_id', $annee->id)
            ->get();

        $eleves = $inscriptions->map(function ($ins) {
            $ins->eleve->resolved_classe_id = $ins->classe_id;
            $ins->eleve->resolved_classe_nom = $ins->classe->nom_classe;
            return $ins->eleve;
        })->sortBy('nom');

        $matieres = Matiere::orderBy('nom_matiere')->get();

        return view('notes.edit', compact('note', 'eleves', 'matieres', 'annee'));
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $annee = currentAcademicYear();
        $note->update($request->validated());

        // Invalidation du cache
        Cache::forget("moyenne:eleve:{$note->eleve_id}:matiere:{$note->matiere_id}:annee:{$annee->id}");
        Cache::forget("moyenne_generale:eleve:{$note->eleve_id}:annee:{$annee->id}");

        return redirect()->route('notes.index')->with('success', 'Note mise à jour.');
    }

    public function destroy(Note $note)
    {
        $annee = currentAcademicYear();
        $eleveId = $note->eleve_id;
        $matiereId = $note->matiere_id;
        
        $note->delete();

        // Nettoyage cache
        Cache::forget("moyenne:eleve:{$eleveId}:matiere:{$matiereId}:annee:{$annee->id}");
        Cache::forget("moyenne_generale:eleve:{$eleveId}:annee:{$annee->id}");

        return redirect()->route('notes.index')->with('success', 'Note supprimée.');
    }
}
