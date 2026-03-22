<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MatiereController extends Controller
{
    // ─── Liste : matières du catalogue avec leurs liaisons ────

    public function index()
    {
        $annee    = currentAcademicYear();
        $matieres = Matiere::withCount('classes')->orderBy('nom_matiere')->paginate(20);

        return view('matieres.index', compact('matieres', 'annee'));
    }

    // ─── Catalogue seul ───────────────────────────────────────

    public function create()
    {
        $annee = currentAcademicYear();

        return view('matieres.create', compact('annee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_matiere' => 'required|string|max:100|unique:matieres,nom_matiere',
        ]);

        Matiere::create($validated);

        return redirect()->route('matieres.index')
            ->with('success', 'Matière créée avec succès.');
    }

    public function show(Matiere $matiere)
    {
        $matiere->load('classes');
        return view('matieres.show', compact('matiere'));
    }

    public function edit(Matiere $matiere)
    {
        return view('matieres.edit', compact('matiere'));
    }

    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nom_matiere' => "required|string|max:100|unique:matieres,nom_matiere,{$matiere->id}",
        ]);

        $matiere->update($validated);

        return redirect()->route('matieres.index')
            ->with('success', 'Matière modifiée.');
    }

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();

        return redirect()->route('matieres.index')
            ->with('success', 'Matière supprimée.');
    }

    // ─── Gestion des liaisons Classe × Matière × Année ────────

    public function assignerIndex()
    {
        $annee   = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();

        return view('matieres.assigner', compact('annee', 'classes'));
    }

    /**
     * Affiche les matières assignées à une classe pour une année.
     */
    public function assignerClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
        ]);

        $annee   = currentAcademicYear();
        $classe  = Classe::findOrFail($request->classe_id);
        $classes = Classe::orderBy('nom_classe')->get();

        // Matières déjà assignées à cette classe cette année
        $matieresAssignees = $annee
            ? $classe->matieresForAnnee($annee->id)->get()->keyBy('id')
            : collect();

        // Tout le catalogue
        $toutesLesMatieres = Matiere::orderBy('nom_matiere')->get();

        return view('matieres.assigner', compact(
            'annee', 'classes', 'classe',
            'matieresAssignees', 'toutesLesMatieres'
        ));
    }

    /**
     * Sauvegarde les liaisons (ajouter / modifier coefficient / retirer).
     */
    public function assignerSauvegarder(Request $request)
    {
        $request->validate([
            'classe_id'      => 'required|exists:classes,id',
            'matieres'       => 'array',
            'matieres.*.id'  => 'required|exists:matieres,id',
            'matieres.*.coef'=> 'required|integer|min:1|max:20',
        ]);

        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->withErrors(['general' => 'Aucune année académique active.']);
        }

        $classe = Classe::findOrFail($request->classe_id);

        DB::transaction(function () use ($request, $classe, $annee) {
            // Supprimer toutes les liaisons existantes pour cette classe/année
            DB::table('classe_matiere')
                ->where('classe_id', $classe->id)
                ->where('annee_academique_id', $annee->id)
                ->delete();

            // Réinsérer les liaisons cochées avec leurs coefficients
            $lignes = [];
            foreach ($request->matieres ?? [] as $item) {
                $lignes[] = [
                    'classe_id'           => $classe->id,
                    'matiere_id'          => $item['id'],
                    'annee_academique_id' => $annee->id,
                    'coefficient'         => $item['coef'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            if (! empty($lignes)) {
                DB::table('classe_matiere')->insert($lignes);
            }
        });

        // Invalider le cache des moyennes pour cette classe
        Cache::forget("dashboard:stats:{$annee->id}");

        return redirect()
            ->route('matieres.assigner.classe', ['classe_id' => $classe->id])
            ->with('success', "Matières de {$classe->nom_classe} mises à jour pour {$annee->libelle}.");
    }
}
