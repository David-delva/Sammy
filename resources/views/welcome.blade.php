<!DOCTYPE html>
<html lang="fr" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E.T.P. - Gestion scolaire</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-900 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-20 top-16 h-72 w-72 rounded-full bg-brand-300/25 blur-3xl animate-float-slow"></div>
        <div class="absolute right-0 top-32 h-96 w-96 rounded-full bg-emerald-200/20 blur-3xl animate-float-delayed"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-navy-900/10 blur-3xl animate-float-slow"></div>
    </div>

    <div class="relative isolate overflow-hidden">
        <div class="institution-public-backdrop -z-10"></div>

        <header class="border-b border-white/60 bg-white/72 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3 text-slate-900">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/20">
                        <i class="bi bi-mortarboard-fill text-lg"></i>
                    </span>
                    <span>
                        <span class="block text-xs font-semibold uppercase tracking-[0.26em] text-slate-400">E.T.P.</span>
                        <span class="block text-base font-semibold tracking-tight">Gestion scolaire</span>
                    </span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-secondary">
                            <i class="bi bi-grid"></i>
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary shadow-lg shadow-brand-600/20">
                                <i class="bi bi-person-plus"></i>
                                Creer un compte
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main>
            <section class="mx-auto max-w-7xl px-4 pb-16 pt-10 sm:px-6 lg:px-8 lg:pb-24 lg:pt-16">
                <div class="page-hero" data-reveal>
                    <div class="page-hero-grid">
                        <div>
                            <p class="page-kicker">Pilotage scolaire moderne</p>
                            <h1 class="page-title">Une plateforme plus fluide pour gerer classes, notes, rangs et parcours eleves.</h1>
                            <p class="page-lead">Centralisez les inscriptions, les annees academiques, la saisie des evaluations et les exports PDF dans une interface vive, responsive et agreable a utiliser.</p>

                            <div class="hero-badges">
                                <span class="hero-badge"><i class="bi bi-phone"></i>Responsive sur mobile et desktop</span>
                                <span class="hero-badge"><i class="bi bi-file-earmark-pdf"></i>Bulletins et listes PDF</span>
                                <span class="hero-badge"><i class="bi bi-lightning-charge"></i>Actions rapides integrees</span>
                            </div>

                            <div class="hero-actions">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                                        <i class="bi bi-arrow-right-circle"></i>
                                        Ouvrir le tableau de bord
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        Se connecter
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn-secondary justify-center sm:w-auto">
                                            <i class="bi bi-person-plus"></i>
                                            Creer un compte
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="hero-panel" data-tilt>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Flux unifie</p>
                                <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white">L'etablissement garde une lecture claire de l'annee en cours.</h2>
                                <p class="mt-3 text-sm leading-7 text-white/70">Les modules principaux restent relies entre eux pour limiter les aller-retours et accelerer les operations quotidiennes.</p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.22em] text-white/45">Modules</p>
                                    <p class="mt-2 text-2xl font-semibold text-white">6+</p>
                                    <p class="mt-1 text-sm text-white/65">gestion, notes, classement</p>
                                </div>
                                <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.22em] text-white/45">Exports</p>
                                    <p class="mt-2 text-2xl font-semibold text-white">PDF</p>
                                    <p class="mt-1 text-sm text-white/65">listes, bulletins, rangs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <div class="grid gap-5 lg:grid-cols-3">
                    <article class="action-card" data-tilt>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <i class="bi bi-calendar-event-fill"></i>
                        </span>
                        <div class="mt-5">
                            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Annees academiques</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-500">Activez un contexte scolaire, consultez l'historique et gardez les donnees bien separees par cycle.</p>
                        </div>
                    </article>

                    <article class="action-card" data-tilt>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-100 text-navy-800">
                            <i class="bi bi-building-fill"></i>
                        </span>
                        <div class="mt-5">
                            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Classes et matieres</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-500">Structurez les classes, assignez les matieres et gardez un catalogue propre pour les saisies futures.</p>
                        </div>
                    </article>

                    <article class="action-card" data-tilt>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <i class="bi bi-journal-check"></i>
                        </span>
                        <div class="mt-5">
                            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Notes et classements</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-500">Saisissez rapidement les notes, calculez les moyennes et produisez les sorties pedagogiques utiles.</p>
                        </div>
                    </article>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/60 bg-white/72 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} E.T.P. - Gestion scolaire.</p>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="font-medium text-brand-600 transition hover:text-brand-700">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="font-medium text-brand-600 transition hover:text-brand-700">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-medium text-brand-600 transition hover:text-brand-700">Inscription</a>
                        @endif
                    @endauth
                </div>
            </div>
        </footer>
    </div>
</body>
</html>