@extends('layouts.app')

@section('title', 'Matières')
@section('breadcrumb', 'Administration / Matières')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Catalogue des matières</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">
                Organisez les matières du programme
                @if(isset($annee) && $annee)
                    pour <span class="font-medium text-gray-700">{{ $annee->libelle }}</span>
                @endif,
                puis assignez-les aux classes avec leurs coefficients.
            </p>
        </div>

        @if($canManageAcademicData)
        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('matieres.assigner') }}" class="btn-secondary justify-center">
                <i class="bi bi-diagram-3"></i>
                Assigner aux classes
            </a>
            <a href="{{ route('matieres.create') }}" class="btn-primary justify-center">
                <i class="bi bi-plus-lg text-sm"></i>
                Nouvelle matière
            </a>
        </div>
        @endif
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4>Matières enregistrées</h4>
            <span class="badge-blue">{{ $matieres->total() }} matière(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Classes concernées</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matieres as $matiere)
                        <tr>
                            <td>
                                <div class="font-semibold text-gray-900">{{ $matiere->nom_matiere }}</div>
                                <div class="mt-1 text-xs text-gray-400">Catalogue pédagogique</div>
                            </td>
                            <td>
                                <span class="badge-blue">{{ $matiere->classes_count }} classe(s)</span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if($canManageAcademicData)
                                    <a href="{{ route('matieres.edit', $matiere) }}" class="btn-secondary btn-sm" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                                        <i class="bi bi-journal-bookmark text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune matière enregistrée</p>
                                    <p class="mt-1 text-sm text-gray-500">Commencez par créer les matières du catalogue avant de les assigner aux classes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matieres->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $matieres->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
