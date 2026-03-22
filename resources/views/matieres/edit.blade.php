@extends('layouts.app')

@section('title', 'Modifier une matière')
@section('breadcrumb', 'Administration / Matières / Modification')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Modifier la matière</h2>
            <p class="mt-2 text-sm text-gray-500">Mettez à jour le libellé de <span class="font-medium text-gray-700">{{ $matiere->nom_matiere }}</span>.</p>
        </div>
        <a href="{{ route('matieres.index') }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour au catalogue
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Édition</h4>
            <span class="badge-gray">Catalogue</span>
        </div>
        <div class="card-body">
            <form action="{{ route('matieres.update', $matiere) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="form-field">
                    <label for="nom_matiere" class="form-label">Nom de la matière <span class="req">*</span></label>
                    <input type="text"
                           id="nom_matiere"
                           name="nom_matiere"
                           value="{{ old('nom_matiere', $matiere->nom_matiere) }}"
                           class="form-input @error('nom_matiere') error @enderror"
                           required>
                    <p class="form-hint">Le nom doit rester unique dans le catalogue.</p>
                    @error('nom_matiere')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('matieres.index') }}" class="btn-secondary justify-center">Annuler</a>
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