@props(['size' => 'normal', 'href' => null, 'disabled' => false])

@php
    $sizes = [
        'small' => 'text-sm px-2.5 py-1.5',
        'normal' => 'text-sm px-4 py-2',
        'large' => 'text-base px-6 py-3',
    ];

    // Hover class: berbeda untuk <a> dan <button>
    $hover = $href ? 'hover:bg-back-error/70 dark:hover:bg-back-error/70' : 'enabled:hover:bg-back-error/70 enabled:dark:hover:bg-back-error/70';

    $classes = 'bg-back-error dark:bg-back-error border border-transparent rounded-md text-back-light font-medium shadow-xs ' . $hover . ' focus:outline-none focus:ring-1 focus:ring-back-error dark:focus:ring-back-error focus:ring-offset-1 disabled:opacity-40 disabled:cursor-not-allowed ' . $sizes[$size];
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
