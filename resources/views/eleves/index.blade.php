@extends('layouts.app')

@section('title', 'Élèves')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des Élèves @if(request()->query('date')) — {{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }} @endif</h5>
        <a href="{{ route('eleves.create', ['date' => request()->query('date')]) }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i> Nouvel Élève</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Sexe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                        <tr>
                            <td>{{ $eleve->matricule }}</td>
                            <td>{{ $eleve->nom }}</td>
                            <td>{{ $eleve->prenom }}</td>
                            <td>{{ $eleve->classe?->nom_classe ?? 'Non assignée' }}</td>                            <td>{{ $eleve->sexe }}</td>
                            <td>
                                <a href="{{ route('bulletins.pdf', $eleve->id) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                <a href="{{ route('eleves.edit', $eleve) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <form action="{{ route('eleves.destroy', $eleve) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun élève enregistré</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
