@extends('layouts.app')

@section('title', 'Créer une matière')
@section('breadcrumb', 'Administration / Matières / Création')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Créer une nouvelle matière</h2>
            <p class="mt-2 text-sm text-gray-500">Ajoutez une matière dans le catalogue global. Vous pourrez ensuite l'assigner aux classes avec un coefficient adapté.</p>
        </div>
        <a href="{{ route('matieres.index') }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour au catalogue
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Informations de la matière</h4>
            <span class="badge-blue">Nouveau</span>
        </div>
        <div class="card-body">
            <form action="{{ route('matieres.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="form-field">
                    <label for="nom_matiere" class="form-label">Nom de la matière <span class="req">*</span></label>
                    <input type="text"
                           id="nom_matiere"
                           name="nom_matiere"
                           value="{{ old('nom_matiere') }}"
                           placeholder="Ex : Mathématiques, Français, Histoire-Géo"
                           class="form-input @error('nom_matiere') error @enderror"
                           required>
                    <p class="form-hint">Le nom doit être unique dans le catalogue pour éviter les doublons d'assignation.</p>
                    @error('nom_matiere')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('matieres.index') }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-check2-circle"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection