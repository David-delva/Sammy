@extends('layouts.app')

@section('title', 'Matières')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Matières</h2>
    <a href="{{ route('matieres.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvelle Matière
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des matières @if(request()->query('date')) — {{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }} @endif</h5>
        <a href="{{ route('matieres.create', ['date' => request()->query('date')]) }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i> Nouvelle Matière</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Coefficient</th>
                        <th>Classe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matieres as $matiere)
                        <tr>
                            <td>{{ $matiere->nom_matiere }}</td>
                            <td>{{ $matiere->coefficient }}</td>
                            <td>{{ optional($matiere->classe)->nom_classe ?? '—' }}</td>
                            <td>
                                <a href="{{ route('matieres.edit', $matiere) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucune matière enregistrée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
