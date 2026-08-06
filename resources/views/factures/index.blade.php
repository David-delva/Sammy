@extends('layouts.app')

@section('title', 'Factures')
@section('breadcrumb', 'Scolarite / Factures')

@section('content')
@php
    $statusLabels = \App\Models\Facture::statutOptions();
@endphp

<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Facturation</p>
                <h2 class="page-title">Gerer les factures d'inscription par annee academique.</h2>
                <p class="page-lead">Retrouvez les factures emises, suivez leur statut, le montant deja regle et le solde restant en quelques clics.</p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-receipt"></i>{{ $factures->total() }} facture(s)</span>
                    @if($annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                    @if($selectedEleve)
                        <span class="hero-badge"><i class="bi bi-person-badge"></i>{{ $selectedEleve->nom }} {{ $selectedEleve->prenom }}</span>
                    @endif
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('factures.create', ['date' => request()->query('date'), 'eleve' => request()->query('eleve')]) }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            <i class="bi bi-plus-circle"></i>
                            Nouvelle facture
                        </a>
                    @endif
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-people"></i>
                        Voir les eleves
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Pilotage</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Chaque inscription peut produire une facture tracable, payable et imprimable.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Le module centralise le montant, les paiements partiels, le solde et le document PDF sans dupliquer les donnees eleves.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Impression</p>
                        <p class="mt-2 text-2xl font-semibold text-white">PDF</p>
                        <p class="mt-1 text-sm text-white/65">pret a archiver</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Statut</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Suivi</p>
                        <p class="mt-1 text-sm text-white/65">emise, partielle, payee, annulee</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h4>Registre des factures</h4>
                <p class="text-xs text-slate-500">Filtrez par statut, eleve ou numero pour retrouver rapidement un document.</p>
            </div>
            <form method="GET" action="{{ route('factures.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @if(request()->has('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif
                @if(request()->filled('eleve'))
                    <input type="hidden" name="eleve" value="{{ request()->query('eleve') }}">
                @endif
                <input type="search" name="search" value="{{ $search }}" placeholder="Numero, eleve, matricule..." class="form-input min-w-[220px]">
                <select name="statut" class="form-select min-w-[180px]" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ $statut === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary justify-center">
                    <i class="bi bi-search"></i>
                    Filtrer
                </button>
                @if($search !== '' || $statut || request()->filled('eleve'))
                    <a href="{{ route('factures.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                        <i class="bi bi-x-lg"></i>
                        Reinitialiser
                    </a>
                @endif
            </form>
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Numero</th>
                        <th>Eleve</th>
                        <th>Classe</th>
                        <th>Montants</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($factures as $facture)
                        @php
                            $eleve = $facture->inscription->eleve;
                            $classe = $facture->inscription->classe;
                            $montantPaye = round((float) ($facture->paiements_sum_montant ?? $facture->montant_paye), 2);
                            $soldeRestant = max(0, round((float) $facture->montant - $montantPaye, 2));
                        @endphp
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $facture->numero }}</div>
                                <div class="mt-1 text-xs text-slate-400">{{ $facture->libelle }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $eleve->nom }} {{ $eleve->prenom }}</div>
                                <div class="mt-1 text-xs text-slate-400">{{ $eleve->matricule }}</div>
                            </td>
                            <td>
                                <span class="badge-blue">{{ $classe->nom_classe }}</span>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900">{{ number_format((float) $facture->montant, 2, ',', ' ') }} FCFA</div>
                                <div class="mt-1 text-xs text-slate-500">Paye : {{ number_format($montantPaye, 2, ',', ' ') }} FCFA</div>
                                <div class="mt-1 text-xs text-slate-500">Solde : {{ number_format($soldeRestant, 2, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                <span class="badge-gray">{{ $statusLabels[$facture->statut] ?? ucfirst($facture->statut) }}</span>
                            </td>
                            <td>
                                <div class="text-sm text-slate-900">{{ $facture->date_emission?->format('d/m/Y') }}</div>
                                <div class="mt-1 text-xs text-slate-400">Echeance {{ $facture->date_echeance?->format('d/m/Y') ?? '--' }}</div>
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('factures.show', ['facture' => $facture, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-eye"></i>
                                        Detail
                                    </a>
                                    <a href="{{ route('factures.pdf', ['facture' => $facture, 'date' => request()->query('date')]) }}" class="btn-primary btn-sm">
                                        <i class="bi bi-printer"></i>
                                        Imprimer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i class="bi bi-receipt text-2xl"></i>
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune facture trouvee</p>
                                    <p class="mt-2 text-sm text-slate-500">Creez une facture d'inscription pour commencer a suivre les paiements et imprimer les justificatifs.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mobile-list sm:hidden">
            @forelse($factures as $facture)
                @php
                    $eleve = $facture->inscription->eleve;
                    $classe = $facture->inscription->classe;
                    $montantPaye = round((float) ($facture->paiements_sum_montant ?? $facture->montant_paye), 2);
                    $soldeRestant = max(0, round((float) $facture->montant - $montantPaye, 2));
                @endphp
                <article class="mobile-list-item">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $facture->numero }}</p>
                                <span class="badge-gray">{{ $statusLabels[$facture->statut] ?? ucfirst($facture->statut) }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $eleve->nom }} {{ $eleve->prenom }} · {{ $classe->nom_classe }}</p>
                            <div class="mt-3 grid gap-2 text-xs text-slate-500">
                                <span class="badge-blue">Total : {{ number_format((float) $facture->montant, 2, ',', ' ') }} FCFA</span>
                                <span class="badge-gray">Paye : {{ number_format($montantPaye, 2, ',', ' ') }} FCFA</span>
                                <span class="badge-gray">Solde : {{ number_format($soldeRestant, 2, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4">
                        <a href="{{ route('factures.show', ['facture' => $facture, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                            <i class="bi bi-eye"></i>
                            Detail
                        </a>
                        <a href="{{ route('factures.pdf', ['facture' => $facture, 'date' => request()->query('date')]) }}" class="btn-primary justify-center">
                            <i class="bi bi-printer"></i>
                            Imprimer
                        </a>
                    </div>
                </article>
            @empty
                <div class="empty-state m-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="bi bi-receipt text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune facture trouvee</p>
                    <p class="mt-2 text-sm text-slate-500">Ajoutez une facture pour commencer a suivre la facturation des inscriptions.</p>
                </div>
            @endforelse
        </div>

        @if($factures->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $factures->links() }}
            </div>
        @endif
    </section>
</div>
@endsection