@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-cobalt-500 text-start text-base font-medium text-cobalt-700 bg-cobalt-50 focus:outline-none focus:text-cobalt-800 focus:bg-cobalt-100 focus:border-cobalt-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-cobalt-700 hover:bg-cobalt-50 focus:outline-none focus:text-cobalt-700 focus:bg-cobalt-50 focus:border-cobalt-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
