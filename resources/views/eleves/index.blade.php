@extends('layouts.app')

@section('title', 'Élèves')
@section('breadcrumb', 'Scolarité / Élèves')

@section('content')
<div class="space-y-6">
    <!-- En-tête de page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="bi bi-people-fill text-xl"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Scolarité</p>
                    <h2 class="mt-0.5 text-2xl font-semibold tracking-tight text-gray-900">Élèves</h2>
                </div>
            </div>
            <p class="mt-3 max-w-2xl text-sm text-gray-500">
                Gérez les inscriptions et consultez les profils des élèves
                @if(isset($annee) && $annee)
                    pour <span class="font-medium text-gray-700">{{ $annee->libelle }}</span>
                @endif.
            </p>
        </div>
        <a href="{{ route('eleves.create', ['date' => request()->query('date')]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-2.5 text-sm font-semibold text-bleue-50 shadow-md shadow-brand-500/25 transition-all hover:from-brand-700 hover:to-brand-800 hover:shadow-lg hover:shadow-brand-500/30 sm:self-auto">
            <i class="bi bi-person-plus"></i>
            Inscrire un élève
        </a>
    </div>

    <!-- Carte : Liste des élèves -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <!-- En-tête de la carte avec filtre -->
        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-gray-900">Liste des élèves</h3>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">{{ $eleves->total() }} élève(s)</span>
            </div>
            
            <!-- Filtre par classe -->
            <form method="GET" action="{{ route('eleves.index') }}" class="flex items-center gap-2">
                @if(request()->has('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif
                <div class="relative">
                    <select name="classe" onchange="this.form.submit()" class="appearance-none rounded-xl border border-gray-200 bg-white py-2 pl-3 pr-8 text-sm text-gray-700 shadow-sm transition-all focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ $classeFilter == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom_classe }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>
                @if($classeFilter)
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600" title="Réinitialiser le filtre">
                        <i class="bi bi-x-lg text-sm"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Version mobile : Cartes -->
        <div class="divide-y divide-gray-100 sm:hidden">
            @forelse($eleves as $eleve)
                <div class="px-4 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                                    <span class="shrink-0 rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ $eleve->sexe === 'M' ? 'M' : 'F' }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-xs text-gray-500">{{ $eleve->matricule }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @if($eleve->resolved_classe)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            <i class="bi bi-door-open text-[10px]"></i>
                                            {{ $eleve->resolved_classe->nom_classe }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                            Non inscrit
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2 border-t border-gray-100 pt-3">
                        <a href="{{ route('eleves.show', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-gray-50 inline-flex">
                            <i class="bi bi-eye"></i>
                            Détails
                        </a>
                        <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-gray-50 inline-flex">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>
                        <form action="{{ route('eleves.destroy', $eleve) }}" method="POST" onsubmit="return confirm('Supprimer cet élève ?')" class="flex-1">
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
                        <i class="bi bi-people text-2xl"></i>
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-700">Aucun élève trouvé</p>
                    <p class="mt-1 text-sm text-gray-500">Commencez par inscrire un élève.</p>
                </div>
            @endforelse
        </div>

        <!-- Version desktop : Tableau -->
        <div class="hidden overflow-x-auto sm:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Élève</th>
                        <th>Classe actuelle</th>
                        <th>Sexe</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                        <tr>
                            <td>
                                <span class="font-semibold tracking-wide text-gray-500">{{ $eleve->matricule }}</span>
                            </td>
                            <td>
                                <div class="font-semibold text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</div>
                                <div class="mt-1 text-xs text-gray-400">Profil élève</div>
                            </td>
                            <td>
                                @if($eleve->resolved_classe)
                                    <span class="badge-blue">{{ $eleve->resolved_classe->nom_classe }}</span>
                                @else
                                    <span class="badge-gray">Non inscrit</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-gray">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Féminin' }}</span>
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('eleves.show', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-eye"></i>
                                        Détails
                                    </a>
                                    <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <form id="delete-{{ $eleve->id }}" action="{{ route('eleves.destroy', $eleve) }}" method="POST" onsubmit="return confirm('Supprimer cet élève ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                                        <i class="bi bi-people text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-gray-700">Aucun élève trouvé</p>
                                    <p class="mt-1 text-sm text-gray-500">Commencez par inscrire un élève pour alimenter les classes, les notes et les bulletins.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eleves->hasPages())
            <div class="border-t border-gray-100 px-4 py-4 sm:px-6">
                {{ $eleves->links() }}
            </div>
        @endif
    </div>
</div>
@endsection