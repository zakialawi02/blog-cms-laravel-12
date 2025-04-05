@php
    $logoPath = 'assets/app_logo/' . ($data['web_setting']['app_logo'] ?? 'app_logo.png');
    if (!file_exists(public_path($logoPath))) {
        $logoPath = 'assets/app_logo/app_logo.png';
    }
@endphp

<img src="{{ asset($logoPath) }}" alt="Logo Application" {{ $attributes->merge(['class' => 'h-auto max-w-24']) }}>
