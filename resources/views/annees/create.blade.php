@extends('layouts.app')

@section('title', 'Créer une année académique')
@section('breadcrumb', 'Administration / Années académiques / Création')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Créer une année académique</h2>
            <p class="mt-2 text-sm text-gray-500">Ajoutez un nouveau cycle scolaire et choisissez s'il doit devenir l'année active par défaut.</p>
        </div>
        <a href="{{ route('annees.index') }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Paramètres de l'année</h4>
            <span class="badge-blue">Nouveau</span>
        </div>
        <div class="card-body">
            <form action="{{ route('annees.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-field">
                    <label for="libelle" class="form-label">Libellé <span class="req">*</span></label>
                    <input type="text"
                           name="libelle"
                           id="libelle"
                           value="{{ old('libelle') }}"
                           placeholder="Ex : 2025-2026"
                           class="form-input @error('libelle') error @enderror"
                           required>
                    <p class="form-hint">Format attendu : année de début et année de fin séparées par un tiret.</p>
                    @error('libelle')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <label for="active" class="flex items-start gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-4 transition-colors hover:border-brand-200 hover:bg-brand-50/40">
                    <input type="checkbox"
                           value="1"
                           id="active"
                           name="active"
                           class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                           {{ old('active') ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Définir comme année active</span>
                        <span class="mt-1 block text-sm text-gray-500">Si vous cochez cette option, l'année active actuelle sera remplacée.</span>
                    </span>
                </label>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('annees.index') }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-check2-circle"></i>
                        Créer l'année
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection