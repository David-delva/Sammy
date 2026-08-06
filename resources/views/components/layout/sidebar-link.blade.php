@props([
    'href' => '#',
    'icon' => 'bi-circle',
    'active' => false,
])

<a href="{{ $href }}" @class(['sidebar-link', 'active' => $active])>
    <i class="bi {{ $icon }} w-5 text-center text-base"></i>
    <span>{{ $slot }}</span>
</a>
