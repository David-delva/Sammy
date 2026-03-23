@extends('layouts.app')

@section('title', 'Modifier un élève')
@section('breadcrumb', 'Scolarité / Élèves / Modification')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Scolarité</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Modifier un élève</h2>
            <p class="mt-2 text-sm text-gray-500">Mettez à jour les informations de <span class="font-medium text-gray-700">{{ $eleve->nom }} {{ $eleve->prenom }}</span>.</p>
        </div>
        <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Édition du profil</h4>
                <p class="mt-1 text-xs text-gray-400">Modifiez les informations civiles et la classe associée à l'année en cours.</p>
            </div>
            @if($annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('eleves.update', $eleve) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="form-field">
                        <label for="matricule" class="form-label">Matricule <span class="req">*</span></label>
                        <input type="text" id="matricule" name="matricule" value="{{ old('matricule', $eleve->matricule) }}" class="form-input @error('matricule') error @enderror" required>
                        @error('matricule')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="classe_id" class="form-label">Classe <span class="req">*</span></label>
                        <select id="classe_id" name="classe_id" class="form-select @error('classe_id') error @enderror" required>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id', $inscription?->classe_id) == $classe->id ? 'selected' : '' }}>
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
                        <input type="text" id="nom" name="nom" value="{{ old('nom', $eleve->nom) }}" class="form-input @error('nom') error @enderror" required>
                        @error('nom')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="prenom" class="form-label">Prénom <span class="req">*</span></label>
                        <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $eleve->prenom) }}" class="form-input @error('prenom') error @enderror" required>
                        @error('prenom')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="req">*</span></label>
                        <input type="date" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', optional($eleve->date_naissance)->format('Y-m-d') ?? '') }}" class="form-input @error('date_naissance') error @enderror" required>
                        @error('date_naissance')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="lieu_naissance" class="form-label">Lieu de naissance <span class="req">*</span></label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance', $eleve->lieu_naissance) }}" class="form-input @error('lieu_naissance') error @enderror" required>
                        @error('lieu_naissance')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="sexe" class="form-label">Sexe <span class="req">*</span></label>
                        <select id="sexe" name="sexe" class="form-select @error('sexe') error @enderror" required>
                            <option value="M" {{ old('sexe', $eleve->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe', $eleve->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('sexe')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-save"></i>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection