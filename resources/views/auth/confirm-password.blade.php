<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">S&eacute;curit&eacute;</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Confirmer votre mot de passe</h1>
        <p class="mt-2 text-sm text-gray-500">Cette zone est prot&eacute;g&eacute;e. Saisissez votre mot de passe avant de continuer.</p>
    </div>

    <div class="alert-info mb-6">
        <i class="bi bi-shield-lock"></i>
        <span>Cette &eacute;tape confirme que vous &ecirc;tes bien &agrave; l'origine de l'action demand&eacute;e.</span>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div class="form-field">
            <label for="password" class="form-label">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="form-input @error('password') error @enderror">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            <i class="bi bi-check-circle"></i>
            Confirmer
        </button>
    </form>
</x-guest-layout>