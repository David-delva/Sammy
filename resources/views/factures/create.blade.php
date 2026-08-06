@extends('layouts.app')

@section('title', 'Nouvelle facture')
@section('breadcrumb', 'Scolarite / Factures / Creation')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Facturation</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Emettre une facture d'inscription</h2>
            <p class="mt-2 text-sm text-gray-500">Rattachez la facture a une inscription annuelle et preparez un document imprimable.</p>
        </div>
        <a href="{{ route('factures.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour aux factures
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Creation de facture</h4>
                <p class="mt-1 text-xs text-gray-400">L'inscription choisie doit appartenir a l'annee academique actuellement selectionnee.</p>
            </div>
            @if($annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            @if($inscriptions->isEmpty())
                <div class="empty-state">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="bi bi-person-plus text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune inscription disponible</p>
                    <p class="mt-2 text-sm text-slate-500">Inscrivez d'abord un eleve dans l'annee selectionnee pour pouvoir creer une facture.</p>
                </div>
            @else
                <form action="{{ route('factures.store', ['date' => request()->query('date')]) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="form-field md:col-span-2">
                            <label for="inscription_id" class="form-label">Inscription concernee <span class="req">*</span></label>
                            <select id="inscription_id" name="inscription_id" class="form-select @error('inscription_id') error @enderror" required>
                                <option value="">Selectionner une inscription</option>
                                @foreach($inscriptions as $inscription)
                                    <option value="{{ $inscription->id }}" {{ (string) old('inscription_id', $selectedInscription?->id) === (string) $inscription->id ? 'selected' : '' }}>
                                        {{ $inscription->eleve->matricule }} - {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }} / {{ $inscription->classe->nom_classe }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inscription_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="libelle" class="form-label">Libelle <span class="req">*</span></label>
                            <input id="libelle" type="text" name="libelle" value="{{ old('libelle', "Frais d'inscription annuelle") }}" class="form-input @error('libelle') error @enderror" required>
                            @error('libelle')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="montant" class="form-label">Montant (FCFA) <span class="req">*</span></label>
                            <input id="montant" type="number" min="0" step="0.01" name="montant" value="{{ old('montant') }}" class="form-input @error('montant') error @enderror" required>
                            @error('montant')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="date_emission" class="form-label">Date d'emission <span class="req">*</span></label>
                            <input id="date_emission" type="date" name="date_emission" value="{{ old('date_emission', now()->toDateString()) }}" class="form-input @error('date_emission') error @enderror" required>
                            @error('date_emission')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="date_echeance" class="form-label">Date d'echeance</label>
                            <input id="date_echeance" type="date" name="date_echeance" value="{{ old('date_echeance') }}" class="form-input @error('date_echeance') error @enderror">
                            @error('date_echeance')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field md:col-span-2">
                            <label for="description" class="form-label">Details</label>
                            <textarea id="description" name="description" rows="4" class="form-input @error('description') error @enderror" placeholder="Precisez le contenu de la facture, les frais inclus ou toute note utile pour l'impression.">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                        <a href="{{ route('factures.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">Annuler</a>
                        <button type="submit" class="btn-primary justify-center">
                            <i class="bi bi-receipt"></i>
                            Creer la facture
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

