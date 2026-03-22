@extends('layouts.app')

@section('title', 'Années académiques')
@section('breadcrumb', 'Administration / Années académiques')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Années académiques</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">Gérez les années scolaires, définissez l'année active et gardez un historique propre du contexte académique.</p>
        </div>

        <a href="{{ route('annees.create') }}" class="btn-primary self-start lg:self-auto">
            <i class="bi bi-plus-lg text-sm"></i>
            Nouvelle année
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="card">
            <div class="card-body">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-400">Total</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $annees->count() }}</p>
                <p class="mt-1 text-sm text-gray-500">années enregistrées</p>
            </div>
        </div>
        <div class="card md:col-span-2">
            <div class="card-body flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <i class="bi bi-calendar-event text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Année actuellement active</p>
                    <p class="text-sm text-gray-500">
                        {{ optional($annees->firstWhere('active', true))->libelle ?? 'Aucune année active définie' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4>Liste des années</h4>
            <span class="badge-gray">{{ $annees->count() }} élément(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($annees as $annee)
                        <tr>
                            <td>
                                <div class="font-semibold text-gray-900">{{ $annee->libelle }}</div>
                                <div class="mt-1 text-xs text-gray-400">Cycle scolaire</div>
                            </td>
                            <td>
                                @if($annee->active)
                                    <span class="badge-green">Active</span>
                                @else
                                    <span class="badge-gray">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('annees.edit', $annee) }}" class="btn-secondary btn-sm" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer cette année académique ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                                        <i class="bi bi-calendar-x text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune année académique enregistrée</p>
                                    <p class="mt-1 text-sm text-gray-500">Créez une première année pour activer le suivi par contexte scolaire.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection