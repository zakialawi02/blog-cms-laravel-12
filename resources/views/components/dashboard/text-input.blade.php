@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'focus:ring-back-primary dark:focus:ring-back-dark-primary focus:border-back-primary dark:focus:border-back-dark-primary block w-full rounded-lg border border-gray-300 bg-gray-50 px-2.5 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400']) }}>
