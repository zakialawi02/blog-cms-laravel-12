@props(['size' => 'normal', 'href' => null])

@php
    $sizes = [
        'small' => 'text-sm px-2.5 py-1.5',
        'normal' => 'text-sm px-4 py-2',
        'large' => 'text-base px-6 py-3',
    ];

    $classes = 'bg-back-secondary dark:bg-back-dark-secondary border border-back-secondary dark:border-back-dark-secondary rounded-md text-center font-medium ' . $sizes[$size] . ' text-back-light shadow-xs enabled:hover:bg-back-secondary/70 enabled:dark:hover:bg-back-dark-secondary/70 focus:outline-hidden focus:ring-1 focus:ring-back-secondary dark:focus:ring-back-dark-secondary focus:ring-offset-1  disabled:opacity-40 transition ease-in-out duration-150';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'disabled' => false, 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
