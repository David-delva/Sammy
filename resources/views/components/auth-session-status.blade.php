@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert-success']) }}>
        <i class="bi bi-check-circle-fill text-success-600"></i>
        <span>{{ $status }}</span>
    </div>
@endif
