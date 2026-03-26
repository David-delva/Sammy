@extends('layouts.app')

@section('title', 'Historique scolaire')
@section('breadcrumb', 'Scolarite / Eleves / Historique')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Historique</p>
                <h2 class="page-title">Le parcours scolaire de {{ $eleve->nom }} {{ $eleve->prenom }} reste lisible annee apres annee.</h2>
                <p class="page-lead">Visualisez les inscriptions, la classe frequentee et la moyenne generale de chaque cycle dans une frise de cartes responsive.</p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-upc"></i>{{ $eleve->matricule }}</span>
                    <span class="hero-badge"><i class="bi bi-clock-history"></i>{{ $historique->count() }} annee(s) repertoriee(s)</span>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('eleves.show', $eleve) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-arrow-left"></i>
                        Retour au profil
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Timeline</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Chaque annee garde sa classe, sa moyenne et son acces de consultation.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Le parcours historique est plus simple a lire et a revisiter grace a des cartes bien distinctes.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Parcours</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $historique->count() }}</p>
                        <p class="mt-1 text-sm text-white/65">etape(s) conservee(s)</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Acces</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Direct</p>
                        <p class="mt-1 text-sm text-white/65">vers l'annee choisie</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @if($historique->isEmpty())
        <div class="alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <span>Aucune inscription historique trouvee pour cet eleve.</span>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach($historique as $item)
                <article class="card overflow-hidden" data-tilt>
                    <div class="card-header">
                        <div>
                            <h4>{{ $item['annee']->libelle }}</h4>
                            <p class="text-xs text-slate-500">Parcours annuel</p>
                        </div>
                        @if($item['annee']->active)
                            <span class="badge-green">Annee active</span>
                        @endif
                    </div>
                    <div class="card-body space-y-4">
                        <div class="surface-soft flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                                <i class="bi bi-building text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Classe frequentee</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $item['classe']->nom_classe }}</p>
                            </div>
                        </div>

                        <div class="surface-soft flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $item['moyenne_generale'] >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                <i class="bi bi-graph-up-arrow text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moyenne generale</p>
                                <p class="mt-1 text-sm font-semibold {{ $item['moyenne_generale'] >= 10 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $item['moyenne_generale'] ? number_format($item['moyenne_generale'], 2) . ' / 20' : '--' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 px-5 py-4">
                        <a href="{{ route('eleves.show', $eleve) }}?date={{ explode('-', $item['annee']->libelle)[0] }}-09-01" class="btn-secondary btn-sm">
                            <i class="bi bi-arrow-right"></i>
                            Consulter cette annee
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
