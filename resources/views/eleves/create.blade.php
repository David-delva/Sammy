@extends('layouts.app')

@section('title', 'Inscrire un élève')
@section('breadcrumb', 'Scolarité / Élèves / Inscription')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Scolarité</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Inscrire un nouvel élève</h2>
            <p class="mt-2 text-sm text-gray-500">Créez le profil de l'élève et rattachez-le à une classe pour l'année académique active.</p>
        </div>
        <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    @if(! $annee)
        <div class="alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>
                Aucune année académique active.
                <a href="{{ route('annees.create') }}" class="font-semibold underline underline-offset-2">Créez une année académique</a>
                avant d'inscrire un élève.
            </span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Informations de l'élève</h4>
                <p class="mt-1 text-xs text-gray-400">Tous les champs marqués d'un astérisque sont obligatoires.</p>
            </div>
            @if($annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('eleves.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="form-field">
                        <label for="matricule" class="form-label">Matricule <span class="req">*</span></label>
                        <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Ex : E-2025-001" class="form-input @error('matricule') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                        @error('matricule')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="classe_id" class="form-label">Classe <span class="req">*</span></label>
                        <select id="classe_id" name="classe_id" class="form-select @error('classe_id') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }}
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="nom" class="form-label">Nom <span class="req">*</span></label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" class="form-input @error('nom') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                        @error('nom')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="prenom" class="form-label">Prénom <span class="req">*</span></label>
                        <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" class="form-input @error('prenom') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                        @error('prenom')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="req">*</span></label>
                        <input type="date" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" class="form-input @error('date_naissance') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                        @error('date_naissance')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="sexe" class="form-label">Sexe <span class="req">*</span></label>
                        <select id="sexe" name="sexe" class="form-select @error('sexe') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                            <option value="">Sélectionner</option>
                            <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('sexe')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @error('general')
                    <div class="alert-error">
                        <i class="bi bi-x-circle-fill"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center" {{ ! $annee ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle"></i>
                        Inscrire l'élève
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection