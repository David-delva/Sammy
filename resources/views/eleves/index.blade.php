@extends('layouts.app')

@section('title', 'Liste des Élèves')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">
            <i class="bi bi-people-fill me-2"></i>Liste des Élèves
            @if($annee)
                <span class="badge bg-primary ms-2">{{ $annee->libelle }}</span>
            @endif
        </h5>
        <a href="{{ route('eleves.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus me-1"></i>Inscrire un élève
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Matricule</th>
                        <th>Nom & Prénom</th>
                        <th>Classe actuelle</th>
                        <th>Sexe</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                        <tr>
                            <td class="px-4 fw-bold text-secondary">{{ $eleve->matricule }}</td>
                            <td>{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                            <td>
                                @if($eleve->resolved_classe)
                                    <span class="badge bg-info text-white">{{ $eleve->resolved_classe->nom_classe }}</span>
                                @else
                                    <span class="text-muted small">Non inscrit</span>
                                @endif
                            </td>
                            <td>{{ $eleve->sexe }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('eleves.show', $eleve) }}" class="btn btn-sm btn-outline-info" title="Détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('eleves.edit', $eleve) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="if(confirm('Supprimer cet élève ?')) document.getElementById('delete-{{ $eleve->id }}').submit();" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-{{ $eleve->id }}" action="{{ route('eleves.destroy', $eleve) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle me-1"></i>Aucun élève trouvé pour cette sélection.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($eleves->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $eleves->links() }}
    </div>
    @endif
</div>
@endsection
