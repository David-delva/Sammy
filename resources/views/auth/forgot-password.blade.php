<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">R&eacute;initialisation</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Mot de passe oubli&eacute;</h1>
        <p class="mt-2 text-sm text-gray-500">Indiquez votre adresse e-mail et nous vous enverrons un lien de r&eacute;initialisation s&eacute;curis&eacute;.</p>
    </div>

    <div class="alert-info mb-6">
        <i class="bi bi-envelope"></i>
        <span>Le lien de r&eacute;initialisation sera envoy&eacute; &agrave; l'adresse associ&eacute;e &agrave; votre compte.</span>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div class="form-field">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nom@exemple.com" class="form-input @error('email') error @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            <i class="bi bi-envelope-open"></i>
            Envoyer le lien de r&eacute;initialisation
        </button>
    </form>

    <div class="border-t border-gray-100 pt-5 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="font-medium text-gray-600 transition hover:text-gray-900 hover:underline">
            <i class="bi bi-arrow-left mr-1"></i>Retour &agrave; la connexion
        </a>
    </div>
</x-guest-layout>