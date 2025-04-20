@props(['items' => [], 'showSegment' => 3, 'class' => ''])

@php
    $totalItems = count($items);
    $startCount = $showSegment - 1;
@endphp

<nav class="{{ $class }}" aria-label="breadcrumb">
    <ol class="flex items-center whitespace-nowrap">
        @if ($totalItems > $showSegment)
            <!-- Item Awal -->
            @foreach (array_slice($items, 0, $startCount) as $item)
                <li class="dark:text-back-dark-light text-back-dark-dark flex items-center text-sm">
                    @if (!empty($item['link']))
                        <a class="hover:underline" href="{{ $item['link'] }}">
                            {{ Str::limit($item['text'] ?? $item['link'], 50) }}
                        </a>
                    @else
                        {{ Str::limit($item['text'] ?? '', 50) }}
                    @endif
                    <i class="ri-arrow-right-s-line px-1.5"></i>
                </li>
            @endforeach

            <!-- Separator -->
            <li class="dark:text-back-dark-light text-back-dark-dark flex items-center text-sm">
                ...
                <i class="ri-arrow-right-s-line px-1.5"></i>
            </li>

            <!-- Item Terakhir -->
            @php $last = end($items); @endphp
            <li class="dark:text-back-dark-light text-back-dark-dark truncate text-sm font-semibold" aria-current="page">
                @if (!empty($last['link']))
                    <a class="hover:underline" href="{{ $last['link'] }}">
                        {{ Str::limit($last['text'] ?? $last['link'], 50) }}
                    </a>
                @else
                    {{ Str::limit($last['text'] ?? '', 50) }}
                @endif
            </li>
        @else
            <!-- Semua Item -->
            @foreach ($items as $item)
                <li class="dark:text-back-dark-light text-back-dark-dark flex items-center text-sm">
                    @if (!empty($item['link']))
                        <a class="hover:underline" href="{{ $item['link'] }}">
                            {{ Str::limit($item['text'] ?? $item['link'], 50) }}
                        </a>
                    @else
                        {{ Str::limit($item['text'] ?? '', 50) }}
                    @endif
                    @unless ($loop->last)
                        <i class="ri-arrow-right-s-line px-1.5"></i>
                    @endunless
                </li>
            @endforeach
        @endif
    </ol>
</nav>
