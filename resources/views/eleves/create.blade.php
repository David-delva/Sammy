@extends('layouts.app')

@section('title', 'Inscrire un Élève')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- Alerte si pas d'année académique --}}
        @if(! $annee)
            <div class="alert alert-warning d-flex align-items-center mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    Aucune année académique active. 
                    <a href="{{ route('annees.create') }}" class="alert-link">Créer une année académique</a> 
                    avant d'inscrire un élève.
                </div>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 text-primary">
                    <i class="bi bi-person-plus-fill me-2"></i>Inscription d'un Nouvel Élève
                    @if($annee)
                        <span class="badge bg-primary ms-2">{{ $annee->libelle }}</span>
                    @endif
                </h5>
                <a href="{{ route('eleves.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Retour
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('eleves.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="matricule" class="form-label fw-bold">Matricule <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('matricule') is-invalid @enderror"
                                   id="matricule" name="matricule"
                                   value="{{ old('matricule') }}"
                                   placeholder="Ex: E-2025-001" required>
                            @error('matricule')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="classe_id" class="form-label fw-bold">Classe <span class="text-danger">*</span></label>
                            <select class="form-select @error('classe_id') is-invalid @enderror"
                                    id="classe_id" name="classe_id" required>
                                <option value="">— Sélectionner une classe —</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}"
                                        {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nom_classe }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   id="nom" name="nom"
                                   value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('prenom') is-invalid @enderror"
                                   id="prenom" name="prenom"
                                   value="{{ old('prenom') }}" required>
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label fw-bold">Date de Naissance <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('date_naissance') is-invalid @enderror"
                                   id="date_naissance" name="date_naissance"
                                   value="{{ old('date_naissance') }}" required>
                            @error('date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sexe" class="form-label fw-bold">Sexe <span class="text-danger">*</span></label>
                            <select class="form-select @error('sexe') is-invalid @enderror"
                                    id="sexe" name="sexe" required>
                                <option value="">— Sélectionner —</option>
                                <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                            </select>
                            @error('sexe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @error('general')
                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                    @enderror

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('eleves.index') }}" class="btn btn-light border">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4" {{ ! $annee ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle me-1"></i>Inscrire l'élève
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
