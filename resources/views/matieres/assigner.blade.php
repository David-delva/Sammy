@extends('layouts.app')

@section('title', 'Assigner des matières')
@section('breadcrumb', 'Administration / Matières / Assignation')

@section('content')
<div class="space-y-6">
    <!-- En-tête de page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="bi bi-book-fill text-xl"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
                    <h2 class="mt-0.5 text-2xl font-semibold tracking-tight text-gray-900">Assigner des matières</h2>
                </div>
            </div>
            <p class="mt-3 max-w-3xl text-sm text-gray-500">Définissez quelles matières sont enseignées dans chaque classe et ajustez leur coefficient pour l'année académique en cours.</p>
        </div>
        <a href="{{ route('matieres.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50 hover:shadow sm:self-auto">
            <i class="bi bi-arrow-left transition-transform group-hover:-translate-x-0.5"></i>
            Retour au catalogue
        </a>
    </div>

    <!-- Alerte année académique -->
    @if(! $annee)
        <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>
            <div class="flex-1">
                <p class="text-sm font-medium text-amber-900">Aucune année académique active</p>
                <p class="mt-1 text-sm text-amber-700">Impossible d'assigner des matières tant qu'un contexte scolaire n'est pas défini.</p>
            </div>
        </div>
    @endif

    <!-- Grille principale -->
    <div class="grid gap-6 xl:grid-cols-[280px_minmax(0,1fr)]">
        <!-- Panneau latéral : Liste des classes (visible seulement sur desktop) -->
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm sm:block">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4">
                <h3 class="text-base font-semibold text-gray-900">Choisir une classe</h3>
                <p class="mt-0.5 text-xs text-gray-500">{{ $classes->count() }} classe(s) disponible(s)</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($classes as $cl)
                    <form method="GET" action="{{ route('matieres.assigner.classe') }}">
                        <button type="submit"
                                name="classe_id"
                                value="{{ $cl->id }}"
                                class="flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left transition-all {{ isset($classe) && $classe->id === $cl->id ? 'bg-brand-50 text-brand-700' : 'hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ isset($classe) && $classe->id === $cl->id ? 'bg-brand-100 text-brand-600' : 'bg-gray-100 text-gray-500' }}">
                                    <i class="bi bi-door-open text-sm"></i>
                                </span>
                                <div>
                                    <span class="block text-sm font-semibold">{{ $cl->nom_classe }}</span>
                                    <span class="mt-0.5 block text-xs {{ isset($classe) && $classe->id === $cl->id ? 'text-brand-600' : 'text-gray-400' }}">Ouvrir l'assignation</span>
                                </div>
                            </div>
                            <i class="bi {{ isset($classe) && $classe->id === $cl->id ? 'bi-chevron-right' : 'bi-arrow-right-short' }} text-lg text-gray-400"></i>
                        </button>
                    </form>
                @empty
                    <div class="flex flex-col items-center justify-center px-4 py-8 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <i class="bi bi-inbox text-xl"></i>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-700">Aucune classe disponible</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Version mobile : Sélecteur de classe -->
        <div class="sm:hidden">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Choisir une classe</h3>
                    <p class="mt-0.5 text-xs text-gray-500">{{ $classes->count() }} classe(s) disponible(s)</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($classes as $cl)
                        <form method="GET" action="{{ route('matieres.assigner.classe') }}">
                            <button type="submit"
                                    name="classe_id"
                                    value="{{ $cl->id }}"
                                    class="flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left transition-all {{ isset($classe) && $classe->id === $cl->id ? 'bg-brand-50 text-brand-700' : 'hover:bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ isset($classe) && $classe->id === $cl->id ? 'bg-brand-100 text-brand-600' : 'bg-gray-100 text-gray-500' }}">
                                        <i class="bi bi-door-open text-sm"></i>
                                    </span>
                                    <div>
                                        <span class="block text-sm font-semibold">{{ $cl->nom_classe }}</span>
                                        <span class="mt-0.5 block text-xs {{ isset($classe) && $classe->id === $cl->id ? 'text-brand-600' : 'text-gray-400' }}">Ouvrir l'assignation</span>
                                    </div>
                                </div>
                                <i class="bi {{ isset($classe) && $classe->id === $cl->id ? 'bi-chevron-right' : 'bi-arrow-right-short' }} text-lg text-gray-400"></i>
                            </button>
                        </form>
                    @empty
                        <div class="flex flex-col items-center justify-center px-4 py-8 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                <i class="bi bi-inbox text-xl"></i>
                            </div>
                            <p class="mt-3 text-sm font-medium text-gray-700">Aucune classe disponible</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Panneau principal : Assignation des matières -->
        <div>
            @if(isset($classe) && isset($toutesLesMatieres))
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <!-- En-tête -->
                    <div class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Matières de {{ $classe->nom_classe }}</h3>
                            <p class="mt-0.5 text-xs text-gray-500">Sélectionnez les matières et définissez leur coefficient.</p>
                        </div>
                        @if($annee)
                            <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3.5 py-1.5 text-sm font-medium text-blue-700">
                                <i class="bi bi-calendar-check"></i>
                                {{ $annee->libelle }}
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('matieres.assigner.store') }}" id="assignForm">
                        @csrf
                        <input type="hidden" name="classe_id" value="{{ $classe->id }}">

                        <!-- Version mobile : Cartes -->
                        <div class="divide-y divide-gray-100 sm:hidden">
                            @foreach($toutesLesMatieres as $index => $mat)
                                @php
                                    $assigne = $matieresAssignees->get($mat->id);
                                    $coef = $assigne ? $assigne->pivot->coefficient : 1;
                                @endphp
                                <div class="px-4 py-4" id="row-{{ $mat->id }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex min-w-0 flex-1 items-start gap-3">
                                            <div>
                                                <input type="checkbox"
                                                       class="matiere-check mt-1 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                                       name="matieres[{{ $index }}][id]"
                                                       value="{{ $mat->id }}"
                                                       data-index="{{ $index }}"
                                                       {{ $assigne ? 'checked' : '' }}
                                                       onchange="toggleRow(this)">
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="truncate text-sm font-semibold text-gray-900" data-role="matiere-name">{{ $mat->nom_matiere }}</p>
                                                    @if($assigne)
                                                        <span class="shrink-0 rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 status-badge">
                                                            <i class="bi bi-check-circle-fill text-[10px]"></i>
                                                            Assignée
                                                        </span>
                                                    @else
                                                        <span class="shrink-0 rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 status-badge">
                                                            Non assignée
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mt-0.5 truncate text-xs text-gray-400">Catalogue global</p>
                                                <div class="mt-3 flex items-center gap-3">
                                                    <label class="text-xs text-gray-500">Coefficient :</label>
                                                    <input type="number"
                                                           name="matieres[{{ $index }}][coef]"
                                                           value="{{ $coef }}"
                                                           min="1"
                                                           max="20"
                                                           class="coef-input h-9 w-20 rounded-lg border border-gray-200 bg-white px-3 text-center text-sm text-gray-900 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
                                                           {{ ! $assigne ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Version desktop : Tableau -->
                        <div class="hidden overflow-x-auto sm:block">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="w-14 px-6 py-3">
                                            <input type="checkbox"
                                                   id="checkAll"
                                                   class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Matière</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Coefficient</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="matieresBody">
                                    @foreach($toutesLesMatieres as $index => $mat)
                                        @php
                                            $assigne = $matieresAssignees->get($mat->id);
                                            $coef = $assigne ? $assigne->pivot->coefficient : 1;
                                        @endphp
                                        <tr id="row-{{ $mat->id }}" class="transition-colors {{ $assigne ? 'bg-brand-50/60' : '' }}">
                                            <td class="px-6 py-4">
                                                <input type="checkbox"
                                                       class="matiere-check h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                                       name="matieres[{{ $index }}][id]"
                                                       value="{{ $mat->id }}"
                                                       data-index="{{ $index }}"
                                                       {{ $assigne ? 'checked' : '' }}
                                                       onchange="toggleRow(this)">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="{{ $assigne ? 'font-semibold text-gray-900' : 'text-gray-700' }}" data-role="matiere-name">
                                                    {{ $mat->nom_matiere }}
                                                </div>
                                                <div class="mt-1 text-xs text-gray-400">Catalogue global</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <input type="number"
                                                       name="matieres[{{ $index }}][coef]"
                                                       value="{{ $coef }}"
                                                       min="1"
                                                       max="20"
                                                       class="coef-input mx-auto h-10 w-20 rounded-lg border border-gray-200 bg-white px-3 text-center text-sm text-gray-900 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
                                                       {{ ! $assigne ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($assigne)
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 status-badge" id="badge-{{ $mat->id }}">
                                                        <i class="bi bi-check-circle-fill text-[10px]"></i>
                                                        Assignée
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 status-badge" id="badge-{{ $mat->id }}">
                                                        Non assignée
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer avec actions -->
                        <div class="flex flex-col gap-4 border-t border-gray-100 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                            <p class="text-sm text-gray-600">
                                <span id="nbAssignees" class="font-semibold text-gray-900">{{ $matieresAssignees->count() }}</span>
                                matière(s) assignée(s) sur {{ $toutesLesMatieres->count() }}
                            </p>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-700 to-blue-800 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/25 transition-all hover:from-blue-800 hover:to-blue-900 hover:shadow-lg hover:shadow-blue-500/30 disabled:cursor-not-allowed disabled:from-gray-400 disabled:to-gray-500 disabled:shadow-none" {{ ! $annee ? 'disabled' : '' }}>
                                <i class="bi bi-save"></i>
                                Enregistrer l'assignation
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-col items-center justify-center px-6 py-16">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                            <i class="bi bi-diagram-3 text-3xl"></i>
                        </div>
                        <p class="mt-5 text-base font-semibold text-gray-900">Sélectionnez une classe</p>
                        <p class="mt-1.5 text-sm text-gray-500">Le panneau d'assignation apparaîtra ici avec toutes les matières disponibles.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleRow(checkbox) {
    const row = document.getElementById('row-' + checkbox.value);
    const badge = document.getElementById('badge-' + checkbox.value);
    const coefInput = row.querySelector('.coef-input');
    const nameCell = row.querySelector('[data-role="matiere-name"]');

    if (checkbox.checked) {
        row.classList.add('bg-brand-50/60');
        nameCell.classList.add('font-semibold', 'text-gray-900');
        coefInput.disabled = false;
        coefInput.classList.remove('bg-gray-100', 'text-gray-400');
        badge.className = 'badge-green status-badge';
        badge.textContent = 'Assignée';
    } else {
        row.classList.remove('bg-brand-50/60');
        nameCell.classList.remove('font-semibold', 'text-gray-900');
        coefInput.disabled = true;
        coefInput.classList.remove('border-red-300', 'ring-2', 'ring-red-200');
        badge.className = 'badge-gray status-badge';
        badge.textContent = 'Non assignée';
    }

    updateCounter();
    syncCheckAll();
}

function updateCounter() {
    const nb = document.querySelectorAll('.matiere-check:checked').length;
    const counter = document.getElementById('nbAssignees');
    if (counter) counter.textContent = nb;
}

function syncCheckAll() {
    const checkboxes = Array.from(document.querySelectorAll('.matiere-check'));
    const checkAll = document.getElementById('checkAll');
    if (!checkboxes.length || !checkAll) return;
    checkAll.checked = checkboxes.every(cb => cb.checked);
}

document.getElementById('checkAll')?.addEventListener('change', function () {
    document.querySelectorAll('.matiere-check').forEach(cb => {
        if (cb.checked !== this.checked) {
            cb.checked = this.checked;
            toggleRow(cb);
        }
    });
});

document.querySelectorAll('.coef-input').forEach(input => {
    input.addEventListener('input', function () {
        this.classList.remove('border-red-300', 'ring-2', 'ring-red-200');
    });
});

document.getElementById('assignForm')?.addEventListener('submit', function (e) {
    let invalid = false;

    document.querySelectorAll('.matiere-check:checked').forEach(cb => {
        const row = document.getElementById('row-' + cb.value);
        const coef = row.querySelector('.coef-input');

        if (!coef.value || parseInt(coef.value, 10) < 1) {
            coef.classList.add('border-red-300', 'ring-2', 'ring-red-200');
            invalid = true;
        }
    });

    if (invalid) {
        e.preventDefault();
    }
});

syncCheckAll();
updateCounter();
</script>
@endsection