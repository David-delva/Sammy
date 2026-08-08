@extends('layouts.app')

@section('title', 'Profil')

@section('breadcrumb')
    Compte / Parametres
@endsection

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Compte</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Mon profil</h1>
                <p class="mt-2 text-sm text-gray-500">Gerez vos informations personnelles, vos acces et la securite de votre compte.</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="card">
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </section>

                <section class="card">
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </section>

                <section class="card border-danger-100">
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </section>
            </div>

            <aside class="space-y-4">
                <section class="card">
                    <div class="card-body space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Rappel</p>
                            <h2 class="mt-2 text-lg font-semibold tracking-tight text-gray-900">Securite du compte</h2>
                        </div>
                        <div class="space-y-3 text-sm leading-6 text-gray-500">
                            <p>Gardez une adresse e-mail valide pour recevoir les notifications de verification et de reinitialisation.</p>
                            <p>Un mot de passe long et unique reste la meilleure protection pour vos acces d'administration.</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
