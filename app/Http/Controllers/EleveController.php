<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEleveRequest;
use App\Http\Requests\UpdateEleveRequest;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EleveController extends Controller
{
    // --- Liste ---

    public function index()
    {
        $date  = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

        if ($annee) {
            $eleveIds = Inscription::where('annee_academique_id', $annee->id)
                ->distinct('eleve_id')
                ->pluck('eleve_id');

            $eleves = Eleve::whereIn('id', $eleveIds)
                ->orderBy('nom')
                ->paginate(20);
        } else {
            $eleves = Eleve::orderBy('nom')->paginate(20);
        }

        // Attacher la classe résolue à chaque élève
        $eleves->getCollection()->transform(function (Eleve $eleve) use ($date) {
            $eleve->resolved_classe = $eleve->classeForDate($date);
            return $eleve;
        });

        return view('eleves.index', compact('eleves', 'annee'));
    }

    // --- Formulaire de création ---

    public function create()
    {
        $annee   = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();

        return view('eleves.create', compact('classes', 'annee'));
    }

    // --- Enregistrement ---

    public function store(StoreEleveRequest $request)
    {
        // Résoudre l'année académique active
        $annee = currentAcademicYear()
            ?? AnneeAcademique::forDate(now()->toDateString(), true);

        if (! $annee) {
            return back()
                ->withInput()
                ->withErrors(['general' => "Aucune année académique active. Veuillez en créer une d'abord."]);
        }

        DB::transaction(function () use ($request, $annee) {
            // 1. Créer l'élève (sans classe_id)
            $eleve = Eleve::create($request->only([
                'matricule', 'nom', 'prenom', 'date_naissance', 'sexe',
            ]));

            // 2. Créer l'inscription liée à l'année académique
            Inscription::create([
                'eleve_id'            => $eleve->id,
                'classe_id'           => $request->classe_id,
                'annee_academique_id' => $annee->id,
            ]);
        });

        return redirect()
            ->route('eleves.index')
            ->with('success', "Élève inscrit avec succès pour l'année " . $annee->libelle . ".");
    }

    // --- Affichage ---

    public function show(Eleve $eleve)
    {
        $date  = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

        $eleve->load([
            'notes' => function ($q) use ($annee) {
                if ($annee) {
                    $q->where('annee_academique_id', $annee->id);
                }
                $q->with('matiere');
            },
        ]);

        $eleve->resolved_classe = $eleve->classeForDate($date);

        return view('eleves.show', compact('eleve'));
    }

    // --- Formulaire d'édition ---

    public function edit(Eleve $eleve)
    {
        $date       = request()->query('date') ?? currentAcademicDate();
        $annee      = currentAcademicYear();
        $classes    = Classe::orderBy('nom_classe')->get();
        $inscription = $eleve->inscriptionForDate($date);

        return view('eleves.edit', compact('eleve', 'classes', 'inscription', 'annee'));
    }

    // --- Mise à jour ---

    public function update(UpdateEleveRequest $request, Eleve $eleve)
    {
        $date  = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

        DB::transaction(function () use ($request, $eleve, $annee, $date) {
            // 1. Mettre à jour les infos de l'élève
            $eleve->update($request->only([
                'matricule', 'nom', 'prenom', 'date_naissance', 'sexe',
            ]));

            // 2. Mettre à jour l'inscription (changer de classe si besoin)
            if ($annee) {
                $inscription = $eleve->inscriptionForDate($date);

                if ($inscription) {
                    $inscription->update(['classe_id' => $request->classe_id]);
                } else {
                    // Créer une inscription si elle n'existe pas encore pour cette année
                    Inscription::create([
                        'eleve_id'            => $eleve->id,
                        'classe_id'           => $request->classe_id,
                        'annee_academique_id' => $annee->id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('eleves.index')
            ->with('success', 'Élève modifié avec succès.');
    }

    // --- Suppression ---

    public function destroy(Eleve $eleve)
    {
        // La suppression cascade sur inscriptions et notes (FK onDelete cascade)
        $eleve->delete();

        return redirect()
            ->route('eleves.index')
            ->with('success', 'Élève supprimé avec succès.');
    }
}
