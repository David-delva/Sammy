@extends('layouts.app')

@section('title', 'Historique scolaire')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold text-primary">Parcours Scolaire</h4>
        <p class="text-muted mb-0">{{ $eleve->nom }} {{ $eleve->prenom }} ({{ $eleve->matricule }})</p>
    </div>
    <a href="{{ route('eleves.show', $eleve) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour au profil
    </a>
</div>

@if($historique->isEmpty())
    <div class="alert alert-info border-0 shadow-sm">
        <i class="bi bi-info-circle me-2"></i> Aucune inscription historique trouvée pour cet élève.
    </div>
@else
    <div class="row">
        @foreach($historique as $item)
            <div class="col-md-6 mb-4">
                <div class="card h-100 reveal">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">{{ $item['annee']->libelle }}</span>
                        @if($item['annee']->active)
                            <span class="badge bg-success">Année Active</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block small uppercase fw-bold">Classe fréquentée</small>
                                <span class="fw-bold text-dark fs-5">{{ $item['classe']->nom_classe }}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 text-info">
                                <i class="bi bi-graph-up-arrow fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block small uppercase fw-bold">Moyenne générale</small>
                                <span class="fw-bold fs-5 {{ $item['moyenne_generale'] >= 10 ? 'text-success' : 'text-danger' }}">
                                    {{ $item['moyenne_generale'] ? number_format($item['moyenne_generale'], 2) . ' / 20' : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-3">
                        <a href="{{ route('eleves.show', $eleve) }}?date={{ explode('-', $item['annee']->libelle)[0] }}-10-01" class="btn btn-sm btn-link text-decoration-none">
                            Consulter les détails de cette année <i class="bi bi-arrow-right small ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
