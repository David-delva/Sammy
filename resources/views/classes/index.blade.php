@extends('layouts.app')

@section('title', 'Classes')
@section('breadcrumb', 'Administration / Classes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Classes</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">
                Gérez les classes et suivez leurs effectifs
                @if(request()->query('date'))
                    pour <span class="font-medium text-gray-700">{{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }}</span>
                @endif.
            </p>
        </div>

        @if($canManageAcademicData)
        <a href="{{ route('classes.create', ['date' => request()->query('date')]) }}" class="btn-primary self-start lg:self-auto">
            <i class="bi bi-plus-lg text-sm"></i>
            Nouvelle classe
        </a>
        @endif
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4>Répertoire des classes</h4>
            <span class="badge-blue">{{ $classes->total() }} classe(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom de la classe</th>
                        <th>Effectif</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $classe)
                        <tr>
                            <td>
                                <div class="font-semibold text-gray-900">{{ $classe->nom_classe }}</div>
                                <div class="mt-1 text-xs text-gray-400">Classe pédagogique</div>
                            </td>
                            <td>
                                <span class="badge-blue">{{ $classe->eleves_count }} élève(s)</span>
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('classes.show', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm" title="Voir la classe">
                                        <i class="bi bi-eye"></i>
                                        Voir
                                    </a>
                                    <a href="{{ route('classes.liste.pdf', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm" title="Télécharger la liste">
                                        <i class="bi bi-printer"></i>
                                        Feuille d'appel
                                    </a>
                                    @if($canManageAcademicData)
                                    <a href="{{ route('classes.edit', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
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
                                        <i class="bi bi-building text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune classe enregistrée</p>
                                    <p class="mt-1 text-sm text-gray-500">Créez une classe pour commencer à répartir les élèves et les matières.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
