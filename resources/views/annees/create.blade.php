@extends('layouts.app')

@section('title', 'Creer une annee academique')
@section('breadcrumb', 'Administration / Annees academiques / Creation')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Creer une annee academique</h2>
            <p class="mt-2 text-sm text-gray-500">Ajoutez un nouveau cycle scolaire et choisissez s'il doit devenir l'annee active par defaut.</p>
        </div>
        <a href="{{ route('annees.index') }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour a la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Parametres de l'annee</h4>
            <span class="badge-blue">Nouveau</span>
        </div>
        <div class="card-body">
            <form action="{{ route('annees.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-field">
                    <label for="libelle" class="form-label">Libelle <span class="req">*</span></label>
                    <input type="text"
                           name="libelle"
                           id="libelle"
                           value="{{ old('libelle') }}"
                           placeholder="Ex : 2025-2026"
                           class="form-input @error('libelle') error @enderror"
                           required>
                    <p class="form-hint">Format attendu : annee de debut et annee de fin separees par un tiret.</p>
                    @error('libelle')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <label for="active" class="flex items-start gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-4 transition-colors hover:border-cobalt-200 hover:bg-cobalt-50/40">
                    <input type="checkbox"
                           value="1"
                           id="active"
                           name="active"
                           class="mt-1 h-4 w-4 rounded border-gray-300 text-cobalt-600 focus:ring-cobalt-500"
                           {{ old('active') ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Definir comme annee active</span>
                        <span class="mt-1 block text-sm text-gray-500">Si vous cochez cette option, l'annee active actuelle sera remplacee.</span>
                    </span>
                </label>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('annees.index') }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-check2-circle"></i>
                        Creer l'annee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
