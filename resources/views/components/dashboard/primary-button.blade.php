@props(['size' => 'normal', 'href' => null, 'disabled' => false])

@php
    $sizes = [
        'small' => 'text-sm px-2.5 py-1.5',
        'normal' => 'text-sm px-4 py-2',
        'large' => 'text-base px-6 py-3',
    ];

    // Hover class tergantung elemen
    $hover = $href ? 'hover:bg-back-primary/70 dark:hover:bg-back-dark-primary/70' : 'enabled:hover:bg-back-primary/70 enabled:dark:hover:bg-back-dark-primary/70';

    $classes = 'bg-back-primary dark:bg-back-dark-primary border border-back-primary dark:border-back-dark-primary ' . $hover . ' focus:ring-back-primary/80 dark:focus:ring-back-dark-primary/80 rounded-md ' . $sizes[$size] . ' text-center font-medium text-back-light shadow-xs focus:outline-none focus:ring-1 focus:ring-offset-1 transition ease-in-out duration-150';
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
