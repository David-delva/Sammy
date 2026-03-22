@extends('layouts.app')

@section('title', 'Élèves')
@section('breadcrumb', 'Scolarité / Élèves')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Scolarité</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Élèves</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">
                Gérez les inscriptions et consultez les profils des élèves
                @if(isset($annee) && $annee)
                    pour <span class="font-medium text-gray-700">{{ $annee->libelle }}</span>
                @endif.
            </p>
        </div>
        <a href="{{ route('eleves.create', ['date' => request()->query('date')]) }}" class="btn-primary self-start lg:self-auto">
            <i class="bi bi-person-plus"></i>
            Inscrire un élève
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4>Liste des élèves</h4>
            <span class="badge-blue">{{ $eleves->total() }} élève(s)</span>
        </div>
        <div class="overflow-x-auto">
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
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $eleves->links() }}
            </div>
        @endif
    </div>
</div>
@endsection