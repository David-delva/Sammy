@extends('layouts.app')

@section('title', 'Inscrire un eleve')
@section('breadcrumb', 'Scolarite / Eleves / Inscription')

@section('content')
<div class="mx-auto max-w-6xl">
    <!-- En-tete de page -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cobalt-100 text-cobalt-600">
                    <i class="bi bi-person-plus-fill text-xl"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Scolarite</p>
                    <h2 class="mt-0.5 text-2xl font-semibold tracking-tight text-gray-900">Inscrire un nouvel eleve</h2>
                </div>
            </div>
            <p class="mt-3 text-sm text-gray-500">Creez le profil de l'eleve et rattachez-le a une classe pour l'annee academique active.</p>
        </div>
        <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Retour a la liste
        </a>
    </div>

    <!-- Alerte annee academique -->
    @if(! $annee)
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-warning-200 bg-warning-50 p-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning-100 text-warning-600">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>
            <div class="flex-1">
                <p class="text-sm font-medium text-warning-900">Aucune annee academique active</p>
                <p class="mt-1 text-sm text-warning-700">
                    <a href="{{ route('annees.create') }}" class="font-semibold text-warning-900 underline underline-offset-2 hover:text-warning-800">Creez une annee academique</a>
                    avant d'inscrire un eleve.
                </p>
            </div>
        </div>
    @endif

    <div class="flex items-start gap-3 rounded-2xl border border-cobalt-100 bg-cobalt-50/80 p-4">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-cobalt-100 text-cobalt-600">
            <i class="bi bi-arrow-repeat"></i>
        </span>
        <div class="flex-1">
            <p class="text-sm font-medium text-cobalt-900">L'eleve existe deja ?</p>
            <p class="mt-1 text-sm text-cobalt-700">Utilisez le parcours de reinscription pour rattacher un profil existant a l'annee selectionnee sans creer de doublon.</p>
            <a href="{{ route('eleves.reinscriptions.index', ['date' => request()->query('date')]) }}" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-cobalt-900 underline underline-offset-2 hover:text-cobalt-700">
                <i class="bi bi-arrow-right"></i>
                Ouvrir la reinscription
            </a>
        </div>
    </div>

    <!-- Formulaire principal -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <!-- En-tete du formulaire -->
        <div class="flex flex-col gap-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Informations de l'eleve</h3>
                <p class="mt-0.5 text-xs text-gray-500">Tous les champs marques d'un asterisque sont obligatoires.</p>
            </div>
            <div class="flex items-center gap-3">
                @if($annee)
                    <span class="inline-flex items-center gap-2 rounded-full bg-cobalt-50 px-3.5 py-1.5 text-sm font-medium text-cobalt-700">
                        <i class="bi bi-calendar-check"></i>
                        {{ $annee->libelle }}
                    </span>
                @endif
                <button type="submit" form="eleve-form"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cobalt-700 to-navy-800 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-cobalt-500/25 transition-all hover:from-cobalt-800 hover:to-navy-900 hover:shadow-lg hover:shadow-cobalt-500/30 disabled:cursor-not-allowed disabled:from-gray-400 disabled:to-gray-500 disabled:shadow-none"
                    {{ ! $annee ? 'disabled' : '' }}>
                    <i class="bi bi-person-plus-fill"></i>
                    Inscrire l'eleve
                </button>
            </div>
        </div>

        <!-- Corps du formulaire -->
        <div class="px-6 py-6">
            <form id="eleve-form" action="{{ route('eleves.store', ['date' => request()->query('date')]) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Section : Informations principales -->
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="group">
                        <label for="matricule" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-upc text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Matricule <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}" 
                            placeholder="Ex : E-2025-001" 
                            class="form-input @error('matricule') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                        @error('matricule')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="classe_id" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-door-open text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Classe <span class="text-danger-500">*</span>
                        </label>
                        <select id="classe_id" name="classe_id" 
                            class="form-select @error('classe_id') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                            <option value="">Selectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }}
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="nom" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-person text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Nom <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" 
                            class="form-input @error('nom') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                        @error('nom')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="prenom" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-person-badge text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Prenom <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" 
                            class="form-input @error('prenom') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                        @error('prenom')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="date_naissance" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-calendar-event text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Date de naissance <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" 
                            class="form-input @error('date_naissance') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                        @error('date_naissance')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="lieu_naissance" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-geo-alt text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Lieu de naissance <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance') }}" 
                            placeholder="Ex : Koulamoutou" 
                            class="form-input @error('lieu_naissance') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                        @error('lieu_naissance')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="sexe" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-gender-ambiguous text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Sexe <span class="text-danger-500">*</span>
                        </label>
                        <select id="sexe" name="sexe" 
                            class="form-select @error('sexe') form-input error @enderror" 
                            required {{ ! $annee ? 'disabled' : '' }}>
                            <option value="">Selectionner</option>
                            <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Feminin</option>
                        </select>
                        @error('sexe')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="bac" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-mortarboard text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Baccalauréat
                        </label>
                        <input type="text" id="bac" name="bac" value="{{ old('bac') }}"
                            placeholder="Ex : Bac D"
                            class="form-input @error('bac') form-input error @enderror"
                            {{ ! $annee ? 'disabled' : '' }}>
                        @error('bac')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="provenance" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="bi bi-signpost text-gray-400 group-focus-within:text-cobalt-600"></i>
                            Provenance
                        </label>
                        <input type="text" id="provenance" name="provenance" value="{{ old('provenance') }}"
                            placeholder="Ex : Lycée technique de Libreville"
                            class="form-input @error('provenance') form-input error @enderror"
                            {{ ! $annee ? 'disabled' : '' }}>
                        @error('provenance')
                            <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Erreur generale -->
                @error('general')
                    <div class="flex items-start gap-3 rounded-xl border border-danger-200 bg-danger-50 p-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-danger-100 text-danger-600">
                            <i class="bi bi-x-circle-fill"></i>
                        </span>
                        <span class="text-sm text-danger-800">{{ $message }}</span>
                    </div>
                @enderror

                <!-- Boutons d'action -->
                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-between">
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" 
                        class="btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn-primary" {{ ! $annee ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle-fill"></i>
                        Inscrire l'eleve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
