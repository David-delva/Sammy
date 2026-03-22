@extends('layouts.app')

@section('title', $eleve->nom . ' ' . $eleve->prenom)
@section('breadcrumb', 'Scolarite / Eleves / Detail')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Profil eleve</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</h2>
            <p class="mt-2 text-sm text-gray-500">Matricule {{ $eleve->matricule }} - Consultez les notes et l'etat scolaire de l'annee selectionnee.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                <i class="bi bi-pencil"></i>
                Modifier
            </a>
            <a href="{{ route('eleves.historique', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                <i class="bi bi-clock-history"></i>
                Historique
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
        <div class="card overflow-hidden">
            <div class="card-body text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                    <i class="bi bi-person-circle text-5xl"></i>
                </div>
                <h3 class="mt-5 text-xl font-semibold tracking-tight text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $eleve->matricule }}</p>
            </div>
            <div class="divide-y divide-gray-100 border-t border-gray-100">
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Classe actuelle</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit' }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Date de naissance</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->date_naissance->format('d/m/Y') }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Sexe</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Feminin' }}</p>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h4>Notes de l'annee</h4>
                    <p class="mt-1 text-xs text-gray-400">Toutes les evaluations enregistrees pour le contexte actif.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 1, 'date' => request()->query('date')]) }}" class="btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Bulletin S1
                    </a>
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 2, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Bulletin S2
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Matiere</th>
                            <th>Type</th>
                            <th>Semestre</th>
                            <th class="text-center">Note / 20</th>
                            <th class="text-right">Date de saisie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eleve->notes as $note)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $note->matiere->nom_matiere }}</div>
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
                                <td class="text-center">
                                    <span class="text-base font-semibold {{ $note->note >= 10 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ number_format($note->note, 2, ',', ' ') }}
                                    </span>
                                </td>
                                <td class="text-right text-sm text-gray-400">{{ $note->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                                            <i class="bi bi-journal-x text-2xl"></i>
                                        </div>
                                        <p class="mt-4 text-sm font-medium text-gray-700">Aucune note enregistree</p>
                                        <p class="mt-1 text-sm text-gray-500">Les notes de cet eleve apparaitront ici des qu'elles seront saisies.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection