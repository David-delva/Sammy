<section class="space-y-6">
    <header class="space-y-2">
        <div class="flex items-center gap-2 text-brand-600">
            <i class="bi bi-shield-lock text-base"></i>
            <p class="text-xs font-semibold uppercase tracking-[0.24em]">Mot de passe</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-gray-900">Renforcer la s&eacute;curit&eacute;</h2>
            <p class="mt-1 text-sm text-gray-500">Choisissez un mot de passe long et unique pour prot&eacute;ger les acc&egrave;s &agrave; l'application.</p>
        </div>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div class="grid gap-5 md:grid-cols-2">
            <div class="form-field md:col-span-2">
                <label for="update_password_current_password" class="form-label">Mot de passe actuel</label>
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="form-input @error('current_password', 'updatePassword') error @enderror">
                @error('current_password', 'updatePassword')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="update_password_password" class="form-label">Nouveau mot de passe</label>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="form-input @error('password', 'updatePassword') error @enderror">
                @error('password', 'updatePassword')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="update_password_password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="form-input @error('password_confirmation', 'updatePassword') error @enderror">
                @error('password_confirmation', 'updatePassword')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button class="btn-primary" type="submit">
                <i class="bi bi-shield-check"></i>
                Mettre &agrave; jour le mot de passe
            </button>

            @if (session('status') === 'password-updated')
                <span class="badge-green">Mot de passe mis &agrave; jour</span>
            @endif
        </div>
    </form>
</section>