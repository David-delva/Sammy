<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\Classe;
use Illuminate\Http\Request;

class MatiereController extends Controller
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
            $classeIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('classe_id')->pluck('classe_id');
            $matieres = Matiere::with('classe')->whereIn('classe_id', $classeIds)->paginate(20);
        } else {
            $matieres = Matiere::with('classe')->paginate(20);
        }

        return view('matieres.index', compact('matieres'));
    }

    public function create()
    {
        $classes = Classe::all();
        return view('matieres.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_matiere' => 'required|string|max:255',
            'coefficient' => 'required|integer|min:1',
            'classe_id' => 'required|exists:classes,id',
        ]);

        Matiere::create($validated);
        return redirect()->route('matieres.index')->with('success', 'Matière créée avec succès.');
    }

    public function show(Matiere $matiere)
    {
        $matiere->load('classe');
        return view('matieres.show', compact('matiere'));
    }

    public function edit(Matiere $matiere)
    {
        $classes = Classe::all();
        return view('matieres.edit', compact('matiere', 'classes'));
    }

    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom_matiere' => 'required|string|max:255',
            'coefficient' => 'required|integer|min:1',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $matiere->update($validated);
        return redirect()->route('matieres.index')->with('success', 'Matière modifiée avec succès.');
    }

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();
        return redirect()->route('matieres.index')->with('success', 'Matière supprimée avec succès.');
    }
}
