@extends('layouts.app')

@section('title', 'Classes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des Classes @if(request()->query('date')) — {{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }} @endif</h5>
        <a href="{{ route('classes.create', ['date' => request()->query('date')]) }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i> Nouvelle Classe</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nom de la Classe</th>
                        <th>Nombre d'Élèves</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $classe)
                        <tr>
                            <td>{{ $classe->nom_classe }}</td>
                            <td>{{ $classe->eleves_count }}</td>
                            <td>
                                <a href="{{ route('classes.show', $classe) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('classes.liste.pdf', $classe) }}" class="btn btn-sm btn-secondary" title="Feuille d'appel">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="{{ route('classes.edit', $classe) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('classes.destroy', $classe) }}" method="POST" class="d-inline">
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
                            <td colspan="3" class="text-center">Aucune classe enregistrée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
