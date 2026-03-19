@extends('layouts.app')

@section('title', 'Détails de l\'élève')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold">Profil de l'élève</h5>
            </div>
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle display-1 text-secondary opacity-50"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $eleve->nom }} {{ $eleve->prenom }}</h4>
                <p class="text-muted mb-3">{{ $eleve->matricule }}</p>
                <div class="d-grid gap-2 d-md-flex">
                    <a href="{{ route('eleves.edit', $eleve) }}" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-pencil me-1"></i>Modifier
                    </a>
                    <a href="{{ route('eleves.historique', $eleve) }}" class="btn btn-outline-info btn-sm flex-fill">
                        <i class="bi bi-clock-history me-1"></i>Historique
                    </a>
                </div>
            </div>
            <div class="list-group list-group-flush border-top">
                <div class="list-group-item px-4 py-3 border-0">
                    <span class="text-muted small d-block">Classe actuelle :</span>
                    <span class="fw-bold text-dark">
                        {{ $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit' }}
                    </span>
                </div>
                <div class="list-group-item px-4 py-3 border-0">
                    <span class="text-muted small d-block">Date de naissance :</span>
                    <span class="fw-bold text-dark">{{ $eleve->date_naissance->format('d/m/Y') }}</span>
                </div>
                <div class="list-group-item px-4 py-3 border-0">
                    <span class="text-muted small d-block">Sexe :</span>
                    <span class="fw-bold text-dark">{{ $eleve->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">Notes de l'année</h5>
                <a href="{{ route('bulletins.pdf', $eleve->id) }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger le bulletin
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Matière</th>
                                <th>Type</th>
                                <th class="text-center">Note / 20</th>
                                <th class="text-end px-4">Date de saisie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eleve->notes as $note)
                                <tr>
                                    <td class="px-4 fw-bold">{{ $note->matiere->nom_matiere }}</td>
                                    <td>
                                        <span class="badge bg-{{ $note->type_devoir == 'composition' ? 'primary' : 'secondary' }} text-uppercase" style="font-size: 0.7rem;">
                                            {{ $note->type_devoir }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fs-5 fw-bold {{ $note->note >= 10 ? 'text-success' : 'text-danger' }}">
                                            {{ $note->note }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4 text-muted small">
                                        {{ $note->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x display-4 mb-2 d-block"></i>
                                        Aucune note enregistrée pour cet élève cette année.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
