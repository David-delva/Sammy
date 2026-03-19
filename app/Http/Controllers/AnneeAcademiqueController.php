<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnneeAcademique;

class AnneeAcademiqueController extends Controller
{
    public function index()
    {
        $annees = AnneeAcademique::orderBy('libelle', 'desc')->get();
        return view('annees.index', compact('annees'));
    }

    public function create()
    {
        return view('annees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:9', 'unique:annee_academiques,libelle'],
            'active' => ['sometimes', 'boolean'],
        ]);

        // If active, unset previous active year
        if ($request->boolean('active')) {
            AnneeAcademique::where('active', true)->update(['active' => false]);
            $validated['active'] = true;
        } else {
            $validated['active'] = false;
        }

        AnneeAcademique::create($validated);

        return redirect()->route('annees.index')->with('success', 'Année académique créée.');
    }

    public function edit(AnneeAcademique $annee)
    {
        return view('annees.edit', compact('annee'));
    }

    public function update(Request $request, AnneeAcademique $annee)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:9', 'unique:annee_academiques,libelle,' . $annee->id],
            'active' => ['sometimes', 'boolean'],
        ]);

        if ($request->boolean('active')) {
            AnneeAcademique::where('active', true)->where('id', '!=', $annee->id)->update(['active' => false]);
            $validated['active'] = true;
        } else {
            $validated['active'] = false;
        }

        $annee->update($validated);

        return redirect()->route('annees.index')->with('success', 'Année académique mise à jour.');
    }

    public function destroy(AnneeAcademique $annee)
    {
        $annee->delete();
        return redirect()->route('annees.index')->with('success', 'Année académique supprimée.');
    }
}
