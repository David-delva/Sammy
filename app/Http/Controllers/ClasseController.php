<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    public function index()
    {
        $date = request()->query('date') ?? (function_exists('currentAcademicDate') ? currentAcademicDate() : (session('academic_year_date') ?? now()->toDateString()));

        if (function_exists('currentAcademicYear')) {
            $annee = currentAcademicYear();
        } elseif (app()->bound('currentAcademicYear')) {
            $annee = app('currentAcademicYear');
        } else {
            $annee = \App\Models\AnneeAcademique::getActiveByDate($date);
        }

        if ($annee) {
            $classeIds = \App\Models\Inscription::where('annee_academique_id', $annee->id)->distinct('classe_id')->pluck('classe_id');
            $classes = Classe::whereIn('id', $classeIds)->paginate(20);
            $classes->getCollection()->transform(function ($c) use ($annee) {
                $c->eleves_count = \App\Models\Inscription::where('annee_academique_id', $annee->id)->where('classe_id', $c->id)->count();

                return $c;
            });
        } else {
            $classes = Classe::withCount('eleves')->paginate(20);
        }

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_classe' => 'required|string|unique:classes|max:255',
        ]);

        Classe::create($validated);

        return redirect()->route('classes.index')->with('success', 'Classe creee avec succes.');
    }

    public function show(Classe $classe)
    {
        $annee = currentAcademicYear();

        if ($annee) {
            $classe->setRelation(
                'eleves',
                Eleve::query()
                    ->whereIn('id', Inscription::query()
                        ->where('classe_id', $classe->id)
                        ->where('annee_academique_id', $annee->id)
                        ->select('eleve_id'))
                    ->orderBy('nom')
                    ->orderBy('prenom')
                    ->get()
            );

            $classe->setRelation(
                'matieres',
                $classe->matieresForAnnee($annee->id)
                    ->orderBy('nom_matiere')
                    ->get()
            );
        } else {
            $classe->load(['eleves', 'matieres']);
        }

        return view('classes.show', compact('classe', 'annee'));
    }

    public function edit(Classe $classe)
    {
        return view('classes.edit', compact('classe'));
    }

    public function update(Request $request, Classe $classe)
    {
        $validated = $request->validate([
            'nom_classe' => 'required|string|unique:classes,nom_classe,'.$classe->id.'|max:255',
        ]);

        $classe->update($validated);

        return redirect()->route('classes.index')->with('success', 'Classe modifiee avec succes.');
    }

    public function destroy(Classe $classe)
    {
        $classe->delete();

        return redirect()->route('classes.index')->with('success', 'Classe supprimee avec succes.');
    }
}
