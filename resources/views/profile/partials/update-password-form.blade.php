<section>
    <header class="mb-3">
        <h5 class="mb-0">Modifier le mot de passe</h5>
        <p class="text-muted small mb-0">Assure-toi d'utiliser un mot de passe long et sécurisé.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Mot de passe actuel</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
            @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Nouveau mot de passe</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
            @error('password_confirmation')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">Enregistrer</button>
            @if (session('status') === 'password-updated')
                <div class="text-success small">Mot de passe mis à jour.</div>
            @endif
        </div>
    </form>
</section>
