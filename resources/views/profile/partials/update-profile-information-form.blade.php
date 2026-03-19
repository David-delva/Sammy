<section>
    <header class="mb-3">
        <h5 class="mb-0">Informations du profil</h5>
        <p class="text-muted small mb-0">Mettez à jour les informations de votre compte et votre adresse e-mail.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 small text-muted">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn btn-link btn-sm">{{ __('Click here to re-send the verification email.') }}</button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-2 text-success small">{{ __('A new verification link has been sent to your email address.') }}</div>
                @endif
            @endif
        </div>

        <div class="d-flex justify-content-start align-items-center">
            <button class="btn btn-primary">Enregistrer</button>

            @if (session('status') === 'profile-updated')
                <div class="text-success small ms-3">Enregistré.</div>
            @endif
        </div>
    </form>
</section>
