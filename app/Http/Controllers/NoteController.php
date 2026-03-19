<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        // Resolve academic year safely
        if (function_exists('currentAcademicYear')) {
            $annee = currentAcademicYear();
        } elseif (app()->bound('currentAcademicYear')) {
            $annee = app('currentAcademicYear');
        } else {
            $date = request()->query('date') ?? session('academic_year_date');
            $annee = \App\Models\AnneeAcademique::getActiveByDate($date);
        }

        $query = Note::with(['eleve', 'matiere']);
        if ($annee) {
            $query->where('annee_academique_id', $annee->id);
        }

        $notes = $query->orderByDesc('id')->paginate(25);
        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

        if ($annee) {
            $eleveIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('eleve_id')->pluck('eleve_id');
            $eleves = Eleve::whereIn('id', $eleveIds)->get()->map(function($e) use ($date) {
                $e->resolved_classe = $e->classeForDate($date);
                return $e;
            });

            $classeIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('classe_id')->pluck('classe_id');
            $matieres = Matiere::with('classe')->whereIn('classe_id', $classeIds)->get();
        } else {
            $eleves = Eleve::all()->map(function($e) use ($date) {
                $e->resolved_classe = $e->classeForDate($date);
                return $e;
            });
            $matieres = Matiere::with('classe')->get();
        }
        return view('notes.create', compact('eleves', 'matieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'note' => 'required|numeric|min:0|max:20',
            'type_devoir' => 'required|in:devoir,composition',
        ]);
        // Resolve academic year from provided date query or current date
        $date = $request->input('date') ?? $request->query('date');
        $annee = \App\Models\AnneeAcademique::forDate($date, true);
        if ($annee) {
            $validated['annee_academique_id'] = $annee->id;
        }

        Note::create($validated);
        return redirect()->route('notes.index')->with('success', 'Note enregistrée avec succès.');
    }

    public function show(Note $note)
    {
        $note->load(['eleve', 'matiere']);
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

        if ($annee) {
            $eleveIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('eleve_id')->pluck('eleve_id');
            $eleves = Eleve::whereIn('id', $eleveIds)->get()->map(function($e) use ($date) {
                $e->resolved_classe = $e->classeForDate($date);
                return $e;
            });

            $classeIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('classe_id')->pluck('classe_id');
            $matieres = Matiere::with('classe')->whereIn('classe_id', $classeIds)->get();
        } else {
            $eleves = Eleve::all()->map(function($e) use ($date) {
                $e->resolved_classe = $e->classeForDate($date);
                return $e;
            });
            $matieres = Matiere::with('classe')->get();
        }
        return view('notes.edit', compact('note', 'eleves', 'matieres'));
    }

    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'note' => 'required|numeric|min:0|max:20',
            'type_devoir' => 'required|in:devoir,composition',
        ]);
        $date = $request->input('date') ?? $request->query('date');
        $annee = \App\Models\AnneeAcademique::forDate($date, true);
        if ($annee) {
            $validated['annee_academique_id'] = $annee->id;
        }

        $note->update($validated);
        return redirect()->route('notes.index')->with('success', 'Note modifiée avec succès.');
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Note supprimée avec succès.');
    }
}
