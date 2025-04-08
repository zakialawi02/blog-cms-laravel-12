@props(['items' => [], 'showSegment' => 3, 'class' => ''])

@php
    $totalItems = count($items);
    $startCount = $showSegment - 1; // Menyisakan satu untuk item terakhir
@endphp

<nav class="{{ $class }}" aria-label="breadcrumb">
    <ol class="flex items-center whitespace-nowrap">
        @if ($totalItems > $showSegment)
            <!-- Tampilkan sejumlah item awal -->
            @foreach (array_slice($items, 0, $startCount) as $item)
                <li class="dark:text-back-dark-light text-back-dark-dark flex items-center text-sm">
                    {{ Str::limit($item['text'], 25) }}
                    <i class="ri-arrow-right-s-line px-1.5"></i>
                </li>
            @endforeach

            <!-- Tampilkan separator ... -->
            <li class="dark:text-back-dark-light text-back-dark-dark flex items-center text-sm">
                ...
                <i class="ri-arrow-right-s-line px-1.5"></i>
            </li>

            <!-- Tampilkan item terakhir -->
            <li class="dark:text-back-dark-light text-back-dark-dark truncate text-sm font-semibold" aria-current="page">
                {{ Str::limit(end($items)['text'], 25) }}
            </li>
        @else
            <!-- Jika item kurang dari atau sama dengan showSegment, tampilkan semuanya -->
            @foreach ($items as $item)
                <li class="dark:text-back-dark-light text-back-dark-dark flex items-center text-sm">
                    {{ Str::limit($item['text'], 25) }}
                    @unless ($loop->last)
                        <i class="ri-arrow-right-s-line px-1.5"></i>
                    @endunless
                </li>
            @endforeach
        @endif
    </ol>
</nav>
