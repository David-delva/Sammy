@extends('layouts.app')

@section('title', 'Saisie en masse')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary"><i class="bi bi-table me-2"></i>Saisie des Notes en Masse</h5>
        <span class="badge bg-primary">{{ $annee->libelle }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('notes.masse.index') }}" method="GET" class="row g-3 align-items-end" id="filterForm">
            <div class="col-md-3">
                <label class="form-label fw-bold small uppercase">1. Classe</label>
                <select name="classe_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">-- Sélectionner --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClasse == $c->id ? 'selected' : '' }}>{{ $c->nom_classe }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small uppercase">2. Matière</label>
                <select name="matiere_id" class="form-select" {{ !$selectedClasse ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <option value="">-- Sélectionner --</option>
                    @foreach($matieres as $m)
                        <option value="{{ $m->id }}" {{ $selectedMatiere == $m->id ? 'selected' : '' }}>{{ $m->nom_matiere }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small uppercase">3. Type</label>
                <select name="type_devoir" class="form-select" {{ !$selectedMatiere ? 'disabled' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <option value="">-- Sélectionner --</option>
                    <option value="devoir" {{ $selectedType == 'devoir' ? 'selected' : '' }}>Devoir</option>
                    <option value="composition" {{ $selectedType == 'composition' ? 'selected' : '' }}>Composition</option>
                </select>
            </div>
            <div class="col-md-3">
                <a href="{{ route('notes.masse.index') }}" class="btn btn-light border w-100">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

@if($eleves->isNotEmpty())
<div class="card shadow-sm border-0">
    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
        <div class="small fw-bold text-muted uppercase">Liste des élèves ({{ $eleves->count() }})</div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="remplirAbsents()">Mettre 0 aux absents</button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="viderTout()">Tout effacer</button>
        </div>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('notes.masse.store') }}" method="POST">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $selectedClasse }}">
            <input type="hidden" name="matiere_id" value="{{ $selectedMatiere }}">
            <input type="hidden" name="type_devoir" value="{{ $selectedType }}">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4" style="width: 150px;">Matricule</th>
                        <th>Nom & Prénom</th>
                        <th class="text-center" style="width: 200px;">Note / 20</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eleves as $index => $ins)
                        @php $noteExistante = $ins->notes->first(); @endphp
                        <tr>
                            <td class="px-4 text-muted small fw-bold">{{ $ins->eleve->matricule }}</td>
                            <td>{{ $ins->eleve->nom }} {{ $ins->eleve->prenom }}</td>
                            <td class="text-center">
                                <input type="number" step="0.25" min="0" max="20" 
                                       name="notes[{{ $ins->eleve->id }}]" 
                                       class="form-control text-center note-input mx-auto" 
                                       style="width: 100px;"
                                       value="{{ $noteExistante ? $noteExistante->note : '' }}"
                                       data-index="{{ $index }}"
                                       onkeydown="clavierNav(event, {{ $index }})">
                            </td>
                            <td class="text-center">
                                @if($noteExistante)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Màj</span>
                                @else
                                    <span class="badge bg-light text-muted border">Vide</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card-footer bg-white py-4 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Astuce : Utilisez les flèches ou Entrée pour naviguer rapidement.
                </div>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Enregistrer toutes les notes
                </button>
            </div>
        </form>
    </div>
</div>
@elseif($selectedType)
<div class="alert alert-info border-0 shadow-sm">
    <i class="bi bi-info-circle me-2"></i> Aucun élève inscrit dans cette classe pour l'année sélectionnée.
</div>
@endif

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
    if(!confirm('Effacer toutes les valeurs saisies ?')) return;
    document.querySelectorAll('.note-input').forEach(i => i.value = '');
}

function remplirAbsents() {
    document.querySelectorAll('.note-input').forEach(i => {
        if(i.value === '') i.value = '0';
    });
}
</script>
@endsection
