<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;

class EleveController extends Controller
{
    public function index()
    {
        $date = request()->query('date') ?? (function_exists('currentAcademicDate') ? currentAcademicDate() : (session('academic_year_date') ?? now()->toDateString()));

        // Resolve academic year safely
        if (function_exists('currentAcademicYear')) {
            $annee = currentAcademicYear();
        } elseif (app()->bound('currentAcademicYear')) {
            $annee = app('currentAcademicYear');
        } else {
            $annee = \App\Models\AnneeAcademique::getActiveByDate($date);
        }

        if ($annee) {
            $eleveIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('eleve_id')->pluck('eleve_id');
            $eleves = Eleve::whereIn('id', $eleveIds)->paginate(20);
            // attach resolved_classe to paginated collection
            $eleves->getCollection()->transform(function($e) use ($date) {
                $e->resolved_classe = $e->classeForDate($date);
                return $e;
            });
        } else {
            $eleves = Eleve::paginate(20);
            $eleves->getCollection()->transform(function($e) use ($date) {
                $e->resolved_classe = $e->classeForDate($date);
                return $e;
            });
        }

        return view('eleves.index', compact('eleves'));
    }

    public function create()
    {
        $classes = Classe::all();
        return view('eleves.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:eleves|max:255',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'classe_id' => 'required|exists:classes,id',
        ]);

        Eleve::create($validated);
        return redirect()->route('eleves.index')->with('success', 'Élève inscrit avec succès.');
    }

    public function show(Eleve $eleve)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();
        $eleve->load(['notes' => function($q) use ($annee) {
            if ($annee) $q->where('annee_academique_id', $annee->id);
        }, 'notes.matiere']);
        $eleve->resolved_classe = $eleve->classeForDate($date);
        return view('eleves.show', compact('eleve'));
    }

    public function edit(Eleve $eleve)
    {
        $classes = Classe::all();
        return view('eleves.edit', compact('eleve', 'classes'));
    }

    public function update(Request $request, Eleve $eleve)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:eleves,matricule,' . $eleve->id . '|max:255',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $eleve->update($validated);
        return redirect()->route('eleves.index')->with('success', 'Élève modifié avec succès.');
    }

    public function destroy(Eleve $eleve)
    {
        $eleve->delete();
        return redirect()->route('eleves.index')->with('success', 'Élève supprimé avec succès.');
    }
}
