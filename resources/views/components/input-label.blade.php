@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-back-dark dark:text-back-light']) }}>
    {{ $value ?? $slot }}
</label>
