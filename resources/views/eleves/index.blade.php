@extends('layouts.app')

@section('title', 'Eleves')
@section('breadcrumb', 'Scolarite / Eleves')

@section('content')
@php
    $routeDate = request()->query('date');
    $dateParams = filled($routeDate) ? ['date' => $routeDate] : [];
    $selectedClasse = filled($classeFilter) ? $classes->firstWhere('id', (int) $classeFilter) : null;
    $activeClassLabel = $selectedClasse?->nom_classe ?? 'Toutes les classes';
    $yearLabel = $annee?->libelle ?? 'Aucune annee';
    $canManage = $canManageAcademicData ?? false;
    $visibleCount = $eleves->count();
    $pageStart = $eleves->firstItem() ?? 0;
    $pageEnd = $eleves->lastItem() ?? 0;
@endphp

<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] bg-[#07140d] text-white shadow-[0_22px_60px_rgba(0,0,0,0.22)]">
        <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="relative px-5 py-6 sm:px-7 lg:px-8">
                <div class="absolute inset-y-0 right-0 hidden w-px bg-white/10 lg:block"></div>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/8 px-3 py-1.5 text-xs font-semibold text-white/80">
                                <i class="bi bi-calendar2-week text-[#f7d117]"></i>
                                {{ $yearLabel }}
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/8 px-3 py-1.5 text-xs font-semibold text-white/80">
                                <i class="bi bi-building text-[#f7d117]"></i>
                                {{ $activeClassLabel }}
                            </span>
                        </div>

                        <h2 class="mt-4 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Registre des eleves
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-white/68">
                            {{ $eleves->total() }} dossier(s) rattache(s) au contexte academique selectionne.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap lg:justify-end">
                        @if($canManage)
                            <a href="{{ route('eleves.create', $dateParams) }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#f7d117] px-4 py-2.5 text-sm font-bold text-black shadow-lg shadow-black/20 transition hover:bg-[#ffe15d]">
                                <i class="bi bi-person-plus-fill"></i>
                                Inscrire
                            </a>
                            <a href="{{ route('eleves.reinscriptions.index', $dateParams) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/18 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/16">
                                <i class="bi bi-arrow-repeat"></i>
                                Reinscriptions
                            </a>
                            <a href="{{ route('eleves.import.form', $dateParams) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/18 bg-black/24 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-black/34">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                                Import
                            </a>
                        @endif

                        <a href="{{ route('eleves.export', array_merge($dateParams, filled($classeFilter) ? ['classe' => $classeFilter] : [])) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/18 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/16">
                            <i class="bi bi-file-earmark-arrow-down text-[#f7d117]"></i>
                            Exporter Excel
                        </a>

                        <a href="{{ route('classement.index', $dateParams) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/18 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/16">
                            <i class="bi bi-trophy-fill text-[#f7d117]"></i>
                            Classement
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 border-t border-white/10 lg:border-t-0 lg:grid-cols-1">
                <div class="border-r border-white/10 px-4 py-4 lg:border-b lg:border-r-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/42">Total</p>
                    <p class="mt-1 text-2xl font-bold text-white" data-count="{{ $eleves->total() }}">{{ $eleves->total() }}</p>
                </div>
                <div class="border-r border-white/10 px-4 py-4 lg:border-b lg:border-r-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/42">Page</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ $visibleCount }}</p>
                </div>
                <div class="px-4 py-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/42">Classes</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ $classes->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[24px] border border-emerald-900/10 bg-white/95 shadow-[0_14px_42px_rgba(15,23,42,0.08)]">
        <div class="flex flex-col gap-4 border-b border-slate-200/80 px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-950">Liste des eleves</h3>
                <p class="mt-1 text-xs text-slate-500">
                    @if($eleves->total() > 0)
                        Affichage {{ $pageStart }}-{{ $pageEnd }} sur {{ $eleves->total() }} dossier(s).
                    @else
                        Aucun dossier pour le filtre courant.
                    @endif
                </p>
            </div>

            <form method="GET" action="{{ route('eleves.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @if(filled($routeDate))
                    <input type="hidden" name="date" value="{{ $routeDate }}">
                @endif

                <label for="classe-filter" class="sr-only">Filtrer par classe</label>
                <div class="relative">
                    <select id="classe-filter" name="classe" onchange="this.form.submit()" class="h-11 min-w-full rounded-full border border-emerald-900/15 bg-white px-4 pr-10 text-sm font-semibold text-slate-800 shadow-sm outline-none transition focus:border-emerald-600 sm:min-w-[230px]">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" @selected((string) $classeFilter === (string) $classe->id)>
                                {{ $classe->nom_classe }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-funnel-fill pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-emerald-700"></i>
                </div>

                @if(filled($classeFilter))
                    <a href="{{ route('eleves.index', $dateParams) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                        <i class="bi bi-x-lg"></i>
                        Reinitialiser
                    </a>
                @endif
            </form>
        </div>

        <div class="block border-b border-slate-100 px-4 py-3 sm:hidden">
            <div class="flex items-center justify-between gap-3 rounded-2xl bg-emerald-50/70 px-4 py-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-emerald-700">Filtre</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $activeClassLabel }}</p>
                </div>
                <i class="bi bi-people-fill text-lg text-emerald-700"></i>
            </div>
        </div>

        <div class="sm:hidden">
            @forelse($eleves as $eleve)
                <article class="border-b border-slate-100 px-4 py-4 last:border-b-0">
                    <div class="flex gap-3">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-lg text-emerald-700">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="truncate text-sm font-bold text-slate-950">{{ $eleve->nom }} {{ $eleve->prenom }}</h4>
                                    <p class="mt-1 text-xs font-semibold tracking-wide text-slate-500">{{ $eleve->matricule }}</p>
                                </div>
                                <span class="inline-flex flex-shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                    {{ $eleve->sexe }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 px-3 py-2">
                                    <p class="font-semibold text-slate-400">Classe</p>
                                    <p class="mt-1 truncate font-bold text-slate-800">{{ $eleve->resolved_classe?->nom_classe ?? 'Non inscrit' }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 px-3 py-2">
                                    <p class="font-semibold text-slate-400">Naissance</p>
                                    <p class="mt-1 truncate font-bold text-slate-800">{{ $eleve->date_naissance?->format('d/m/Y') ?? '--' }}</p>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-2">
                                <a href="{{ route('eleves.show', array_merge(['eleve' => $eleve], $dateParams)) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-3 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-800">
                                    <i class="bi bi-eye-fill"></i>
                                    Ouvrir le dossier
                                </a>

                                @if($canManage)
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('eleves.edit', array_merge(['eleve' => $eleve], $dateParams)) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs font-bold text-amber-800 transition hover:bg-amber-100">
                                            <i class="bi bi-pencil-fill"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('eleves.destroy', array_merge(['eleve' => $eleve], $dateParams)) }}" method="POST" onsubmit="return confirm('Retirer cet eleve de l annee selectionnee ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-3 py-2.5 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                                <i class="bi bi-trash-fill"></i>
                                                Retirer
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-700">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 class="mt-4 text-sm font-bold text-slate-950">Aucun eleve trouve</h4>
                    <p class="mx-auto mt-2 max-w-xs text-sm leading-6 text-slate-500">Aucun dossier ne correspond au contexte selectionne.</p>
                    @if($canManage)
                        <a href="{{ route('eleves.create', $dateParams) }}" class="mt-4 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                            <i class="bi bi-person-plus-fill"></i>
                            Inscrire un eleve
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full min-w-[860px] text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/90">
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Eleve</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Matricule</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Classe</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Naissance</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Sexe</th>
                        <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($eleves as $eleve)
                        <tr class="bg-white transition hover:bg-emerald-50/45">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                        <i class="bi bi-person-fill text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-slate-950">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $eleve->lieu_naissance ?: 'Lieu non renseigne' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold tracking-wide text-slate-600">{{ $eleve->matricule }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($eleve->resolved_classe)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                        <i class="bi bi-building"></i>
                                        {{ $eleve->resolved_classe->nom_classe }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">
                                        <i class="bi bi-dash-circle"></i>
                                        Non inscrit
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $eleve->date_naissance?->format('d/m/Y') ?? '--' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                    {{ $eleve->sexe === 'M' ? 'Masculin' : 'Feminin' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('eleves.show', array_merge(['eleve' => $eleve], $dateParams)) }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                        <i class="bi bi-eye-fill"></i>
                                        Details
                                    </a>

                                    @if($canManage)
                                        <a href="{{ route('eleves.edit', array_merge(['eleve' => $eleve], $dateParams)) }}" class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800 transition hover:bg-amber-100">
                                            <i class="bi bi-pencil-fill"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('eleves.destroy', array_merge(['eleve' => $eleve], $dateParams)) }}" method="POST" onsubmit="return confirm('Retirer cet eleve de l annee selectionnee ?')" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                                <i class="bi bi-trash-fill"></i>
                                                Retirer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-2xl text-emerald-700">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <h4 class="mt-4 text-base font-bold text-slate-950">Aucun eleve trouve</h4>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Aucun dossier ne correspond au contexte selectionne.</p>
                                @if($canManage)
                                    <a href="{{ route('eleves.create', $dateParams) }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                                        <i class="bi bi-person-plus-fill"></i>
                                        Inscrire un eleve
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eleves->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-5">
                {{ $eleves->appends(request()->query())->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
