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
<body class="min-h-screen bg-slate-100 text-gray-900 antialiased">
    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(26,86,219,0.12),_transparent_36%),radial-gradient(circle_at_bottom_right,_rgba(15,45,86,0.16),_transparent_32%),linear-gradient(180deg,_#f8fafc_0%,_#eef4ff_100%)]"></div>
        <div class="absolute inset-y-0 left-0 hidden w-1/2 bg-[linear-gradient(180deg,rgba(15,45,86,0.95)_0%,rgba(10,31,61,0.98)_100%)] xl:block"></div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-6 sm:px-6 lg:px-8">
            <div class="grid w-full overflow-hidden rounded-[30px] border border-white/70 bg-white/85 shadow-[0_30px_80px_rgba(15,45,86,0.14)] backdrop-blur xl:grid-cols-[1.08fr_0.92fr]">
                <aside class="hidden min-h-[760px] flex-col justify-between bg-[linear-gradient(180deg,#0f2d56_0%,#0a1f3d_100%)] p-10 text-white xl:flex">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-white shadow-lg shadow-black/10">
                                <i class="bi bi-mortarboard-fill text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-white/60">&Eacute;.T.P.</p>
                                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Gestion scolaire</h1>
                            </div>
                        </div>

                        <div class="mt-16 max-w-md space-y-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-100">Espace s&eacute;curis&eacute;</p>
                            <h2 class="text-4xl font-semibold leading-tight tracking-tight">Pilotez la vie scolaire avec une interface claire et rapide.</h2>
                            <p class="text-base leading-7 text-white/72">Acc&eacute;dez aux modules d'inscription, de notes, de classement et de suivi acad&eacute;mique depuis un environnement unifi&eacute;.</p>
                        </div>

                        <div class="mt-12 space-y-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-brand-100">
                                        <i class="bi bi-shield-check"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white">Connexion prot&eacute;g&eacute;e</p>
                                        <p class="mt-1 text-sm leading-6 text-white/65">Vos acc&egrave;s et op&eacute;rations sensibles restent cantonn&eacute;s &agrave; un espace authentifi&eacute;.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-brand-100">
                                        <i class="bi bi-graph-up-arrow"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white">Suivi acad&eacute;mique</p>
                                        <p class="mt-1 text-sm leading-6 text-white/65">Consultez les donn&eacute;es d'ann&eacute;e, les notes et les classements dans le m&ecirc;me flux.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                            <p class="text-white/55">Modules</p>
                            <p class="mt-2 text-2xl font-semibold">6+</p>
                            <p class="mt-1 text-white/65">gestion, notes, classements</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                            <p class="text-white/55">Contexte</p>
                            <p class="mt-2 text-2xl font-semibold">Annuel</p>
                            <p class="mt-1 text-white/65">pilotage par ann&eacute;e acad&eacute;mique</p>
                        </div>
                    </div>
                </aside>

                <main class="flex items-center justify-center p-6 sm:p-10 lg:p-12">
                    <div class="w-full max-w-md space-y-8">
                        <div class="text-center xl:hidden">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-navy-900 text-white shadow-lg shadow-navy-900/15">
                                <i class="bi bi-mortarboard-fill text-xl"></i>
                            </div>
                            <h1 class="mt-4 text-2xl font-semibold tracking-tight text-gray-900">&Eacute;.T.P.</h1>
                            <p class="mt-1 text-sm text-gray-500">Gestion scolaire</p>
                        </div>

                        {{ $slot }}

                        <div class="border-t border-gray-100 pt-4 text-center text-sm text-gray-500">
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