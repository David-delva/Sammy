@extends('layouts.app')

@section('title', $facture->numero)
@section('breadcrumb', 'Scolarite / Factures / Detail')

@section('content')
@php
    $eleve = $facture->inscription->eleve;
    $classe = $facture->inscription->classe;
    $annee = $facture->inscription->anneeAcademique;
    $statusLabels = \App\Models\Facture::statutOptions();
    $manualStatusLabels = \App\Models\Facture::manualStatutOptions();
    $paymentModes = \App\Models\Billing\PaiementFacture::modeOptions();
    $montantPaye = (float) $facture->montant_paye;
    $soldeRestant = (float) $facture->solde_restant;
    $paiements = $facture->paiements->sortByDesc(fn ($paiement) => (string) $paiement->date_paiement)->values();
@endphp

<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Facturation</p>
                <h2 class="page-title">{{ $facture->numero }} garde une trace claire de l'inscription de {{ $eleve->nom }} {{ $eleve->prenom }}.</h2>
                <p class="page-lead">Consultez le montant, les paiements enregistres, le solde restant et lancez l'impression PDF depuis une seule fiche.</p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-receipt"></i>{{ $facture->libelle }}</span>
                    <span class="hero-badge"><i class="bi bi-building"></i>{{ $classe->nom_classe }}</span>
                    <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee?->libelle }}</span>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('factures.pdf', ['facture' => $facture, 'date' => request()->query('date')]) }}" class="btn-primary justify-center shadow-lg shadow-cobalt-600/20 sm:w-auto">
                        <i class="bi bi-printer"></i>
                        Imprimer la facture
                    </a>
                    <a href="{{ route('factures.index', ['date' => request()->query('date'), 'eleve' => $eleve->id]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-arrow-left"></i>
                        Retour a la liste
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Montant</p>
                    <h3 class="mt-3 text-3xl font-semibold tracking-tight text-white">{{ number_format((float) $facture->montant, 2, ',', ' ') }} FCFA</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Date d'emission : {{ $facture->date_emission?->format('d/m/Y') }} · Echeance : {{ $facture->date_echeance?->format('d/m/Y') ?? '--' }}</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Paye</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($montantPaye, 2, ',', ' ') }}</p>
                        <p class="mt-1 text-sm text-white/65">FCFA enregistres</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Solde</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($soldeRestant, 2, ',', ' ') }}</p>
                        <p class="mt-1 text-sm text-white/65">FCFA restants</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_360px]">
        <section class="space-y-6">
            <section class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h4>Informations de facturation</h4>
                        <p class="text-xs text-slate-500">Resume de l'inscription concernee et contenu du document.</p>
                    </div>
                    <span class="badge-gray">{{ $statusLabels[$facture->statut] ?? ucfirst($facture->statut) }}</span>
                </div>
                <div class="card-body space-y-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="detail-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Eleve</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $eleve->matricule }}</p>
                        </div>
                        <div class="detail-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Classe</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $classe->nom_classe }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $annee?->libelle }}</p>
                        </div>
                        <div class="detail-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Date d'emission</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $facture->date_emission?->format('d/m/Y') }}</p>
                        </div>
                        <div class="detail-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Dernier paiement</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $facture->date_paiement?->format('d/m/Y') ?? '--' }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Montant total</p>
                            <p class="mt-3 text-base font-semibold text-slate-900">{{ number_format((float) $facture->montant, 2, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Montant paye</p>
                            <p class="mt-3 text-base font-semibold text-slate-900">{{ number_format($montantPaye, 2, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Solde restant</p>
                            <p class="mt-3 text-base font-semibold text-slate-900">{{ number_format($soldeRestant, 2, ',', ' ') }} FCFA</p>
                        </div>
                    </div>

                    <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Libelle</p>
                        <p class="mt-3 text-base font-semibold text-slate-900">{{ $facture->libelle }}</p>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $facture->description ?: 'Aucun detail complementaire n a ete renseigne pour cette facture.' }}</p>
                    </div>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h4>Historique des paiements</h4>
                        <p class="text-xs text-slate-500">Chaque paiement garde son mode, sa date et sa reference.</p>
                    </div>
                    <span class="badge-gray">{{ $paiements->count() }} operation(s)</span>
                </div>

                <div class="card-body p-0">
                    @if($paiements->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-slate-500">
                            Aucun paiement n'a encore ete enregistre pour cette facture.
                        </div>
                    @else
                        <div class="hidden overflow-x-auto sm:block">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Mode</th>
                                        <th>Reference</th>
                                        <th>Saisi par</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paiements as $paiement)
                                        <tr>
                                            <td>{{ $paiement->date_paiement?->format('d/m/Y') }}</td>
                                            <td class="font-semibold text-slate-900">{{ number_format((float) $paiement->montant, 2, ',', ' ') }} FCFA</td>
                                            <td>{{ $paymentModes[$paiement->mode_paiement] ?? ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
                                            <td>{{ $paiement->reference ?: '--' }}</td>
                                            <td>{{ $paiement->creator?->name ?? 'Systeme' }}</td>
                                            <td>
                                                @if($canManageAcademicData && $facture->statut !== \App\Models\Facture::STATUT_ANNULEE)
                                                    <div class="flex justify-end">
                                                        <form action="{{ route('factures.paiements.destroy', ['facture' => $facture, 'paiement' => $paiement, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Supprimer ce paiement ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-secondary btn-sm">
                                                                <i class="bi bi-trash"></i>
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mobile-list sm:hidden">
                            @foreach($paiements as $paiement)
                                <article class="mobile-list-item">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ number_format((float) $paiement->montant, 2, ',', ' ') }} FCFA</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $paiement->date_paiement?->format('d/m/Y') }} · {{ $paymentModes[$paiement->mode_paiement] ?? ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</p>
                                            <p class="mt-1 text-xs text-slate-500">Reference : {{ $paiement->reference ?: '--' }}</p>
                                        </div>
                                        @if($canManageAcademicData && $facture->statut !== \App\Models\Facture::STATUT_ANNULEE)
                                            <form action="{{ route('factures.paiements.destroy', ['facture' => $facture, 'paiement' => $paiement, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Supprimer ce paiement ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </section>

        <aside class="surface-card" data-tilt>
            <div class="relative space-y-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.26em] text-cobalt-600">Actions</p>
                    <h4 class="mt-3 text-xl font-semibold tracking-tight text-slate-900">Statut, paiements et impression</h4>
                </div>

                @if($canManageAcademicData)
                    <form action="{{ route('factures.status.update', ['facture' => $facture, 'date' => request()->query('date')]) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="form-field">
                            <label for="statut" class="form-label">Mettre a jour le statut</label>
                            <select id="statut" name="statut" class="form-select @error('statut') error @enderror" aria-describedby="statut-help {{ $errors->has('statut') ? 'statut-error' : '' }}">
                                @foreach($manualStatusLabels as $value => $label)
                                    <option value="{{ $value }}" {{ $facture->statut === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p id="statut-help" class="mt-2 text-xs text-slate-500">Le statut partiellement paye est calcule automatiquement a partir des paiements saisis.</p>
                            @error('statut')
                                <p id="statut-error" class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn-primary w-full justify-center">
                            <i class="bi bi-check2-circle"></i>
                            Enregistrer le statut
                        </button>
                    </form>

                    <div class="border-t border-slate-100 pt-5">
                        <h5 class="text-sm font-semibold text-slate-900">Enregistrer un paiement</h5>
                        <p class="mt-1 text-xs text-slate-500">Ajoutez un paiement partiel ou complet. Le solde et le statut seront recalcules automatiquement.</p>

                        @if($facture->statut === \App\Models\Facture::STATUT_ANNULEE)
                            <div class="mt-4 rounded-2xl border border-warning-200 bg-warning-50 px-4 py-4 text-sm text-warning-900">
                                Cette facture est annulee. Aucun paiement supplementaire ne peut etre ajoute tant qu'elle reste annulee.
                            </div>
                        @elseif($soldeRestant <= 0)
                            <div class="mt-4 rounded-2xl border border-success-200 bg-success-50 px-4 py-4 text-sm text-success-900">
                                Le solde est deja nul. Aucun paiement supplementaire n'est requis.
                            </div>
                        @else
                            <form action="{{ route('factures.paiements.store', ['facture' => $facture, 'date' => request()->query('date')]) }}" method="POST" class="mt-4 space-y-4">
                                @csrf
                                <div class="form-field">
                                    <label for="montant" class="form-label">Montant du paiement</label>
                                    <input id="montant" name="montant" type="number" min="0" step="0.01" max="{{ $soldeRestant }}" value="{{ old('montant', $soldeRestant) }}" class="form-input @error('montant') error @enderror" aria-describedby="montant-help {{ $errors->has('montant') ? 'montant-error' : '' }}">
                                    <p id="montant-help" class="mt-2 text-xs text-slate-500">Solde restant : {{ number_format($soldeRestant, 2, ',', ' ') }} FCFA.</p>
                                    @error('montant')
                                        <p id="montant-error" class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                    <select id="mode_paiement" name="mode_paiement" class="form-select @error('mode_paiement') error @enderror" aria-describedby="{{ $errors->has('mode_paiement') ? 'mode_paiement-error' : '' }}">
                                        @foreach($paymentModes as $value => $label)
                                            @if($value !== \App\Models\Billing\PaiementFacture::MODE_REGULARISATION)
                                                <option value="{{ $value }}" {{ old('mode_paiement') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('mode_paiement')
                                        <p id="mode_paiement-error" class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="reference" class="form-label">Reference</label>
                                    <input id="reference" name="reference" type="text" value="{{ old('reference') }}" class="form-input @error('reference') error @enderror" aria-describedby="reference-help {{ $errors->has('reference') ? 'reference-error' : '' }}">
                                    <p id="reference-help" class="mt-2 text-xs text-slate-500">Numero de recu, reference mobile money, virement ou cheque.</p>
                                    @error('reference')
                                        <p id="reference-error" class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="date_paiement" class="form-label">Date du paiement</label>
                                    <input id="date_paiement" name="date_paiement" type="date" value="{{ old('date_paiement', now()->toDateString()) }}" class="form-input @error('date_paiement') error @enderror" aria-describedby="{{ $errors->has('date_paiement') ? 'date_paiement-error' : '' }}">
                                    @error('date_paiement')
                                        <p id="date_paiement-error" class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-field">
                                    <label for="commentaire" class="form-label">Commentaire</label>
                                    <textarea id="commentaire" name="commentaire" rows="3" class="form-input @error('commentaire') error @enderror" aria-describedby="{{ $errors->has('commentaire') ? 'commentaire-error' : '' }}">{{ old('commentaire') }}</textarea>
                                    @error('commentaire')
                                        <p id="commentaire-error" class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="btn-primary w-full justify-center">
                                    <i class="bi bi-cash-coin"></i>
                                    Enregistrer le paiement
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                <a href="{{ route('factures.pdf', ['facture' => $facture, 'date' => request()->query('date')]) }}" class="btn-secondary w-full justify-center">
                    <i class="bi bi-file-earmark-pdf"></i>
                    Ouvrir le PDF imprimable
                </a>

                <a href="{{ route('eleves.show', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary w-full justify-center">
                    <i class="bi bi-person"></i>
                    Retour au profil eleve
                </a>
            </div>
        </aside>
    </div>
</div>
@endsection
