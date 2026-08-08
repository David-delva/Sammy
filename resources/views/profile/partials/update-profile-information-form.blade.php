<section class="space-y-6">
    <header class="space-y-2">
        <div class="flex items-center gap-2 text-cobalt-600">
            <i class="bi bi-person-circle text-base"></i>
            <p class="text-xs font-semibold uppercase tracking-[0.24em]">Informations du profil</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-gray-900">Vos coordonnees</h2>
            <p class="mt-1 text-sm text-gray-500">Mettez a jour le nom affiche et l'adresse e-mail associee a votre compte.</p>
        </div>
    </header>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 md:grid-cols-2">
            <div class="form-field">
                <label for="name" class="form-label">Nom complet</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="form-input @error('name') error @enderror">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="form-input @error('email') error @enderror">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="space-y-3 rounded-2xl border border-warning-200 bg-warning-50 px-4 py-4">
                <div class="flex items-start gap-3 text-sm text-warning-900">
                    <i class="bi bi-envelope-exclamation mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Adresse e-mail non verifiee</p>
                        <p class="mt-1 leading-6 text-warning-800">Votre compte utilise une adresse non confirmee. Renvoyez un e-mail de verification pour finaliser la securisation de l'acces.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button form="send-verification" class="btn-secondary btn-sm" type="submit">
                        <i class="bi bi-send"></i>
                        Renvoyer l'e-mail de verification
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <span class="badge-green">Lien de verification envoye</span>
                    @endif
                </div>
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button class="btn-primary" type="submit">
                <i class="bi bi-check2-circle"></i>
                Enregistrer les modifications
            </button>

            @if (session('status') === 'profile-updated')
                <span class="badge-green">Profil enregistre</span>
            @endif
        </div>
    </form>
</section>
