@extends('layouts.app')

@section('title', 'Inscrire un Ã©lÃ¨ve')
@section('breadcrumb', 'ScolaritÃ© / Ã‰lÃ¨ves / Inscription')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">ScolaritÃ©</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Inscrire un nouvel Ã©lÃ¨ve</h2>
            <p class="mt-2 text-sm text-gray-500">CrÃ©ez le profil de l'Ã©lÃ¨ve et rattachez-le Ã  une classe pour l'annÃ©e acadÃ©mique active.</p>
        </div>
        <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour Ã  la liste
        </a>
    </div>

    @if(! $annee)
        <div class="alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>
                Aucune annÃ©e acadÃ©mique active.
                <a href="{{ route('annees.create') }}" class="font-semibold underline underline-offset-2">CrÃ©ez une annÃ©e acadÃ©mique</a>
                avant d'inscrire un Ã©lÃ¨ve.
            </span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Informations de l'Ã©lÃ¨ve</h4>
                <p class="mt-1 text-xs text-gray-400">Tous les champs marquÃ©s d'un astÃ©risque sont obligatoires.</p>
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
                            <option value="">SÃ©lectionner une classe</option>
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
                        <label for="prenom" class="form-label">PrÃ©nom <span class="req">*</span></label>
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
                        <label for="lieu_naissance" class="form-label">Lieu de naissance <span class="req">*</span></label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance') }}" placeholder="Ex : Koulamoutou" class="form-input @error('lieu_naissance') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                        @error('lieu_naissance')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="sexe" class="form-label">Sexe <span class="req">*</span></label>
                        <select id="sexe" name="sexe" class="form-select @error('sexe') error @enderror" required {{ ! $annee ? 'disabled' : '' }}>
                            <option value="">SÃ©lectionner</option>
                            <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>FÃ©minin</option>
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
                        Inscrire l'Ã©lÃ¨ve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection