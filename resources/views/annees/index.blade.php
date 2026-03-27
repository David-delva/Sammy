@extends('layouts.app')

@section('title', 'Annees academiques')
@section('breadcrumb', 'Administration / Annees academiques')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="bi bi-calendar-event text-xl"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
                    <h2 class="mt-0.5 text-2xl font-semibold tracking-tight text-gray-900">Annees academiques</h2>
                </div>
            </div>
            <p class="mt-3 max-w-2xl text-sm text-gray-500">Gerez les annees scolaires, definissez l'annee active et accordez, si besoin, une derogation d'ecriture au secretariat pour une annee archivee.</p>
        </div>

        <a href="{{ route('annees.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-700 to-blue-800 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/25 transition-all hover:from-blue-800 hover:to-blue-900 hover:shadow-lg hover:shadow-blue-500/30 sm:self-auto">
            <i class="bi bi-plus-lg"></i>
            <span class="hidden sm:inline">Nouvelle annee</span>
            <span class="sm:hidden">+</span>
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-400">Total</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">{{ $annees->count() }}</p>
                        <p class="mt-1 text-sm text-gray-500">annees enregistrees</p>
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
                            <p class="text-sm font-semibold text-gray-900">Annee actuellement active</p>
                            <p class="mt-0.5 truncate text-sm text-gray-500">
                                {{ optional($annees->firstWhere('active', true))->libelle ?? 'Aucune annee active definie' }}
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

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Derogations d'ecriture du secretariat</h3>
                <p class="mt-1 text-sm text-gray-500">Le secretariat peut modifier l'annee en cours automatiquement. Pour toute autre annee, un administrateur doit accorder une autorisation explicite.</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">{{ $secretariats->count() }} compte(s) secretariat</span>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($annees as $annee)
                @php
                    $authorizedUserIds = $authorizedUserIdsByYear[$annee->id] ?? [];
                    $isCurrentCalendarYear = $annee->libelle === $currentCalendarLabel;
                @endphp
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $annee->libelle }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $isCurrentCalendarYear ? "Acces automatique pour le secretariat car cette annee correspond au calendrier en cours." : "Acces ecriture interdit au secretariat tant qu'une derogation admin n'est pas accordee." }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $isCurrentCalendarYear ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            <i class="bi {{ $isCurrentCalendarYear ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                            {{ $isCurrentCalendarYear ? 'Acces automatique' : 'Derogation requise' }}
                        </span>
                    </div>

                    <div class="mt-4 grid gap-3 lg:grid-cols-2">
                        @forelse($secretariats as $secretariat)
                            @php
                                $isAuthorized = $isCurrentCalendarYear || in_array($secretariat->id, $authorizedUserIds, true);
                            @endphp
                            <div class="rounded-2xl border {{ $isAuthorized ? 'border-emerald-200 bg-emerald-50/40' : 'border-gray-200 bg-white' }} p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $secretariat->name }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $secretariat->email }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $isAuthorized ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        <i class="bi {{ $isAuthorized ? 'bi-check-circle-fill' : 'bi-slash-circle' }}"></i>
                                        {{ $isAuthorized ? 'Peut modifier' : 'Lecture seule' }}
                                    </span>
                                </div>

                                @if($isCurrentCalendarYear)
                                    <p class="mt-3 text-xs leading-6 text-gray-500">Aucune action supplementaire n'est necessaire sur l'annee academique en cours.</p>
                                @elseif($isAuthorized)
                                    <form action="{{ route('annees.write-access.destroy', [$annee, $secretariat]) }}" method="POST" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2 text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50">
                                            <i class="bi bi-shield-x"></i>
                                            Retirer l'autorisation
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('annees.write-access.store', $annee) }}" method="POST" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $secretariat->id }}">
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-2 text-xs font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-50">
                                            <i class="bi bi-shield-check"></i>
                                            Autoriser cette annee
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-sm text-gray-500 lg:col-span-2">
                                Aucun compte secretariat n'est encore disponible pour recevoir une derogation.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-sm text-gray-500 sm:px-6">
                    Aucune annee academique enregistree pour configurer les derogations du secretariat.
                </div>
            @endforelse
        </div>
    </section>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900">Liste des annees</h3>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">{{ $annees->count() }} element(s)</span>
            </div>
        </div>

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
                        <a href="{{ route('annees.edit', $annee) }}" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-gray-50">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>
                        <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer cette annee academique ?')" class="flex-1">
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
                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune annee academique enregistree</p>
                    <p class="mt-1 text-sm text-gray-500">Creez une premiere annee pour activer le suivi par contexte scolaire.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Libelle</th>
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
                                    <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer cette annee academique ?')">
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
                                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune annee academique enregistree</p>
                                    <p class="mt-1 text-sm text-gray-500">Creez une premiere annee pour activer le suivi par contexte scolaire.</p>
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