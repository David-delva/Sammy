@extends('layouts.app')

@section('title', 'Créer Année Académique')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Créer une année académique</h2>
    <a href="{{ route('annees.index') }}" class="btn btn-outline-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('annees.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé (ex: 2025-2026)</label>
                <input type="text" name="libelle" id="libelle" class="form-control" value="{{ old('libelle') }}" required>
                @error('libelle') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="active" name="active">
                <label class="form-check-label" for="active">Définir comme année active</label>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Créer</button>
                <a href="{{ route('annees.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
