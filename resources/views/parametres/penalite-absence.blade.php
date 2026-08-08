@extends('layouts.app')

@section('title', 'Pénalité d\'absence')
@section('breadcrumb', 'Administration / Paramètres / Pénalité d\'absence')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Administration</p>
                <h2 class="page-title">Configurez la pénalité appliquée par heure d'absence.</h2>
                <p class="page-lead">Règle 4.9 : cette valeur est déduite de la moyenne de chaque matière, proportionnellement aux heures d'absence enregistrées. Valeur par défaut : 0,01 point par heure.</p>
            </div>
        </div>
    </section>

    <section class="card max-w-xl">
        <div class="card-header">
            <h4>Taux global</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="mb-4 rounded-2xl border border-success-200 bg-success-50 p-4 text-sm text-success-700">{{ session('success') }}</div>
            @endif

            <form action="{{ route('penalite-absence.update') }}" method="POST" class="space-y-5">
                @csrf
                <div class="form-field">
                    <label for="penalite_par_heure" class="form-label">Points déduits par heure d'absence <span class="req">*</span></label>
                    <input type="number" step="0.0001" min="0" max="1" id="penalite_par_heure" name="penalite_par_heure"
                           value="{{ old('penalite_par_heure', $tauxActuel) }}" class="form-input @error('penalite_par_heure') error @enderror" required>
                    @error('penalite_par_heure')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">Exemple : 0,01 signifie qu'une absence de 10 heures retire 0,10 point à la moyenne de la matière concernée.</p>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-save"></i>
                    Enregistrer
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
