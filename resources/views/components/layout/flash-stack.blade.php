@props([
    'isCurrentAcademicYear' => true,
    'currentAcademicYear' => null,
    'canManageAcademicData' => false,
])

@if(! $isCurrentAcademicYear && $currentAcademicYear)
    <div class="alert-warning animate-fadein" role="alert">
        <i class="bi bi-exclamation-diamond-fill mt-0.5 flex-shrink-0"></i>
        <div class="flex-1">
            <strong>Mode consultation :</strong> vous visualisez l'année {{ $currentAcademicYear->libelle }}.
            @if($canManageAcademicData)
                Les modifications restent autorisées pour votre compte sur cette année.
            @else
                La modification est bloquée pour le secrétariat tant qu'un administrateur n'accorde pas d'autorisation explicite.
            @endif
        </div>
        <button onclick="window.location.href='{{ route('academic-year.reset') }}'" class="btn-secondary btn-sm self-center whitespace-nowrap" style="cursor: pointer;">
            Retour au présent
        </button>
    </div>
@endif

@if(session('success'))
    <div class="alert-success animate-fadein" role="alert">
        <i class="bi bi-check-circle-fill flex-shrink-0 text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('warning'))
    <div class="alert-warning animate-fadein" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
        <span>{{ session('warning') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-error animate-fadein" role="alert">
        <i class="bi bi-x-circle-fill flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if($errors->any() && ! request()->routeIs('*.create') && ! request()->routeIs('*.edit'))
    <div class="alert-error animate-fadein" role="alert">
        <i class="bi bi-x-circle-fill mt-0.5 flex-shrink-0"></i>
        <ul class="list-none space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
