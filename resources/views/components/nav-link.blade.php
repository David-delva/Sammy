@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-cobalt-500 text-sm font-medium leading-5 text-cobalt-700 focus:outline-none focus:border-cobalt-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 hover:text-cobalt-700 hover:border-cobalt-200 focus:outline-none focus:text-cobalt-700 focus:border-cobalt-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
