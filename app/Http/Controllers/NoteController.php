<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\AcademicCacheService;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
    ) {
    }

    public function index(Request $request)
    {
        $annee = currentAcademicYear();
        $selectedSemestre = in_array((int) $request->query('semestre'), array_keys(Note::semestreOptions()), true)
            ? (int) $request->query('semestre')
            : null;
        $selectedClasse = $request->query('classe');
        $selectedMatiere = $request->query('matiere');
        $selectedType = $request->query('type_devoir');
        $search = $request->query('search');

        $notes = Note::with(['eleve', 'matiere', 'anneeAcademique', 'eleve.inscriptions'])
            ->when($annee, fn ($query) => $query->where('annee_academique_id', $annee->id))
            ->when($selectedSemestre, fn ($query) => $query->where('semestre', $selectedSemestre))
            ->when($selectedClasse, function ($query) use ($selectedClasse, $annee) {
                return $query->whereHas('eleve.inscriptions', function ($q) use ($selectedClasse, $annee) {
                    $q->where('classe_id', $selectedClasse)
                        ->where('annee_academique_id', $annee?->id);
                });
            })
            ->when($selectedMatiere, fn ($query) => $query->where('matiere_id', $selectedMatiere))
            ->when($selectedType, fn ($query) => $query->where('type_devoir', $selectedType))
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('eleve', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('matricule', 'like', "%{$search}%");
                })
                    ->orWhereHas('matiere', function ($q) use ($search) {
                        $q->where('nom_matiere', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('semestre')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $classes = \App\Models\Classe::orderBy('nom_classe')->get();
        $matieres = \App\Models\Matiere::orderBy('nom_matiere')->get();

        return view('notes.index', compact(
            'notes',
            'selectedSemestre',
            'selectedClasse',
            'selectedMatiere',
            'selectedType',
            'search',
            'classes',
            'matieres'
        ));
    }

    public function create()
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->with('error', 'Aucune année académique active. Veuillez en créer une.');
        }

        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('annee_academique_id', $annee->id)
            ->get();

        $eleves = $inscriptions->map(function ($inscription) {
            $inscription->eleve->resolved_classe_id = $inscription->classe_id;
            $inscription->eleve->resolved_classe_nom = $inscription->classe->nom_classe;

            return $inscription->eleve;
        })->sortBy('nom');

        $matieres = Matiere::orderBy('nom_matiere')->get();

        return view('notes.create', compact('eleves', 'matieres', 'annee'));
    }

    public function store(StoreNoteRequest $request)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->with('error', "Pas d'année active.");
        }

        $data = $request->validated();
        $data['annee_academique_id'] = $annee->id;

        Note::create($data);
        $this->forgetNoteCaches($data['eleve_id'], $data['matiere_id'], $annee->id);

        return redirect()->route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')])
            ->with('success', 'Note ajoutée avec succès.');
    }

    public function edit(Note $note)
    {
        $annee = currentAcademicYear();

        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('annee_academique_id', $annee?->id ?? $note->annee_academique_id)
            ->get();

        $eleves = $inscriptions->map(function ($inscription) {
            $inscription->eleve->resolved_classe_id = $inscription->classe_id;
            $inscription->eleve->resolved_classe_nom = $inscription->classe->nom_classe;

            return $inscription->eleve;
        })->sortBy('nom');

        $matieres = Matiere::orderBy('nom_matiere')->get();

        return view('notes.edit', compact('note', 'eleves', 'matieres', 'annee'));
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $oldEleveId = $note->eleve_id;
        $oldMatiereId = $note->matiere_id;
        $anneeId = $note->annee_academique_id;

        $note->update($request->validated());

        $this->forgetNoteCaches($oldEleveId, $oldMatiereId, $anneeId);
        $this->forgetNoteCaches($note->eleve_id, $note->matiere_id, $anneeId);

        return redirect()->route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')])
            ->with('success', 'Note mise à jour.');
    }

    public function destroy(Note $note)
    {
        $eleveId = $note->eleve_id;
        $matiereId = $note->matiere_id;
        $anneeId = $note->annee_academique_id;

        $note->delete();
        $this->forgetNoteCaches($eleveId, $matiereId, $anneeId);

        return redirect()->route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')])
            ->with('success', 'Note supprimée.');
    }

    protected function forgetNoteCaches(int $eleveId, int $matiereId, int $anneeId): void
    {
        foreach ([null, Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $this->academicCache->forget(
                $this->academicCache->noteAverageKey($eleveId, $matiereId, $anneeId, $semestre)
            );
        }
    }
}