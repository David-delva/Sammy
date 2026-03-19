@extends('layouts.app')

@section('title', 'Classement')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary"><i class="bi bi-trophy me-2"></i>Classement Annuel</h5>
        <span class="badge bg-primary">{{ $annee->libelle }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('classement.index') }}" method="GET" class="row g-3 align-items-end" id="classeForm">
            <div class="col-md-6">
                <label class="form-label fw-bold small uppercase">Sélectionner une classe</label>
                <select name="classe_id" class="form-select" onchange="document.getElementById('classeForm').submit()">
                    <option value="">-- Choisir --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClasseId == $c->id ? 'selected' : '' }}>{{ $c->nom_classe }}</option>
                    @endforeach
                </select>
            </div>
            @if($selectedClasseId && $classement->isNotEmpty())
            <div class="col-md-6 text-md-end">
                <a href="{{ route('classement.pdf', $selectedClasseId) }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Exporter en PDF
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

@if($selectedClasseId)
    @if($classement->isNotEmpty())
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3">
            <div class="small fw-bold text-muted uppercase">Résultats : {{ $classe->nom_classe }} ({{ $classement->count() }} élèves classés)</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 text-center" style="width: 80px;">Rang</th>
                            <th>Élève</th>
                            <th class="text-center">Moyenne Générale</th>
                            <th class="text-center">Mention</th>
                            <th class="text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classement as $item)
                            <tr>
                                <td class="px-4 text-center">
                                    @if($item['rang'] == 1)
                                        <span class="badge bg-warning text-dark rounded-circle p-2" title="Premier">
                                            <i class="bi bi-award-fill"></i> 1
                                        </span>
                                    @else
                                        <span class="fw-bold text-muted">{{ $item['rang'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</div>
                                    <div class="small text-muted">{{ $item['eleve']->matricule }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold {{ $item['moyenne'] >= 10 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($item['moyenne'], 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeCls = match($item['mention']) {
                                            'Excellent' => 'bg-success',
                                            'Très Bien' => 'bg-info',
                                            'Bien' => 'bg-primary',
                                            'Assez Bien' => 'bg-secondary',
                                            default => 'bg-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeCls }}">{{ $item['mention'] }}</span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="{{ route('eleves.show', $item['eleve']->id) }}" class="btn btn-sm btn-light border">Voir profil</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info border-0 shadow-sm">
        <i class="bi bi-info-circle me-2"></i> Aucune donnée de note disponible pour cette classe cette année.
    </div>
    @endif
@endif
@endsection
