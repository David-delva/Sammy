@extends('layouts.app')

@section('title', 'Saisie en masse')
@section('breadcrumb', 'Évaluations / Notes / Saisie en masse')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Évaluations</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Saisie en masse</h2>
            <p class="mt-2 max-w-3xl text-sm text-gray-500">
                Saisissez rapidement les notes par classe et par matière
                @if(isset($annee) && $annee)
                    pour <span class="font-medium text-gray-700">{{ $annee->libelle }}</span>
                @endif.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Filtrer la saisie</h4>
            <span class="badge-gray">Étapes 1 à 3</span>
        </div>
        <div class="card-body">
            <form action="{{ route('notes.masse.index') }}" method="GET" id="filterForm" class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="form-field">
                    <label class="form-label" for="classe_id">1. Classe</label>
                    <select id="classe_id" name="classe_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Sélectionner</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClasse == $c->id ? 'selected' : '' }}>{{ $c->nom_classe }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label" for="matiere_id">2. Matière</label>
                    <select id="matiere_id" name="matiere_id" class="form-select" {{ !$selectedClasse ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                        <option value="">Sélectionner</option>
                        @foreach($matieres as $m)
                            <option value="{{ $m->id }}" {{ $selectedMatiere == $m->id ? 'selected' : '' }}>{{ $m->nom_matiere }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label" for="type_devoir">3. Type</label>
                    <select id="type_devoir" name="type_devoir" class="form-select" {{ !$selectedMatiere ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                        <option value="">Sélectionner</option>
                        <option value="devoir" {{ $selectedType == 'devoir' ? 'selected' : '' }}>Devoir</option>
                        <option value="composition" {{ $selectedType == 'composition' ? 'selected' : '' }}>Composition</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('notes.masse.index') }}" class="btn-secondary w-full justify-center">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    @if($eleves->isNotEmpty())
        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h4>Liste des élèves</h4>
                    <p class="mt-1 text-xs text-gray-400">{{ $eleves->count() }} élève(s) à renseigner</p>
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

            <form action="{{ route('notes.masse.store') }}" method="POST">
                @csrf
                <input type="hidden" name="classe_id" value="{{ $selectedClasse }}">
                <input type="hidden" name="matiere_id" value="{{ $selectedMatiere }}">
                <input type="hidden" name="type_devoir" value="{{ $selectedType }}">

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom & prénom</th>
                                <th class="text-center">Note / 20</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eleves as $index => $ins)
                                @php $noteExistante = $ins->notes->first(); @endphp
                                <tr>
                                    <td>
                                        <span class="font-semibold tracking-wide text-gray-500">{{ $ins->eleve->matricule }}</span>
                                    </td>
                                    <td>
                                        <div class="font-semibold text-gray-900">{{ $ins->eleve->nom }} {{ $ins->eleve->prenom }}</div>
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                               step="0.25"
                                               min="0"
                                               max="20"
                                               name="notes[{{ $ins->eleve->id }}]"
                                               class="note-input mx-auto h-10 w-24 rounded-lg border border-gray-200 bg-white px-3 text-center text-sm text-gray-900 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                                               value="{{ $noteExistante ? $noteExistante->note : '' }}"
                                               data-index="{{ $index }}"
                                               onkeydown="clavierNav(event, {{ $index }})">
                                    </td>
                                    <td class="text-center">
                                        @if($noteExistante)
                                            <span class="badge-green">Màj</span>
                                        @else
                                            <span class="badge-gray">Vide</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-4 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        <i class="bi bi-info-circle mr-1"></i>
                        Astuce : utilisez Entrée pour passer rapidement au champ suivant.
                    </p>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-check-circle"></i>
                        Enregistrer toutes les notes
                    </button>
                </div>
            </form>
        </div>
    @elseif($selectedType)
        <div class="alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <span>Aucun élève inscrit dans cette classe pour l'année sélectionnée.</span>
        </div>
    @endif
</div>

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
    document.querySelectorAll('.note-input').forEach(i => i.value = '');
}

function remplirAbsents() {
    document.querySelectorAll('.note-input').forEach(i => {
        if (i.value === '') i.value = '0';
    });
}
</script>
@endsection