@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1.5 block font-medium text-sm text-back-dark dark:text-back-light']) }}>
    {{ $value ?? $slot }}
</label>
