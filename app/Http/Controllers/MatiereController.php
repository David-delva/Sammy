<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Matiere;
use App\Services\AcademicCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatiereController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
    ) {
    }

    public function index()
    {
        $annee = currentAcademicYear();
        $matieres = Matiere::withCount('classes')->orderBy('nom_matiere')->paginate(20);

        return view('matieres.index', compact('matieres', 'annee'));
    }

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

    public function assignerIndex()
    {
        $annee = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();

        return view('matieres.assigner', compact('annee', 'classes'));
    }

    public function assignerClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
        ]);

        $annee = currentAcademicYear();
        $classe = Classe::findOrFail($request->classe_id);
        $classes = Classe::orderBy('nom_classe')->get();

        $matieresAssignees = $annee
            ? $classe->matieresForAnnee($annee->id)->get()->keyBy('id')
            : collect();

        $toutesLesMatieres = Matiere::orderBy('nom_matiere')->get();

        return view('matieres.assigner', compact(
            'annee',
            'classes',
            'classe',
            'matieresAssignees',
            'toutesLesMatieres'
        ));
    }

    public function assignerSauvegarder(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matieres' => 'array',
            'matieres.*.id' => 'required|exists:matieres,id',
            'matieres.*.coef' => 'required|integer|min:1|max:20',
        ]);

        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->withErrors(['general' => 'Aucune année académique active.']);
        }

        $classe = Classe::findOrFail($request->classe_id);

        DB::transaction(function () use ($request, $classe, $annee) {
            DB::table('classe_matiere')
                ->where('classe_id', $classe->id)
                ->where('annee_academique_id', $annee->id)
                ->delete();

            $lignes = [];
            foreach ($request->matieres ?? [] as $item) {
                $lignes[] = [
                    'classe_id' => $classe->id,
                    'matiere_id' => $item['id'],
                    'annee_academique_id' => $annee->id,
                    'coefficient' => $item['coef'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($lignes)) {
                DB::table('classe_matiere')->insert($lignes);
            }
        });

        $this->academicCache->bust();

        return redirect()
            ->route('matieres.assigner.classe', ['classe_id' => $classe->id])
            ->with('success', "Matières de {$classe->nom_classe} mises à jour pour {$annee->libelle}.");
    }
}