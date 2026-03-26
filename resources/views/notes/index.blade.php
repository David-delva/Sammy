@extends('layouts.app')

@section('title', 'Notes')
@section('breadcrumb', 'Évaluations / Notes')

@section('content')
<div class="space-y-8">
    <!-- En-tête de page Premium -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-6 shadow-xl shadow-blue-900/20 sm:p-8">
        <div class="absolute right-0 top-0 h-40 w-40 translate-x-1/3 -translate-y-1/3 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 -translate-x-1/4 translate-y-1/4 rounded-full bg-white/5 blur-3xl"></div>
        
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                    <i class="bi bi-clipboard-data-fill text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-200">Évaluations</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-indigo-500 sm:text-3xl">Notes & Résultats</h2>
                    <p class="mt-2 max-w-xl text-sm text-indigo-100">
                        Suivez les notes enregistrées
                        @if(isset($currentAcademicLabel) && $currentAcademicLabel)
                            pour <span class="font-bold text-green-400">{{ $currentAcademicLabel }}</span>
                        @endif.
                    </p>
                </div>
            </div>
            @if($canManageAcademicData)
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('notes.masse.index', ['date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-5 py-3 text-sm font-semibold text-indigo-500 backdrop-blur-sm transition-all hover:bg-white/20 hover:shadow-lg hover:shadow-black/10">
                    <i class="bi bi-table transition-transform group-hover:scale-110"></i>
                    <span class="hidden sm:inline">Saisie en masse</span>
                    <span class="sm:hidden">Saisie</span>
                </a>
                <a href="{{ route('notes.create', ['date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow-lg shadow-black/20 transition-all hover:scale-105 hover:shadow-xl hover:shadow-black/30">
                    <i class="bi bi-plus-lg transition-transform group-hover:rotate-90"></i>
                    <span class="hidden sm:inline">Nouvelle note</span>
                    <span class="sm:hidden">+</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Panneau de filtres Premium -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl shadow-gray-200/50">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 via-white to-gray-50 px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-500/30">
                        <i class="bi bi-funnel text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Filtres de recherche</h3>
                        <p class="text-xs font-medium text-gray-600">Affinez vos résultats avec des critères précis</p>
                    </div>
                </div>
                <button type="button" onclick="toggleFilters()" class="group flex items-center gap-2 text-sm font-semibold text-indigo-600 transition-colors hover:text-indigo-700 sm:hidden">
                    <span>{{ request()->hasAny(['classe', 'matiere', 'type_devoir', 'search']) ? 'Filtres actifs' : 'Afficher les filtres' }}</span>
                    <i class="bi {{ request()->hasAny(['classe', 'matiere', 'type_devoir', 'search']) ? 'bi-chevron-up' : 'bi-chevron-down' }} transition-transform group-hover:scale-110"></i>
                </button>
            </div>
        </div>

        <div id="filtersPanel" class="{{ request()->hasAny(['classe', 'matiere', 'type_devoir', 'search']) ? '' : 'hidden' }} sm:block">
            <form action="{{ route('notes.index') }}" method="GET" class="p-6">
                @if(request()->query('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                    <!-- Recherche -->
                    <div class="lg:col-span-2">
                        <label class="mb-2.5 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-700">
                            <i class="bi bi-search text-indigo-500"></i>
                            Recherche
                        </label>
                        <div class="group relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Élève, matière, matricule..."
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm font-medium text-gray-900 transition-all placeholder:text-gray-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 group-hover:border-gray-300">
                            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors group-hover:text-indigo-500"></i>
                        </div>
                    </div>

                    <!-- Classe -->
                    <div>
                        <label class="mb-2.5 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-700">
                            <i class="bi bi-door-open text-indigo-500"></i>
                            Classe
                        </label>
                        <div class="group relative">
                            <select name="classe" class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 py-3 pl-3 pr-9 text-sm font-medium text-gray-900 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 group-hover:border-gray-300">
                                <option value="">Toutes les classes</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ $selectedClasse == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nom_classe }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 transition-colors group-hover:text-indigo-500"></i>
                        </div>
                    </div>

                    <!-- Matière -->
                    <div>
                        <label class="mb-2.5 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-700">
                            <i class="bi bi-book text-indigo-500"></i>
                            Matière
                        </label>
                        <div class="group relative">
                            <select name="matiere" class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 py-3 pl-3 pr-9 text-sm font-medium text-gray-900 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 group-hover:border-gray-300">
                                <option value="">Toutes les matières</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" {{ $selectedMatiere == $matiere->id ? 'selected' : '' }}>
                                        {{ $matiere->nom_matiere }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 transition-colors group-hover:text-indigo-500"></i>
                        </div>
                    </div>

                    <!-- Type de devoir -->
                    <div>
                        <label class="mb-2.5 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-700">
                            <i class="bi bi-file-text text-indigo-500"></i>
                            Type
                        </label>
                        <div class="group relative">
                            <select name="type_devoir" class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 py-3 pl-3 pr-9 text-sm font-medium text-gray-900 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 group-hover:border-gray-300">
                                <option value="">Tous les types</option>
                                <option value="devoir" {{ $selectedType === 'devoir' ? 'selected' : '' }}>Devoir</option>
                                <option value="composition" {{ $selectedType === 'composition' ? 'selected' : '' }}>Composition</option>
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 transition-colors group-hover:text-indigo-500"></i>
                        </div>
                    </div>

                    <!-- Semestre -->
                    <div>
                        <label class="mb-2.5 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-700">
                            <i class="bi bi-calendar3 text-indigo-500"></i>
                            Semestre
                        </label>
                        <div class="group relative">
                            <select name="semestre" class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 py-3 pl-3 pr-9 text-sm font-medium text-gray-900 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 group-hover:border-gray-300" onchange="this.form.submit()">
                                <option value="">Tous les semestres</option>
                                @foreach(\App\Models\Note::semestreOptions() as $value => $label)
                                    <option value="{{ $value }}" {{ (string) $selectedSemestre === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 transition-colors group-hover:text-indigo-500"></i>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl bg-gray-50 p-4">
                    <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition-all hover:scale-105 hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl hover:shadow-indigo-500/40">
                        <i class="bi bi-funnel transition-transform group-hover:scale-110"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('notes.index', ['date' => request()->query('date')]) }}" class="group inline-flex items-center gap-2 rounded-xl border-2 border-gray-200 bg-white px-6 py-3 text-sm font-bold text-gray-700 transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        <i class="bi bi-x-lg transition-transform group-hover:rotate-90"></i>
                        Réinitialiser
                    </a>
                    <div class="ml-auto flex items-center gap-2 rounded-xl bg-white px-4 py-2 shadow-sm">
                        <span class="text-sm font-medium text-gray-600">
                            <span class="text-lg font-bold text-indigo-600">{{ $notes->total() }}</span> note(s)
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des notes Premium -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl shadow-gray-200/50">
        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 via-white to-gray-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/30">
                    <i class="bi bi-journal-bookmark-fill text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Registre des notes</h3>
                    <p class="text-xs font-medium text-gray-600">Consultez et gérez toutes les notes</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-indigo-500/30">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ $notes->total() }} note(s)
                </span>
            </div>
        </div>

        <!-- Version mobile : Cartes Premium -->
        <div class="divide-y divide-gray-100 sm:hidden">
            @forelse($notes as $note)
                <div class="group px-4 py-5 transition-all hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-transparent">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm">
                                            {{ substr($note->eleve->nom, 0, 1) }}{{ substr($note->eleve->prenom, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-gray-900">{{ $note->eleve->nom }} {{ $note->eleve->prenom }}</p>
                                            <p class="truncate text-xs font-medium text-gray-500">{{ $note->eleve->matricule ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <span class="shrink-0 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-lg font-bold text-white shadow-lg shadow-indigo-500/30">
                                    {{ number_format($note->note, 2, ',', ' ') }}
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">
                                    <i class="bi bi-book-fill text-[11px]"></i>
                                    {{ $note->matiere->nom_matiere }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg {{ $note->type_devoir === 'composition' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-700' }} px-3 py-1.5 text-xs font-bold">
                                    <i class="bi {{ $note->type_devoir === 'composition' ? 'bi-trophy-fill' : 'bi-file-text-fill' }} text-[11px]"></i>
                                    {{ ucfirst($note->type_devoir) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg {{ $note->semestre === 2 ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }} px-3 py-1.5 text-xs font-bold">
                                    <i class="bi bi-calendar-event-fill text-[11px]"></i>
                                    {{ $note->semestre_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($canManageAcademicData)
                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-3">
                        <a href="{{ route('notes.edit', ['note' => $note, 'date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-2.5 text-xs font-bold text-gray-700 transition-all hover:from-gray-100 hover:to-gray-200 inline-flex">
                            <i class="bi bi-pencil-square"></i>
                            Modifier
                        </a>
                        <form action="{{ route('notes.destroy', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-50 to-red-100 px-4 py-2.5 text-xs font-bold text-red-600 transition-all hover:from-red-100 hover:to-red-200">
                                <i class="bi bi-trash-fill"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-gray-100 to-gray-200 text-gray-400 shadow-inner">
                        <i class="bi bi-journal-text text-3xl"></i>
                    </div>
                    <p class="mt-6 text-lg font-bold text-gray-900">Aucune note enregistrée</p>
                    <p class="mt-2 text-center text-sm font-medium text-gray-600">Ajoutez une note manuellement ou utilisez la saisie en masse.</p>
                </div>
            @endforelse
        </div>

        <!-- Version desktop : Tableau Premium -->
        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 via-gray-100 to-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Élève</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Matière</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Note</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Semestre</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($notes as $note)
                        <tr class="group transition-all hover:bg-gradient-to-r hover:from-indigo-50/30 hover:to-transparent">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-md shadow-indigo-500/20">
                                        {{ substr($note->eleve->nom, 0, 1) }}{{ substr($note->eleve->prenom, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $note->eleve->nom }} {{ $note->eleve->prenom }}</div>
                                        <div class="mt-0.5 text-xs font-medium text-gray-500">{{ $note->eleve->matricule ?? 'Matricule indisponible' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                        <i class="bi bi-book-fill text-xs"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $note->matiere->nom_matiere }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl {{ $note->note >= 10 ? 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white' : 'bg-gradient-to-br from-red-400 to-red-600 text-white' }} text-base font-bold shadow-lg {{ $note->note >= 10 ? 'shadow-emerald-500/30' : 'shadow-red-500/30' }}">
                                    {{ number_format($note->note, 2, ',', ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-lg {{ $note->type_devoir === 'composition' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-700' }} px-3 py-1.5 text-xs font-bold">
                                    <i class="bi {{ $note->type_devoir === 'composition' ? 'bi-trophy-fill' : 'bi-file-text-fill' }} text-[11px]"></i>
                                    {{ ucfirst($note->type_devoir) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-lg {{ $note->semestre === 2 ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }} px-3 py-1.5 text-xs font-bold">
                                    <i class="bi bi-calendar-event-fill text-[11px]"></i>
                                    {{ $note->semestre_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($canManageAcademicData)
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('notes.edit', ['note' => $note, 'date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="group inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-700 transition-all hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="bi bi-pencil-square transition-transform group-hover:scale-110"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('notes.destroy', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="group inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-700 transition-all hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                                            <i class="bi bi-trash-fill transition-transform group-hover:scale-110"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-gray-100 to-gray-200 text-gray-400 shadow-inner">
                                        <i class="bi bi-journal-text text-3xl"></i>
                                    </div>
                                    <p class="mt-6 text-lg font-bold text-gray-900">Aucune note enregistrée</p>
                                    <p class="mt-2 text-sm font-medium text-gray-600">Ajoutez une note manuellement ou utilisez la saisie en masse pour gagner du temps.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notes->hasPages())
            <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                {{ $notes->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('hidden');
}
</script>
@endsection

