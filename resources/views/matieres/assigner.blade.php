@extends('layouts.app')

@section('title', 'Assigner des matières')
@section('breadcrumb', 'Administration / Matières / Assignation')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Administration</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Assigner des matières</h2>
            <p class="mt-2 max-w-3xl text-sm text-gray-500">Définissez quelles matières sont enseignées dans chaque classe et ajustez leur coefficient pour l'année académique en cours.</p>
        </div>
        <a href="{{ route('matieres.index') }}" class="btn-secondary self-start lg:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour au catalogue
        </a>
    </div>

    @if(! $annee)
        <div class="alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Aucune année académique active. Impossible d'assigner des matières tant qu'un contexte scolaire n'est pas défini.</span>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[280px_minmax(0,1fr)]">
        <div class="card overflow-hidden">
            <div class="card-header">
                <h4>Choisir une classe</h4>
                <span class="badge-gray">{{ $classes->count() }} classe(s)</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($classes as $cl)
                    <form method="GET" action="{{ route('matieres.assigner.classe') }}">
                        <button type="submit"
                                name="classe_id"
                                value="{{ $cl->id }}"
                                class="flex w-full items-center justify-between gap-3 px-4 py-4 text-left transition-colors {{ isset($classe) && $classe->id === $cl->id ? 'bg-brand-50 text-brand-700' : 'hover:bg-slate-50' }}">
                            <span>
                                <span class="block text-sm font-semibold">{{ $cl->nom_classe }}</span>
                                <span class="mt-1 block text-xs {{ isset($classe) && $classe->id === $cl->id ? 'text-brand-600' : 'text-gray-400' }}">Ouvrir l'assignation</span>
                            </span>
                            <i class="bi {{ isset($classe) && $classe->id === $cl->id ? 'bi-chevron-right' : 'bi-arrow-right-short' }} text-sm"></i>
                        </button>
                    </form>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500">Aucune classe disponible.</div>
                @endforelse
            </div>
        </div>

        <div>
            @if(isset($classe) && isset($toutesLesMatieres))
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h4>Matières de {{ $classe->nom_classe }}</h4>
                            <p class="mt-1 text-xs text-gray-400">Sélectionnez les matières et définissez leur coefficient.</p>
                        </div>
                        @if($annee)
                            <span class="badge-blue">{{ $annee->libelle }}</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('matieres.assigner.store') }}" id="assignForm">
                        @csrf
                        <input type="hidden" name="classe_id" value="{{ $classe->id }}">

                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="w-14">
                                            <input type="checkbox"
                                                   id="checkAll"
                                                   class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                        </th>
                                        <th>Matière</th>
                                        <th class="text-center">Coefficient</th>
                                        <th class="text-center">Statut</th>
                                    </tr>
                                </thead>
                                <tbody id="matieresBody">
                                    @foreach($toutesLesMatieres as $index => $mat)
                                        @php
                                            $assigne = $matieresAssignees->get($mat->id);
                                            $coef = $assigne ? $assigne->pivot->coefficient : 1;
                                        @endphp
                                        <tr id="row-{{ $mat->id }}" class="matiere-row {{ $assigne ? 'bg-brand-50/60' : '' }}">
                                            <td>
                                                <input type="checkbox"
                                                       class="matiere-check h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                                       name="matieres[{{ $index }}][id]"
                                                       value="{{ $mat->id }}"
                                                       data-index="{{ $index }}"
                                                       {{ $assigne ? 'checked' : '' }}
                                                       onchange="toggleRow(this)">
                                            </td>
                                            <td data-role="matiere-name" class="{{ $assigne ? 'font-semibold text-gray-900' : 'text-gray-700' }}">
                                                <div>{{ $mat->nom_matiere }}</div>
                                                <div class="mt-1 text-xs text-gray-400">Catalogue global</div>
                                            </td>
                                            <td class="text-center">
                                                <input type="number"
                                                       name="matieres[{{ $index }}][coef]"
                                                       value="{{ $coef }}"
                                                       min="1"
                                                       max="20"
                                                       class="coef-input mx-auto h-10 w-20 rounded-lg border border-gray-200 bg-white px-3 text-center text-sm text-gray-900 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 disabled:bg-gray-100 disabled:text-gray-400"
                                                       {{ ! $assigne ? 'disabled' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                @if($assigne)
                                                    <span class="badge-green status-badge" id="badge-{{ $mat->id }}">Assignée</span>
                                                @else
                                                    <span class="badge-gray status-badge" id="badge-{{ $mat->id }}">Non assignée</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-col gap-4 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-gray-500">
                                <span id="nbAssignees" class="font-semibold text-gray-900">{{ $matieresAssignees->count() }}</span>
                                matière(s) assignée(s) sur {{ $toutesLesMatieres->count() }}
                            </p>
                            <button type="submit" class="btn-primary justify-center" {{ ! $annee ? 'disabled' : '' }}>
                                <i class="bi bi-save"></i>
                                Enregistrer l'assignation
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="card">
                    <div class="card-body py-14 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                            <i class="bi bi-diagram-3 text-3xl"></i>
                        </div>
                        <p class="mt-5 text-sm font-medium text-gray-700">Sélectionnez une classe</p>
                        <p class="mt-1 text-sm text-gray-500">Le panneau d'assignation apparaîtra ici avec toutes les matières disponibles.</p>
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