@extends('layouts.app')

@section('title', 'Années Académiques')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Années Académiques</h2>
    <a href="{{ route('annees.create') }}" class="btn btn-primary">Nouvelle année</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Active</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($annees as $annee)
                    <tr>
                        <td>{{ $annee->libelle }}</td>
                        <td>
                            @if($annee->active)
                                <span class="badge bg-primary">Active</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('annees.edit', $annee) }}" class="btn btn-sm btn-outline-secondary">Modifier</a>

                            <form action="{{ route('annees.destroy', $annee) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Supprimer cette année académique ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
