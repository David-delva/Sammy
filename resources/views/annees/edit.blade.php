@extends('layouts.app')

@section('title', 'Modifier une année académique')
@section('breadcrumb', 'Administration / Années académiques / Modification')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Modifier l'année académique</h2>
            <p class="mt-2 text-sm text-gray-500">Mettez à jour le libellé ou l'état actif de <span class="font-medium text-gray-700">{{ $annee->libelle }}</span>.</p>
        </div>
        <a href="{{ route('annees.index') }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Édition</h4>
            @if($annee->active)
                <span class="badge-green">Active</span>
            @else
                <span class="badge-gray">Inactive</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('annees.update', $annee) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="libelle" class="form-label">Libellé <span class="req">*</span></label>
                    <input type="text"
                           name="libelle"
                           id="libelle"
                           value="{{ old('libelle', $annee->libelle) }}"
                           class="form-input @error('libelle') error @enderror"
                           required>
                    <p class="form-hint">Conservez un format homogène pour toutes les années du catalogue.</p>
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
                           {{ old('active', $annee->active) ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Marquer comme année active</span>
                        <span class="mt-1 block text-sm text-gray-500">Une seule année peut être active à la fois.</span>
                    </span>
                </label>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('annees.index') }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-save"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection