@extends('layouts.app')

@section('title', 'Saisir une Note')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Saisir une Nouvelle Note</h5>
                <a href="{{ route('notes.index') }}" class="btn btn-sm btn-outline-secondary">Retour</a>
            </div>
            <div class="card-body">
                <form action="{{ route('notes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                    <div class="mb-3">
                        <label for="eleve_id" class="form-label">Élève</label>
                        <select class="form-select @error('eleve_id') is-invalid @enderror" id="eleve_id" name="eleve_id" required>
                            <option value="">Sélectionner un élève</option>
                            @foreach($eleves as $eleve)
                                <option value="{{ $eleve->id }}" {{ old('eleve_id') == $eleve->id ? 'selected' : '' }}>
                                    {{ $eleve->nom }} {{ $eleve->prenom }} ({{ optional($eleve->resolved_classe ?? $eleve->classe)->nom_classe ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                        @error('eleve_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="matiere_id" class="form-label">Matière</label>
                        <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                            <option value="">Sélectionner une matière</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                    {{ $matiere->nom_matiere }} ({{ optional($matiere->classe)->nom_classe ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                        @error('matiere_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (sur 20)</label>
                        <input type="number" step="0.01" class="form-control @error('note') is-invalid @enderror" 
                               id="note" name="note" value="{{ old('note') }}" min="0" max="20" required>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="type_devoir" class="form-label">Type</label>
                        <select class="form-select @error('type_devoir') is-invalid @enderror" id="type_devoir" name="type_devoir" required>
                            <option value="">Sélectionner</option>
                            <option value="devoir" {{ old('type_devoir') == 'devoir' ? 'selected' : '' }}>Devoir</option>
                            <option value="composition" {{ old('type_devoir') == 'composition' ? 'selected' : '' }}>Composition</option>
                        </select>
                        @error('type_devoir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('notes.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
