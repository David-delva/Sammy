@extends('layouts.app')

@section('title', 'Saisie en masse')
@section('breadcrumb', 'Evaluations / Notes / Saisie en masse')

@push('styles')
<style>
    main.flex-1 { overflow: hidden !important; }
    .page-shell { height: 100%; display: flex; flex-direction: column; }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-3" style="height:calc(100vh - 8rem)">
    <section class="card shrink-0">
        <div class="card-header">
            <div>
                <h4>Filtrer la saisie</h4>
                <p class="text-xs text-slate-500">Choisissez le bon contexte avant de commencer a renseigner les valeurs.</p>
            </div>
            <span class="badge-gray">Etapes 1 a 4</span>
        </div>
        <div class="card-body">
            <form action="{{ route('notes.masse.index') }}" method="GET" id="filterForm" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @if(request()->query('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif

                <div class="form-field">
                    <label class="form-label" for="classe_id">1. Classe</label>
                    <select id="classe_id" name="classe_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Selectionner</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClasse == $c->id ? 'selected' : '' }}>{{ $c->nom_classe }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label" for="matiere_id">2. Matiere</label>
                    <select id="matiere_id" name="matiere_id" class="form-select" {{ !$selectedClasse ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                        <option value="">Selectionner</option>
                        @foreach($matieres as $m)
                            <option value="{{ $m->id }}" {{ $selectedMatiere == $m->id ? 'selected' : '' }}>{{ $m->nom_matiere }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label" for="type_devoir">3. Type</label>
                    <select id="type_devoir" name="type_devoir" class="form-select" {{ !$selectedMatiere ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                        <option value="">Selectionner</option>
                        @foreach(\App\Models\Note::typeDevoirOptions() as $value => $label)
                            <option value="{{ $value }}" {{ $selectedType == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label" for="semestre">4. Semestre</label>
                    <select id="semestre" name="semestre" class="form-select" {{ !$selectedType ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                        <option value="">Selectionner</option>
                        @foreach(\App\Models\Note::semestreOptions() as $value => $label)
                            <option value="{{ $value }}" {{ (string) $selectedSemestre === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('notes.masse.index', ['date' => request()->query('date')]) }}" class="btn-secondary w-full justify-center">Reinitialiser</a>
                </div>
            </form>
        </div>
    </section>

    @if($eleves->isNotEmpty())
        <section class="card flex min-h-0 flex-1 flex-col overflow-hidden">
            <div class="card-header">
                <div>
                    <h4>Liste des eleves a renseigner</h4>
                    <p class="text-xs text-slate-500">{{ $eleves->count() }} eleve(s) pour {{ \App\Models\Note::semestreOptions()[$selectedSemestre] }}</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="button" class="btn-secondary btn-sm justify-center" onclick="remplirAbsents()">
                        <i class="bi bi-x-circle"></i>
                        Mettre 0 aux absents
                    </button>
                    <button type="button" class="btn-danger btn-sm justify-center" onclick="viderTout()">
                        <i class="bi bi-trash"></i>
                        Tout effacer
                    </button>
                </div>
            </div>

            <form action="{{ route('notes.masse.store') }}" method="POST" class="flex min-h-0 flex-1 flex-col">
                @csrf
                <input type="hidden" name="classe_id" value="{{ $selectedClasse }}">
                <input type="hidden" name="matiere_id" value="{{ $selectedMatiere }}">
                <input type="hidden" name="type_devoir" value="{{ $selectedType }}">
                <input type="hidden" name="semestre" value="{{ $selectedSemestre }}">

                <div class="mobile-list flex-1 overflow-y-auto sm:hidden">
                    @foreach($eleves as $index => $ins)
                        @php $noteExistante = $ins->notes->first(); @endphp
                        <article class="mobile-list-item">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cobalt-100 text-cobalt-700 font-bold text-sm">
                                    {{ substr($ins->eleve->nom, 0, 1) }}{{ substr($ins->eleve->prenom, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $ins->eleve->nom }} {{ $ins->eleve->prenom }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $ins->eleve->matricule }}</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if($noteExistante)
                                            <span class="badge-green">Maj</span>
                                        @else
                                            <span class="badge-gray">Vide</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-slate-100 pt-4">
                                <label class="form-label" for="note-mobile-{{ $ins->eleve->id }}">Note / 20</label>
                                <input
                                    type="number"
                                    step="0.25"
                                    min="0"
                                    max="20"
                                    id="note-mobile-{{ $ins->eleve->id }}"
                                    name="notes[{{ $ins->eleve->id }}]"
                                    class="note-input form-input text-center"
                                    value="{{ $noteExistante ? $noteExistante->note : '' }}"
                                    data-index="{{ $index }}"
                                    onkeydown="clavierNav(event, {{ $index }})"
                                >
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="hidden flex-1 overflow-y-auto sm:block">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom et prenom</th>
                                <th class="text-center">Note / 20</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eleves as $index => $ins)
                                @php $noteExistante = $ins->notes->first(); @endphp
                                <tr>
                                    <td>
                                        <span class="font-semibold tracking-wide text-slate-500">{{ $ins->eleve->matricule }}</span>
                                    </td>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $ins->eleve->nom }} {{ $ins->eleve->prenom }}</div>
                                    </td>
                                    <td class="text-center">
                                        <input
                                            type="number"
                                            step="0.25"
                                            min="0"
                                            max="20"
                                            name="notes[{{ $ins->eleve->id }}]"
                                            class="note-input form-input mx-auto w-24 text-center"
                                            value="{{ $noteExistante ? $noteExistante->note : '' }}"
                                            data-index="{{ $index }}"
                                            onkeydown="clavierNav(event, {{ $index }})"
                                        >
                                    </td>
                                    <td class="text-center">
                                        @if($noteExistante)
                                            <span class="badge-green">Maj</span>
                                        @else
                                            <span class="badge-gray">Vide</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-4 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        <i class="bi bi-info-circle mr-1 text-cobalt-600"></i>
                        Astuce : utilisez Entree pour passer rapidement au champ suivant.
                    </p>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-check-circle"></i>
                        Enregistrer toutes les notes
                    </button>
                </div>
            </form>
        </section>
    @elseif($selectedSemestre)
        <div class="alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <span>Aucun eleve inscrit dans cette classe pour l'annee selectionnee.</span>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function clavierNav(e, index) {
    const inputs = document.querySelectorAll('.note-input');
    if (e.key === 'Enter') {
        e.preventDefault();
        const next = inputs[index + 1];
        if (next) next.focus();
    }
}

function viderTout() {
    if (!confirm('Effacer toutes les valeurs saisies ?')) return;
    document.querySelectorAll('.note-input').forEach(input => input.value = '');
}

function remplirAbsents() {
    document.querySelectorAll('.note-input').forEach(input => {
        if (input.value === '') input.value = '0';
    });
}
</script>
@endpush
