@php
    $featureCards = [
        [
            'icon' => 'bi-camera-video-fill',
            'title' => 'Classes en direct',
            'description' => 'Les sessions vidéo et la communication de groupe restent disponibles dans le même espace de compte.',
        ],
        [
            'icon' => 'bi-calendar2-week-fill',
            'title' => 'Planification quotidienne',
            'description' => 'Les calendriers, la présence et les rappels sont prêts dès votre connexion.',
        ],
    ];

    $statusCards = [
        ['label' => 'Statut', 'value' => 'En ligne', 'hint' => 'toujours connecté'],
        ['label' => 'Mode', 'value' => 'Sécurisé', 'hint' => 'accès enseignant et admin'],
    ];
@endphp

<aside class="institution-public-aside hidden min-h-[760px] flex-col justify-between overflow-hidden p-10 text-white xl:flex">
    <div>
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 shadow-lg shadow-black/10">
                <i class="bi bi-mortarboard-fill text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/55">E.T.P.</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Campus Connecté</h1>
            </div>
        </div>

        <div class="mt-16 space-y-5">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/60">Accès enseignant</p>
            <h2 class="text-4xl leading-tight tracking-tight" style="font-family: 'Fraunces', ui-serif, Georgia, serif;">Un accès sécurisé pour les appels, les discussions, les calendriers et la présence.</h2>
            <p class="max-w-md text-base leading-7 text-white/72">Connectez-vous à la plateforme, retrouvez votre rythme de classe et avancez dans la journée avec une interface plus claire.</p>
        </div>

        <div class="mt-12 grid gap-4">
            @foreach($featureCards as $card)
                <div class="rounded-[26px] border border-white/10 bg-white/8 px-5 py-5 backdrop-blur-sm" data-tilt>
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-white">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $card['title'] }}</p>
                            <p class="mt-1 text-sm leading-6 text-white/65">{{ $card['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm">
        @foreach($statusCards as $card)
            <div class="rounded-[24px] border border-white/10 bg-white/6 px-4 py-4">
                <p class="text-white/55">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold">{{ $card['value'] }}</p>
                <p class="mt-1 text-white/65">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>
</aside>
