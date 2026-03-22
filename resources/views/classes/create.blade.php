@extends('layouts.app')

@section('title', 'Créer une classe')
@section('breadcrumb', 'Administration / Classes / Création')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Créer une nouvelle classe</h2>
            <p class="mt-2 text-sm text-gray-500">Ajoutez une classe dans le catalogue pour l'utiliser dans les inscriptions et les assignations de matières.</p>
        </div>
        <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Informations de la classe</h4>
            <span class="badge-blue">Nouveau</span>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="form-field">
                    <label for="nom_classe" class="form-label">Nom de la classe <span class="req">*</span></label>
                    <input type="text"
                           id="nom_classe"
                           name="nom_classe"
                           value="{{ old('nom_classe') }}"
                           class="form-input @error('nom_classe') error @enderror"
                           placeholder="Ex : 6e A, Terminale C, BTS 1"
                           required>
                    <p class="form-hint">Choisissez un intitulé court, clair et unique dans le catalogue.</p>
                    @error('nom_classe')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">Annuler</a>
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