@extends('layouts.app')

@section('title', 'Années académiques')
@section('breadcrumb', 'Administration / Années académiques')

@section('content')
<div class="space-y-6">
    <!-- En-tête de page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="bi bi-calendar-event text-xl"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
                    <h2 class="mt-0.5 text-2xl font-semibold tracking-tight text-gray-900">Années académiques</h2>
                </div>
            </div>
            <p class="mt-3 max-w-2xl text-sm text-gray-500">Gérez les années scolaires, définissez l'année active et gardez un historique propre du contexte académique.</p>
        </div>

        <a href="{{ route('annees.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-700 to-blue-800 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/25 transition-all hover:from-blue-800 hover:to-blue-900 hover:shadow-lg hover:shadow-blue-500/30 sm:self-auto">
            <i class="bi bi-plus-lg"></i>
            <span class="hidden sm:inline">Nouvelle année</span>
            <span class="sm:hidden">+</span>
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-400">Total</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $annees->count() }}</p>
                        <p class="mt-1 text-sm text-gray-500">années enregistrées</p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <i class="bi bi-calendar3 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="sm:col-span-2 lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <i class="bi bi-calendar-check text-lg"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Année actuellement active</p>
                            <p class="mt-0.5 truncate text-sm text-gray-500">
                                {{ optional($annees->firstWhere('active', true))->libelle ?? 'Aucune année active définie' }}
                            </p>
                        </div>
                        @if($annees->firstWhere('active', true))
                            <span class="shrink-0 rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                <i class="bi bi-check-circle-fill text-[10px]"></i>
                                Active
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des années -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <!-- En-tête -->
        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900">Liste des années</h3>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">{{ $annees->count() }} élément(s)</span>
            </div>
        </div>

        <!-- Version mobile : Cartes -->
        <div class="divide-y divide-gray-100 sm:hidden">
            @forelse($annees as $annee)
                <div class="px-4 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $annee->active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                <i class="bi {{ $annee->active ? 'bi-calendar-check' : 'bi-calendar-x' }} text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $annee->libelle }}</p>
                                    @if($annee->active)
                                        <span class="shrink-0 rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
                                            <i class="bi bi-check-circle-fill text-[10px]"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="shrink-0 rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-0.5 text-xs text-gray-400">Cycle scolaire</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2 border-t border-gray-100 pt-3">
                        <a href="{{ route('annees.edit', $annee) }}" class="flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-gray-50 inline-flex">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>
                        <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer cette année académique ?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-medium text-red-600 transition-all hover:border-red-300 hover:bg-red-50">
                                <i class="bi bi-trash"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-4 py-12">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                        <i class="bi bi-calendar-x text-2xl"></i>
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune année académique enregistrée</p>
                    <p class="mt-1 text-sm text-gray-500">Créez une première année pour activer le suivi par contexte scolaire.</p>
                </div>
            @endforelse
        </div>

        <!-- Version desktop : Tableau -->
        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Libellé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($annees as $annee)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $annee->libelle }}</div>
                                <div class="mt-1 text-xs text-gray-400">Cycle scolaire</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($annee->active)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                        <i class="bi bi-check-circle-fill text-[10px]"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        <i class="bi bi-x-circle-fill text-[10px]"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('annees.edit', $annee) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-gray-50" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer cette année académique ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-medium text-red-600 transition-all hover:border-red-300 hover:bg-red-50" title="Supprimer">
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