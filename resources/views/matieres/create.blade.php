@extends('layouts.app')

@section('title', 'Creer une matiere')
@section('breadcrumb', 'Administration / Matieres / Creation')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Creer une nouvelle matiere</h2>
            <p class="mt-2 text-sm text-gray-500">Ajoutez une matiere dans le catalogue global. Vous pourrez ensuite l'assigner aux classes avec un coefficient adapte.</p>
        </div>
        <a href="{{ route('matieres.index') }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour au catalogue
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Informations de la matiere</h4>
            <span class="badge-blue">Nouveau</span>
        </div>
        <div class="card-body">
            <form action="{{ route('matieres.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="form-field">
                    <label for="nom_matiere" class="form-label">Nom de la matiere <span class="req">*</span></label>
                    <input type="text"
                           id="nom_matiere"
                           name="nom_matiere"
                           value="{{ old('nom_matiere') }}"
                           placeholder="Ex : Mathematiques, Francais, Histoire-Geo"
                           class="form-input @error('nom_matiere') error @enderror"
                           required>
                    <p class="form-hint">Le nom doit etre unique dans le catalogue pour eviter les doublons d'assignation.</p>
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
