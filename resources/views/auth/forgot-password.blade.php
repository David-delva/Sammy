<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Reinitialisation</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Mot de passe oublie</h1>
        <p class="mt-2 text-sm text-gray-500">Indiquez votre adresse e-mail et nous vous enverrons un lien de reinitialisation securise.</p>
    </div>

    <div class="alert-info mb-6">
        <i class="bi bi-envelope"></i>
        <span>Le lien de reinitialisation sera envoye a l'adresse associee a votre compte.</span>
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
            Envoyer le lien de reinitialisation
        </button>
    </form>

    <div class="border-t border-gray-100 pt-5 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="font-medium text-gray-600 transition hover:text-gray-900 hover:underline">
            <i class="bi bi-arrow-left mr-1"></i>Retour a la connexion
        </a>
    </div>
</x-guest-layout>
