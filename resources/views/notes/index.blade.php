@extends('layouts.app')

@section('title', 'Notes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Gestion des Notes
            @if(request()->query('date'))
                — ({{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }})
            @endif
        </h5>
        <a href="{{ route('notes.create', ['date' => request()->query('date')]) }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i> Nouvelle Note</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Matière</th>
                        <th>Note</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                        <tr>
                            <td>{{ $note->eleve->nom }} {{ $note->eleve->prenom }}</td>
                            <td>{{ $note->matiere->nom_matiere }}</td>
                            <td>{{ $note->note }}</td>
                            <td>
                                <span class="badge bg-{{ $note->type_devoir === 'composition' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($note->type_devoir) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('notes.edit', $note) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="d-inline">
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
                            <td colspan="5" class="text-center">Aucune note enregistrée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
