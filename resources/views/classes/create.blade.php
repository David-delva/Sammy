@extends('layouts.app')

@section('title', 'Créer une Classe')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Créer une Nouvelle Classe</h5>
                <a href="{{ route('classes.index') }}" class="btn btn-sm btn-outline-secondary">Retour</a>
            </div>
            <div class="card-body">
                <form action="{{ route('classes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nom_classe" class="form-label">Nom de la Classe</label>
                        <input type="text" class="form-control @error('nom_classe') is-invalid @enderror" 
                               id="nom_classe" name="nom_classe" value="{{ old('nom_classe') }}" required>
                        @error('nom_classe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
