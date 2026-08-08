<!DOCTYPE html>
<html lang="fr" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INPTIC - Campus Connecté</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-white antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-20 top-10 h-80 w-80 rounded-full bg-cobalt-300/25 blur-3xl animate-float-slow"></div>
        <div class="absolute right-0 top-24 h-96 w-96 rounded-full bg-cobalt-100 blur-3xl animate-float-delayed opacity-70"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-success-200/20 blur-3xl animate-float-slow"></div>
    </div>

    <div class="relative isolate overflow-hidden">
        <div class="institution-public-backdrop -z-10"></div>

        <header class="sticky top-0 z-30 px-3 pt-3 sm:px-4 lg:px-6">
            <div class="mx-auto max-w-7xl rounded-[30px] border border-white/15 bg-white/10 shadow-[0_24px_60px_rgba(12,8,35,0.22)] backdrop-blur-2xl">
                <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 text-white">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cobalt-500 to-navy-500 text-white shadow-lg shadow-cobalt-500/25">
                            <i class="bi bi-mortarboard-fill text-lg"></i>
                        </span>
                        <span>
                            <span class="block text-xs font-semibold uppercase tracking-[0.28em] text-white/60">INPTIC</span>
                            <span class="block text-base font-semibold tracking-tight">Campus Connecté</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-2 xl:flex">
                        <a href="#packages" class="marketing-menu-link">Offres</a>
                        <a href="#services" class="marketing-menu-link">Services</a>
                        <a href="#help" class="marketing-menu-link">Aide</a>
                        <a href="#contact" class="marketing-menu-link">Contact</a>
                    </nav>

                    <div class="flex flex-wrap items-center gap-3">
                        <label class="marketing-search" aria-label="Rechercher">
                            <i class="bi bi-search"></i>
                            <input type="search" placeholder="Rechercher des classes, services ou offres" />
                        </label>

                        <div class="notification-chip">
                            <span class="notification-pulse"></span>
                            <span class="hidden sm:inline">En ligne</span>
                        </div>

                        @auth
                            <a href="{{ route('dashboard') }}" class="account-chip">
                                <span class="account-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <span>Mon compte</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="account-chip">
                                <span class="account-avatar">MC</span>
                                <span>Mon compte</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 pb-24 pt-8 sm:px-6 lg:px-8 lg:pt-10">
            <section class="page-hero" data-reveal>
                <div class="page-hero-grid">
                    <div>
                        <p class="page-kicker">Suite éducative connectée</p>
                        <h1 class="page-title">Restez connectés, à tout moment.</h1>
                        <p class="page-lead">
                            Une plateforme scolaire plus vive pour enseigner, planifier, communiquer et suivre les élèves.
                            Appels vidéo, discussions de groupe, calendriers et présence s'unifient dans un espace fluide et moderne.
                        </p>

                        <div class="hero-badges">
                            <span class="hero-badge"><i class="bi bi-camera-video-fill"></i>Appels vidéo</span>
                            <span class="hero-badge"><i class="bi bi-chat-dots-fill"></i>Discussions de groupe</span>
                            <span class="hero-badge"><i class="bi bi-calendar3"></i>Calendriers intelligents</span>
                            <span class="hero-badge"><i class="bi bi-clipboard-check-fill"></i>Registres de présence</span>
                        </div>

                        <div class="hero-actions">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary justify-center shadow-lg shadow-cobalt-600/20 sm:w-auto">
                                    Découvrir l'offre
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary justify-center shadow-lg shadow-cobalt-600/20 sm:w-auto">
                                    Découvrir l'offre
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-secondary justify-center sm:w-auto">
                                        <i class="bi bi-person-plus"></i>
                                        Créer un compte
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="hero-visual" data-reveal data-reveal-delay="120ms">
                        <div class="mascot-scene" data-tilt>
                            <div class="mascot-glow"></div>
                            <div class="floating-chip one"><i class="bi bi-camera-video-fill"></i>Salle en direct</div>
                            <div class="floating-chip two"><i class="bi bi-chat-left-text-fill"></i>12 discussions</div>
                            <div class="floating-chip three"><i class="bi bi-calendar2-week-fill"></i>Planning hebdomadaire</div>
                            <div class="floating-chip four"><i class="bi bi-check2-square"></i>Présence active</div>
                            <div class="mascot-platform"></div>
                            <div class="mascot-bot">
                                <div class="mascot-head">
                                    <div class="mascot-face">
                                        <span class="mascot-eye"></span>
                                        <span class="mascot-eye"></span>
                                    </div>
                                </div>
                                <div class="mascot-body">
                                    <div class="mascot-core"></div>
                                </div>
                                <span class="mascot-arm left"></span>
                                <span class="mascot-arm right"></span>
                                <span class="mascot-leg left"><span class="mascot-foot left"></span></span>
                                <span class="mascot-leg right"><span class="mascot-foot right"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="packages" class="mt-8 grid gap-5 lg:grid-cols-4">
                <article class="feature-tile p-5" data-tilt>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cobalt-100 text-cobalt-700"><i class="bi bi-camera-video-fill text-xl"></i></span>
                    <h2 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">Cours vidéo</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Lancez des cours, du mentorat et du support à distance grâce à des salles vidéo stables pour chaque classe.</p>
                </article>
                <article class="feature-tile p-5" data-tilt>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cobalt-100 text-cobalt-700"><i class="bi bi-chat-left-dots-fill text-xl"></i></span>
                    <h2 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">Discussions de groupe</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Gardez enseignants, élèves et équipe administrative alignés avec des conversations en direct bien structurées.</p>
                </article>
                <article class="feature-tile p-5" data-tilt>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-success-100 text-success-700"><i class="bi bi-calendar-event-fill text-xl"></i></span>
                    <h2 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">Calendriers partagés</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Planifiez cours, examens, rappels et événements scolaires dans une même couche visuelle.</p>
                </article>
                <article class="feature-tile p-5" data-tilt>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-success-50 text-success-700"><i class="bi bi-clipboard-check-fill text-xl"></i></span>
                    <h2 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">Suivi de présence</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Suivez la présence des élèves avec des registres rapides et des statuts prêts pour l'administration.</p>
                </article>
            </section>

            <section id="services" class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <div class="workspace-card p-6 sm:p-7" data-reveal>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="page-kicker">Tableau enseignant</p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">Le glassmorphism au service d'un pilotage clair.</h2>
                        </div>
                        <span class="badge-green">Mode enseignant</span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="teacher-metric">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Salles connectées</p>
                            <p class="mt-3 text-4xl font-semibold tracking-tight text-slate-900">08</p>
                            <div class="teacher-progress"><div class="teacher-progress-bar" style="width: 78%"></div></div>
                        </div>
                        <div class="teacher-metric">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Synchronisation des présences</p>
                            <p class="mt-3 text-4xl font-semibold tracking-tight text-slate-900">94%</p>
                            <div class="teacher-progress"><div class="teacher-progress-bar" style="width: 94%"></div></div>
                        </div>
                        <div class="mini-card">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Outils actifs</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="tool-pill"><i class="bi bi-camera-video"></i>Appels</span>
                                <span class="tool-pill"><i class="bi bi-chat-square"></i>Messagerie</span>
                                <span class="tool-pill"><i class="bi bi-calendar3"></i>Calendrier</span>
                            </div>
                        </div>
                        <div class="mini-card">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Statut</p>
                            <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-success-50 px-3 py-2 text-sm font-semibold text-success-700">
                                <span class="notification-pulse"></span>
                                Tous les services sont prêts
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="hero-panel" data-tilt>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Flux enseignant</p>
                        <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Un rythme plus fluide pour les cours en direct, la planification et le suivi des élèves.</h3>
                        <p class="mt-3 text-sm leading-7 text-white/72">Passez de la communication à l'emploi du temps puis au suivi des présences sans changer de contexte.</p>
                    </div>

                    <div class="grid gap-3">
                        <div class="rounded-[24px] border border-white/10 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.22em] text-white/45">Assistance</p>
                            <p class="mt-2 text-lg font-semibold text-white">Aide en temps réel et contact de l'établissement</p>
                        </div>
                        <div class="rounded-[24px] border border-white/10 bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.22em] text-white/45">Accès</p>
                            <p class="mt-2 text-lg font-semibold text-white">Compte, recherche et alertes réunis dans un seul en-tête</p>
                        </div>
                    </div>
                </aside>
            </section>

            <section id="help" class="mt-8 grid gap-5 lg:grid-cols-3">
                <article class="feature-tile p-5">
                    <span class="badge-blue">Centre d'aide</span>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Prise en main rapide</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Faites monter les administrateurs et enseignants en compétence avec une interface familière et une navigation claire.</p>
                </article>
                <article class="feature-tile p-5">
                    <span class="badge-yellow">Services</span>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Support opérationnel</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Utilisez la facturation, les classements, les affectations et l'historique des élèves dans une même plateforme connectée.</p>
                </article>
                <article id="contact" class="feature-tile p-5">
                    <span class="badge-green">Contact</span>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Prêt à activer votre campus ?</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-500">Ouvrez l'espace de travail, consultez l'offre en direct et gérez la journée scolaire depuis une interface moderne.</p>
                    <div class="mt-5">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary">Ouvrir le tableau de bord <i class="bi bi-arrow-right"></i></a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">Ouvrir le tableau de bord <i class="bi bi-arrow-right"></i></a>
                        @endauth
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
