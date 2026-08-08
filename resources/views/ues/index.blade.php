@extends('layouts.app')

@section('title', 'Unités d\'enseignement')
@section('breadcrumb', 'Administration / UE')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Administration</p>
                <h2 class="page-title">Organisez les matières en Unités d'Enseignement par classe et par semestre.</h2>
                <p class="page-lead">Chaque UE regroupe des matières et porte un total de crédits (règle 4.2) utilisé pour la validation du semestre et l'attribution du diplôme.</p>
                @if(isset($annee) && $annee)
                    <div class="hero-badges">
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[260px_minmax(0,1fr)]">
        <section class="card">
            <div class="card-header">
                <h4>Classes</h4>
            </div>
            <div class="card-body divide-y divide-slate-100 p-0">
                @forelse($classes as $cl)
                    <a href="{{ route('ues.gerer', ['classe_id' => $cl->id]) }}"
                       class="flex items-center justify-between px-4 py-3 text-sm {{ isset($classe) && $classe->id === $cl->id ? 'bg-cobalt-50 font-semibold text-cobalt-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        {{ $cl->nom_classe }}
                        <i class="bi bi-chevron-right text-xs text-slate-400"></i>
                    </a>
                @empty
                    <p class="p-4 text-sm text-slate-500">Aucune classe disponible.</p>
                @endforelse
            </div>
        </section>

        <div class="space-y-6">
            @if(session('success'))
                <div class="rounded-2xl border border-success-200 bg-success-50 p-4 text-sm text-success-700">{{ session('success') }}</div>
            @endif
            @error('general')
                <div class="rounded-2xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700">{{ $message }}</div>
            @enderror
            @error('matiere_ids')
                <div class="rounded-2xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700">{{ $message }}</div>
            @enderror

            @if(! isset($classe))
                <section class="card">
                    <div class="card-body py-16 text-center text-sm text-slate-500">
                        Sélectionnez une classe pour gérer ses UE.
                    </div>
                </section>
            @elseif(! isset($annee) || ! $annee)
                <section class="card">
                    <div class="card-body py-16 text-center text-sm text-slate-500">
                        Aucune année académique active.
                    </div>
                </section>
            @else
                @foreach([\App\Models\Note::SEMESTRE_1 => 'Semestre 1', \App\Models\Note::SEMESTRE_2 => 'Semestre 2'] as $sem => $label)
                    <section class="card">
                        <div class="card-header">
                            <div>
                                <h4>{{ $label }} — {{ $classe->nom_classe }}</h4>
                                <p class="text-xs text-slate-500">Total crédits configurés : {{ $uesParSemestre[$sem]->sum('credits') }}</p>
                            </div>
                            <form action="{{ route('semestres.update', $semestresParNumero[$sem]) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <label class="text-xs text-slate-500" for="libelle-{{ $sem }}">Libellé sur le bulletin :</label>
                                <input type="text" id="libelle-{{ $sem }}" name="libelle" value="{{ $semestresParNumero[$sem]->libelle }}" class="form-input w-40" placeholder="Semestre 4">
                                <button type="submit" class="btn-secondary">Enregistrer</button>
                            </form>
                        </div>
                        <div class="card-body space-y-4">
                            @forelse($uesParSemestre[$sem] as $ue)
                                <details class="rounded-2xl border border-slate-200 p-4" {{ $loop->first ? 'open' : '' }}>
                                    <summary class="cursor-pointer text-sm font-semibold text-slate-800">
                                        {{ $ue->code }} — {{ $ue->libelle }}
                                        <span class="badge-blue ml-2">{{ $ue->credits }} crédits</span>
                                    </summary>
                                    <form action="{{ route('ues.update', $ue) }}" method="POST" class="mt-4 space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <div class="form-field">
                                                <label class="form-label">Code</label>
                                                <input type="text" name="code" value="{{ $ue->code }}" class="form-input" required>
                                            </div>
                                            <div class="form-field sm:col-span-2">
                                                <label class="form-label">Libellé</label>
                                                <input type="text" name="libelle" value="{{ $ue->libelle }}" class="form-input" required>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Semestre</label>
                                                <select name="semestre" class="form-select">
                                                    <option value="1" {{ $ue->semestre->numero === 1 ? 'selected' : '' }}>Semestre 1</option>
                                                    <option value="2" {{ $ue->semestre->numero === 2 ? 'selected' : '' }}>Semestre 2</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="form-label">Matières (les crédits de l'UE = somme des crédits des matières cochées)</p>
                                            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                                @foreach($matieres as $matiere)
                                                    @php $autreUe = $matiereAssigneeA[$matiere->id] ?? null; @endphp
                                                    <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm {{ $autreUe && $autreUe->id !== $ue->id ? 'opacity-60' : '' }}">
                                                        <input type="checkbox" name="matiere_ids[]" value="{{ $matiere->id }}"
                                                               {{ $ue->matieres->contains($matiere->id) ? 'checked' : '' }}>
                                                        {{ $matiere->nom_matiere }} <span class="text-xs text-slate-400">({{ $matiere->pivot->credits }} cr.)</span>
                                                        @if($autreUe && $autreUe->id !== $ue->id)
                                                            <span class="text-xs text-danger-600">déjà dans {{ $autreUe->code }}</span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="flex justify-between gap-3">
                                            <button type="submit" class="btn-primary">Enregistrer</button>
                                        </div>
                                    </form>
                                    <form action="{{ route('ues.destroy', $ue) }}" method="POST" onsubmit="return confirm('Supprimer cette UE ?')" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary text-danger-600">Supprimer l'UE</button>
                                    </form>
                                </details>
                            @empty
                                <p class="text-sm text-slate-500">Aucune UE pour ce semestre.</p>
                            @endforelse

                            <details class="rounded-2xl border border-dashed border-cobalt-300 p-4">
                                <summary class="cursor-pointer text-sm font-semibold text-cobalt-700">
                                    <i class="bi bi-plus-lg"></i> Ajouter une UE en {{ $label }}
                                </summary>
                                <form action="{{ route('ues.store') }}" method="POST" class="mt-4 space-y-4">
                                    @csrf
                                    <input type="hidden" name="classe_id" value="{{ $classe->id }}">
                                    <input type="hidden" name="semestre" value="{{ $sem }}">
                                    <div class="grid gap-4 sm:grid-cols-3">
                                        <div class="form-field">
                                            <label class="form-label">Code <span class="req">*</span></label>
                                            <input type="text" name="code" class="form-input" placeholder="UE{{ $sem === 1 ? '5' : '6' }}-1" required>
                                        </div>
                                        <div class="form-field sm:col-span-2">
                                            <label class="form-label">Libellé <span class="req">*</span></label>
                                            <input type="text" name="libelle" class="form-input" required>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="form-label">Matières</p>
                                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                            @foreach($matieres as $matiere)
                                                @php $autreUe = $matiereAssigneeA[$matiere->id] ?? null; @endphp
                                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm {{ $autreUe ? 'opacity-60' : '' }}">
                                                    <input type="checkbox" name="matiere_ids[]" value="{{ $matiere->id }}">
                                                    {{ $matiere->nom_matiere }} <span class="text-xs text-slate-400">({{ $matiere->pivot->credits }} cr.)</span>
                                                    @if($autreUe)
                                                        <span class="text-xs text-danger-600">déjà dans {{ $autreUe->code }}</span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button type="submit" class="btn-primary">Créer l'UE</button>
                                </form>
                            </details>
                        </div>
                    </section>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
