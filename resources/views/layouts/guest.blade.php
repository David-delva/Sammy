<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestion scolaire') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-900 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-16 top-12 h-64 w-64 rounded-full bg-brand-300/20 blur-3xl animate-float-slow"></div>
        <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-emerald-200/20 blur-3xl animate-float-delayed"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-navy-900/10 blur-3xl animate-float-slow"></div>
    </div>

    <div class="relative isolate min-h-screen overflow-hidden px-4 py-6 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(26,86,219,0.12),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(15,45,86,0.14),_transparent_32%),linear-gradient(180deg,_#f8fbff_0%,_#eef4ff_100%)]"></div>

        <div class="relative mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-7xl items-center">
            <div class="grid w-full overflow-hidden rounded-[34px] border border-white/75 bg-white/82 shadow-[0_34px_90px_rgba(15,45,86,0.14)] backdrop-blur xl:grid-cols-[1.06fr_0.94fr]">
                <aside class="hidden min-h-[760px] flex-col justify-between overflow-hidden bg-[linear-gradient(180deg,#0f2d56_0%,#0a1f3d_100%)] p-10 text-white xl:flex">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 shadow-lg shadow-black/10">
                                <i class="bi bi-mortarboard-fill text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/55">E.T.P.</p>
                                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Gestion scolaire</h1>
                            </div>
                        </div>

                        <div class="mt-16 space-y-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-100">Espace securise</p>
                            <h2 class="text-4xl leading-tight tracking-tight" style="font-family: 'Source Serif 4', ui-serif, Georgia, serif;">Un acces moderne pour piloter classes, notes et parcours scolaires.</h2>
                            <p class="max-w-md text-base leading-7 text-white/72">Toutes les operations essentielles de l'etablissement restent reunies dans une interface fluide, mobile et plus vivante.</p>
                        </div>

                        <div class="mt-12 grid gap-4">
                            <div class="rounded-[26px] border border-white/10 bg-white/6 px-5 py-5 backdrop-blur-sm" data-tilt>
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-brand-100">
                                        <i class="bi bi-lightning-charge-fill"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white">Parcours rapides</p>
                                        <p class="mt-1 text-sm leading-6 text-white/65">Connexion, saisie de notes, exports PDF et consultation annuelle dans le meme flux.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-[26px] border border-white/10 bg-white/6 px-5 py-5 backdrop-blur-sm" data-tilt>
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-emerald-200">
                                        <i class="bi bi-phone"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white">Experience mobile</p>
                                        <p class="mt-1 text-sm leading-6 text-white/65">Le shell adaptatif facilite les formulaires, les tableaux et les actions depuis petit ecran.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-[24px] border border-white/10 bg-white/6 px-4 py-4">
                            <p class="text-white/55">Modules</p>
                            <p class="mt-2 text-2xl font-semibold">6+</p>
                            <p class="mt-1 text-white/65">gestion, notes, classement</p>
                        </div>
                        <div class="rounded-[24px] border border-white/10 bg-white/6 px-4 py-4">
                            <p class="text-white/55">Contexte</p>
                            <p class="mt-2 text-2xl font-semibold">Annuel</p>
                            <p class="mt-1 text-white/65">suivi academique unifie</p>
                        </div>
                    </div>
                </aside>

                <main class="flex items-center justify-center p-5 sm:p-8 lg:p-10 xl:p-12">
                    <div class="w-full max-w-lg space-y-6">
                        <div class="text-center xl:hidden" data-reveal>
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-navy-900 text-white shadow-lg shadow-navy-900/15">
                                <i class="bi bi-mortarboard-fill text-xl"></i>
                            </div>
                            <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">E.T.P.</h1>
                            <p class="mt-1 text-sm text-slate-500">Gestion scolaire</p>
                        </div>

                        <div class="auth-panel">
                            <div class="relative">
                                {{ $slot }}
                            </div>
                        </div>

                        <div class="rounded-full border border-white/70 bg-white/75 px-5 py-3 text-center text-sm text-slate-500 shadow-sm backdrop-blur">
                            <a href="{{ url('/') }}" class="font-medium text-brand-600 transition hover:text-brand-700">
                                <i class="bi bi-arrow-left mr-1"></i>Retour au site
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
