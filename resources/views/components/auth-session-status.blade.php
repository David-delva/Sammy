@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert-success']) }}>
        <i class="bi bi-check-circle-fill text-emerald-600"></i>
        <span>{{ $status }}</span>
    </div>
@endif