@props(['size' => 'normal', 'href' => null, 'disabled' => false])

@php
    $sizes = [
        'small' => 'text-sm px-2.5 py-1.5',
        'normal' => 'text-sm px-4 py-2',
        'large' => 'text-base px-6 py-3',
    ];

    // Hover class disesuaikan dengan elemen
    $hover = $href ? 'hover:bg-back-light/60 dark:hover:bg-back-dark-light/60' : 'enabled:hover:bg-back-light/60 enabled:dark:hover:bg-back-dark-light/60';

    $classes = 'bg-white dark:bg-back-dark-light border border-back-base-300 dark:border-back-dark-base-300 rounded-md text-center font-medium ' . $sizes[$size] . ' text-back-dark-dark shadow-xs ' . $hover . ' focus:outline-hidden focus:ring-1 focus:ring-back-primary dark:focus:ring-back-dark-primary focus:ring-offset-1 transition ease-in-out duration-150';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'disabled' => $disabled, 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
