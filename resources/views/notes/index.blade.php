@extends('layouts.app')

@section('title', 'Notes')
@section('breadcrumb', 'Evaluations / Notes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Evaluations</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Notes</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">
                Suivez les notes enregistrees
                @if(isset($currentAcademicLabel) && $currentAcademicLabel)
                    pour <span class="font-medium text-gray-700">{{ $currentAcademicLabel }}</span>
                @endif.
            </p>
        </div>
        <div class="flex flex-col gap-3 lg:items-end">
            <form action="{{ route('notes.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row">
                @if(request()->query('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif
                <select name="semestre" class="form-select min-w-[180px]" onchange="this.form.submit()">
                    <option value="">Tous les semestres</option>
                    @foreach(\App\Models\Note::semestreOptions() as $value => $label)
                        <option value="{{ $value }}" {{ (string) $selectedSemestre === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('notes.masse.index', ['date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-secondary justify-center">
                    <i class="bi bi-table"></i>
                    Saisie en masse
                </a>
                <a href="{{ route('notes.create', ['date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-primary justify-center">
                    <i class="bi bi-plus-lg text-sm"></i>
                    Nouvelle note
                </a>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4>Registre des notes</h4>
            <span class="badge-blue">{{ $notes->total() }} note(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Eleve</th>
                        <th>Matiere</th>
                        <th class="text-center">Note</th>
                        <th>Type</th>
                        <th>Semestre</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                        <tr>
                            <td>
                                <div class="font-semibold text-gray-900">{{ $note->eleve->nom }} {{ $note->eleve->prenom }}</div>
                                <div class="mt-1 text-xs text-gray-400">{{ $note->eleve->matricule ?? 'Matricule indisponible' }}</div>
                            </td>
                            <td>{{ $note->matiere->nom_matiere }}</td>
                            <td class="text-center">
                                <span class="text-base font-semibold {{ $note->note >= 10 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format($note->note, 2, ',', ' ') }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $note->type_devoir === 'composition' ? 'badge-purple' : 'badge-gray' }}">
                                    {{ ucfirst($note->type_devoir) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $note->semestre === 2 ? 'badge-yellow' : 'badge-blue' }}">
                                    {{ $note->semestre_label }}
                                </span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('notes.edit', ['note' => $note, 'date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('notes.destroy', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                                        <i class="bi bi-journal-text text-2xl"></i>
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune note enregistree</p>
                                    <p class="mt-1 text-sm text-gray-500">Ajoutez une note manuellement ou utilisez la saisie en masse pour gagner du temps.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notes->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $notes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection