@props(['size' => 'normal', 'href' => null, 'disabled' => false])

@php
    $sizes = [
        'small' => 'text-sm px-2.5 py-1.5',
        'normal' => 'text-sm px-4 py-2',
        'large' => 'text-base px-6 py-3',
    ];

    // Hover class dibedakan berdasarkan elemen
    $hover = $href ? 'hover:bg-back-neutral/70 dark:hover:bg-back-dark-neutral/70' : 'enabled:hover:bg-back-neutral/70 enabled:dark:hover:bg-back-dark-neutral/70';

    $classes = 'bg-back-neutral dark:bg-back-dark-neutral border border-back-base-300 dark:border-back-dark-base-300 rounded-md text-center font-medium ' . $sizes[$size] . ' text-back-light shadow-xs ' . $hover . ' focus:outline-hidden focus:ring-1 focus:ring-back-base-300 dark:focus:ring-back-dark-base-300 focus:ring-offset-1 disabled:opacity-40 transition ease-in-out duration-150';
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
