@extends('layouts.app')

@section('title', 'Modifier une Matière')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Modifier la Matière</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.update', $matiere) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nom_matiere" class="form-label">Nom de la Matière</label>
                        <input type="text" class="form-control @error('nom_matiere') is-invalid @enderror" 
                               id="nom_matiere" name="nom_matiere" value="{{ old('nom_matiere', $matiere->nom_matiere) }}" required>
                        @error('nom_matiere')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="coefficient" class="form-label">Coefficient</label>
                        <input type="number" class="form-control @error('coefficient') is-invalid @enderror" 
                               id="coefficient" name="coefficient" value="{{ old('coefficient', $matiere->coefficient) }}" min="1" required>
                        @error('coefficient')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="classe_id" class="form-label">Classe</label>
                        <select class="form-select @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id" required>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id', $matiere->classe_id) == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('matieres.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
