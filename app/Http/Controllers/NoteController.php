<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\AcademicCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
    ) {}

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
            ->when($selectedClasse && $annee, function ($query) use ($selectedClasse, $annee) {
                return $query->whereHas('eleve.inscriptions', function ($q) use ($selectedClasse, $annee) {
                    $q->where('classe_id', $selectedClasse)
                        ->where('annee_academique_id', $annee->id);
                });
            })
            ->when($selectedMatiere, fn ($query) => $query->where('matiere_id', $selectedMatiere))
            ->when($selectedType, fn ($query) => $query->where('type_devoir', $selectedType))
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->whereHas('eleve', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('matricule', 'like', "%{$search}%");
                    })->orWhereHas('matiere', function ($q) use ($search) {
                        $q->where('nom_matiere', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc('semestre')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $classes = Classe::orderBy('nom_classe')->get();
        $matieres = Matiere::orderBy('nom_matiere')->get();

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
            return back()->with('error', 'Aucune annee academique active. Veuillez en creer une.');
        }

        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('annee_academique_id', $annee->id)
            ->get();

        $eleves = $this->mapInscriptionStudents($inscriptions);
        $matieresByClasse = $this->matieresByClasseForYear(
            (int) $annee->id,
            $inscriptions->pluck('classe_id')->unique()->values()->all()
        );
        $matieres = $this->flattenMatieresByClasse($matieresByClasse);

        return view('notes.create', compact('eleves', 'matieres', 'matieresByClasse', 'annee'));
    }

    public function store(StoreNoteRequest $request)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->with('error', "Pas d'annee active.");
        }

        $data = $request->validated();
        $data['annee_academique_id'] = $annee->id;

        Note::create($data);
        $this->forgetNoteCaches((int) $data['eleve_id'], (int) $data['matiere_id'], (int) $annee->id);

        return redirect()->route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')])
            ->with('success', 'Note ajoutee avec succes.');
    }

    public function edit(Note $note)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            $note->loadMissing('anneeAcademique');
            $noteYear = $note->anneeAcademique;
            $annee = $noteYear instanceof AnneeAcademique ? $noteYear : null;
        }

        $contextYearId = $annee instanceof AnneeAcademique
            ? (int) $annee->id
            : (int) $note->annee_academique_id;

        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('annee_academique_id', $contextYearId)
            ->get();

        $eleves = $this->mapInscriptionStudents($inscriptions);
        $matieresByClasse = $this->matieresByClasseForYear(
            $contextYearId,
            $inscriptions->pluck('classe_id')->unique()->values()->all()
        );
        $matieres = $this->flattenMatieresByClasse($matieresByClasse);

        return view('notes.edit', compact('note', 'eleves', 'matieres', 'matieresByClasse', 'annee'));
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $oldEleveId = (int) $note->eleve_id;
        $oldMatiereId = (int) $note->matiere_id;
        $anneeId = (int) $note->annee_academique_id;

        $note->update($request->validated());

        $this->forgetNoteCaches($oldEleveId, $oldMatiereId, $anneeId);
        $this->forgetNoteCaches((int) $note->eleve_id, (int) $note->matiere_id, $anneeId);

        return redirect()->route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')])
            ->with('success', 'Note mise a jour.');
    }

    public function destroy(Note $note)
    {
        $eleveId = (int) $note->eleve_id;
        $matiereId = (int) $note->matiere_id;
        $anneeId = (int) $note->annee_academique_id;

        $note->delete();
        $this->forgetNoteCaches($eleveId, $matiereId, $anneeId);

        return redirect()->route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')])
            ->with('success', 'Note supprimee.');
    }

    protected function forgetNoteCaches(int $eleveId, int $matiereId, int $anneeId): void
    {
        foreach ([null, Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
            $this->academicCache->forget(
                $this->academicCache->noteAverageKey($eleveId, $matiereId, $anneeId, $semestre)
            );
        }
    }

    /**
     * @param  Collection<int, Inscription>  $inscriptions
     * @return Collection<int, Eleve>
     */
    protected function mapInscriptionStudents(Collection $inscriptions): Collection
    {
        /** @var Collection<int, Eleve> $students */
        $students = collect();

        foreach ($inscriptions as $inscription) {
            $eleve = $inscription->eleve;
            $classe = $inscription->classe;

            if (! $eleve instanceof Eleve || ! $classe instanceof Classe) {
                continue;
            }

            $eleve->setAttribute('resolved_classe_id', (int) $inscription->classe_id);
            $eleve->setAttribute('resolved_classe_nom', (string) $classe->nom_classe);

            $students->push($eleve);
        }

        return $students->sortBy('nom')->values();
    }

    /**
     * @param  array<int, int>  $classeIds
     * @return array<int, array<int, array{id:int, nom_matiere:string}>>
     */
    protected function matieresByClasseForYear(int $anneeId, array $classeIds): array
    {
        if ($classeIds === []) {
            return [];
        }

        return DB::table('classe_matiere')
            ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
            ->where('classe_matiere.annee_academique_id', $anneeId)
            ->whereIn('classe_matiere.classe_id', $classeIds)
            ->orderBy('matieres.nom_matiere')
            ->get([
                'classe_matiere.classe_id',
                'matieres.id',
                'matieres.nom_matiere',
            ])
            ->groupBy('classe_id')
            ->map(fn (Collection $matieres) => $matieres->map(fn ($matiere) => [
                'id' => (int) $matiere->id,
                'nom_matiere' => (string) $matiere->nom_matiere,
            ])->values()->all())
            ->all();
    }

    /**
     * @param  array<int, array<int, array{id:int, nom_matiere:string}>>  $matieresByClasse
     * @return Collection<int, array{id:int, nom_matiere:string}>
     */
    protected function flattenMatieresByClasse(array $matieresByClasse): Collection
    {
        /** @var Collection<int, array{id:int, nom_matiere:string}> $matieres */
        $matieres = collect($matieresByClasse)->flatten(1);

        return $matieres
            ->unique(fn (array $matiere): int => $matiere['id'])
            ->map(fn (array $matiere): array => [
                'id' => (int) $matiere['id'],
                'nom_matiere' => (string) $matiere['nom_matiere'],
            ])
            ->values();
    }
}
