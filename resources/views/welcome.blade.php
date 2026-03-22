<!DOCTYPE html>
<html lang="fr" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&Eacute;.T.P. - Gestion scolaire</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-gray-900 antialiased">
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(26,86,219,0.14),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(15,45,86,0.18),_transparent_28%),linear-gradient(180deg,_#f8fafc_0%,_#eef4ff_100%)]"></div>
        <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-[linear-gradient(180deg,rgba(15,45,86,0.96)_0%,rgba(15,45,86,0.82)_48%,rgba(15,45,86,0)_100%)]"></div>

        <header class="relative border-b border-white/10">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3 text-white">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 shadow-lg shadow-black/10">
                        <i class="bi bi-mortarboard-fill text-lg"></i>
                    </span>
                    <span>
                        <span class="block text-xs font-semibold uppercase tracking-[0.26em] text-white/65">&Eacute;.T.P.</span>
                        <span class="block text-base font-semibold tracking-tight">Gestion scolaire</span>
                    </span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-secondary bg-white/90 text-navy-900 hover:bg-white">
                            <i class="bi bi-grid"></i>
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary bg-white/90 text-navy-900 hover:bg-white">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary shadow-lg shadow-brand-600/20">
                                <i class="bi bi-person-plus"></i>
                                Cr&eacute;er un compte
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main>
            <section class="mx-auto max-w-7xl px-4 pb-20 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
                <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                    <div class="max-w-2xl text-white">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-100">Pilotage scolaire moderne</p>
                        <h1 class="mt-5 text-4xl font-semibold tracking-tight sm:text-5xl lg:text-6xl">Une base propre pour suivre les classes, les notes et les parcours &eacute;l&egrave;ves.</h1>
                        <p class="mt-6 max-w-xl text-base leading-8 text-white/72 sm:text-lg">Centralisez les inscriptions, les ann&eacute;es acad&eacute;miques, les moyennes et les classements dans une interface claire, rapide et adapt&eacute;e au quotidien de l'&eacute;tablissement.</p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
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
                                    <a href="{{ route('register') }}" class="btn-secondary justify-center border-white/20 bg-white/10 text-white hover:bg-white/15 sm:w-auto">
                                        <i class="bi bi-person-plus"></i>
                                        Cr&eacute;er un compte
                                    </a>
                                @endif
                            @endauth
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 backdrop-blur-sm">
                                <p class="text-xs uppercase tracking-[0.24em] text-white/50">Modules</p>
                                <p class="mt-2 text-2xl font-semibold">6+</p>
                                <p class="mt-1 text-sm text-white/65">gestion, notes, classement</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 backdrop-blur-sm">
                                <p class="text-xs uppercase tracking-[0.24em] text-white/50">Acc&egrave;s</p>
                                <p class="mt-2 text-2xl font-semibold">24/7</p>
                                <p class="mt-1 text-sm text-white/65">espace centralis&eacute;</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 backdrop-blur-sm">
                                <p class="text-xs uppercase tracking-[0.24em] text-white/50">Sorties</p>
                                <p class="mt-2 text-2xl font-semibold">PDF</p>
                                <p class="mt-1 text-sm text-white/65">bulletins et listes</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -left-6 top-8 hidden h-24 w-24 rounded-full bg-brand-500/20 blur-3xl lg:block"></div>
                        <div class="absolute -right-2 bottom-10 hidden h-32 w-32 rounded-full bg-cyan-400/20 blur-3xl lg:block"></div>

                        <div class="relative overflow-hidden rounded-[28px] border border-white/70 bg-white/90 p-6 shadow-[0_30px_70px_rgba(15,45,86,0.16)] backdrop-blur sm:p-7">
                            <div class="card overflow-hidden border-0 shadow-none">
                                <div class="card-header border-b border-gray-100 px-0 pb-4 pt-0">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Vue d'ensemble</p>
                                        <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Flux de gestion unifi&eacute;</h2>
                                    </div>
                                </div>
                                <div class="card-body space-y-4 px-0 pb-0 pt-5">
                                    <div class="flex items-start gap-4 rounded-2xl border border-gray-100 bg-slate-50 px-4 py-4">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                                            <i class="bi bi-people-fill"></i>
                                        </span>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900">Suivi des &eacute;l&egrave;ves</h3>
                                            <p class="mt-1 text-sm leading-6 text-gray-500">Inscription, historique et rattachement par classe avec lecture rapide du contexte acad&eacute;mique.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-4 rounded-2xl border border-gray-100 bg-slate-50 px-4 py-4">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                            <i class="bi bi-journal-check"></i>
                                        </span>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900">Notes et compositions</h3>
                                            <p class="mt-1 text-sm leading-6 text-gray-500">Saisie structur&eacute;e des &eacute;valuations avec coh&eacute;rence par mati&egrave;re, classe et ann&eacute;e scolaire.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-4 rounded-2xl border border-gray-100 bg-slate-50 px-4 py-4">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                            <i class="bi bi-trophy-fill"></i>
                                        </span>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900">Classements et bulletins</h3>
                                            <p class="mt-1 text-sm leading-6 text-gray-500">Calcul des moyennes, g&eacute;n&eacute;ration des rangs et export des documents de suivi.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <article class="card p-6">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <i class="bi bi-calendar-event-fill"></i>
                        </span>
                        <h2 class="mt-5 text-xl font-semibold tracking-tight text-gray-900">Ann&eacute;es acad&eacute;miques</h2>
                        <p class="mt-3 text-sm leading-7 text-gray-500">Pilotez les p&eacute;riodes scolaires, activez l'ann&eacute;e en cours et naviguez facilement dans l'historique.</p>
                    </article>

                    <article class="card p-6">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-100 text-navy-800">
                            <i class="bi bi-building-fill"></i>
                        </span>
                        <h2 class="mt-5 text-xl font-semibold tracking-tight text-gray-900">Classes et mati&egrave;res</h2>
                        <p class="mt-3 text-sm leading-7 text-gray-500">Organisez les classes, attribuez les mati&egrave;res et structurez les donn&eacute;es sans d&eacute;pendre de Bootstrap.</p>
                    </article>

                    <article class="card p-6">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </span>
                        <h2 class="mt-5 text-xl font-semibold tracking-tight text-gray-900">Sorties op&eacute;rationnelles</h2>
                        <p class="mt-3 text-sm leading-7 text-gray-500">Produisez des listes, tableaux et bulletins &agrave; partir d'une base d'interface d&eacute;sormais homog&egrave;ne.</p>
                    </article>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} &Eacute;.T.P. - Gestion scolaire.</p>
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