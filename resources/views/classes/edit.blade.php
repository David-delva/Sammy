@extends('layouts.app')

@section('title', 'Modifier une classe')
@section('breadcrumb', 'Administration / Classes / Modification')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Modifier la classe</h2>
            <p class="mt-2 text-sm text-gray-500">Mettez à jour les informations de <span class="font-medium text-gray-700">{{ $classe->nom_classe }}</span>.</p>
        </div>
        <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Édition</h4>
            <span class="badge-gray">Classe</span>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.update', $classe) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="form-field">
                    <label for="nom_classe" class="form-label">Nom de la classe <span class="req">*</span></label>
                    <input type="text"
                           id="nom_classe"
                           name="nom_classe"
                           value="{{ old('nom_classe', $classe->nom_classe) }}"
                           class="form-input @error('nom_classe') error @enderror"
                           required>
                    <p class="form-hint">Le nom doit rester unique pour éviter les ambiguïtés d'inscription.</p>
                    @error('nom_classe')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">Annuler</a>
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