@props(['sectionKey' => '', 'sectionData' => []])

@switch($sectionKey)
    @case('all-categories-widget')
        <section class="text-dark dark:text-dark-light container px-2">
            <div class="mb-3 flex w-full items-center gap-4 align-middle">
                <h4 class="whitespace-nowrap text-3xl font-semibold">{{ $sectionData['config']['label'] }}</h4>
                <div class="to-secondary dark:to-dark-secondary mt-3 h-[4px] flex-grow bg-gradient-to-r from-transparent"></div>
                <x-dashboard.primary-button class="px-1! py-0.5!" href="/blog/{{ str_replace(':', '/', '') }}">More »</x-dashboard.primary-button>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
                @forelse ($sectionData['data'] as $item)
                    <a class="block transform rounded-xl shadow-lg transition-transform duration-300 ease-in-out hover:scale-105" href={{ route('article.category', $item->category) }}>
                        <div class="random-bg-card text-dark-base-300 flex h-32 w-full items-center justify-center rounded-xl">
                            <h4 class="px-2 text-center text-xl font-bold md:text-2xl">{{ $item->category }}</h4>
                        </div>
                    </a>
                @empty
                    <p class="my-2">No Posts Available</p>
                @endforelse
            </div>
        </section>
    @break

    @case('all-tags-widget')
        <section class="text-dark dark:text-dark-light container px-2">
            <div class="mb-3 flex w-full items-center gap-4 align-middle">
                <h4 class="whitespace-nowrap text-3xl font-semibold">{{ $sectionData['config']['label'] }}</h4>
                <div class="to-secondary dark:to-dark-secondary mt-3 h-[4px] flex-grow bg-gradient-to-r from-transparent"></div>
                <x-dashboard.primary-button class="px-1! py-0.5!" href="/blog/{{ str_replace(':', '/', '') }}">More »</x-dashboard.primary-button>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
                @forelse ($sectionData['data'] as $item)
                    <a class="block transform rounded-xl shadow-lg transition-transform duration-300 ease-in-out hover:scale-105" href={{ route('article.tag', $item->slug) }}>
                        <div class="random-bg-card text-dark-base-300 flex h-32 w-full items-center justify-center rounded-xl">
                            <h4 class="px-2 text-center text-xl font-bold md:text-2xl">{{ $item->tag_name }}</h4>
                        </div>
                    </a>
                @empty
                    <p class="my-2">No Posts Available</p>
                @endforelse
            </div>
        </section>
    @break

    @case('js-script')
        <div class="mb-1" id="{{ $sectionData['itemsKey'] }}-sidebar">
            {!! $sectionData['config']['total'] !!}
        </div>
    @break

    @default
        <section class="text-dark dark:text-dark-light container px-2">
            <div class="mb-3 flex w-full items-center gap-4 align-middle">
                <h4 class="whitespace-nowrap text-3xl font-semibold">{{ $sectionData['config']['label'] }}</h4>
                <div class="to-secondary dark:to-dark-secondary mt-3 h-[4px] flex-grow bg-gradient-to-r from-transparent"></div>
                <x-dashboard.primary-button class="px-1! py-0.5!" href="/blog/{{ str_replace(':', '/', $sectionData['itemsKey']) }}">More »</x-dashboard.primary-button>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($sectionData['data'] as $article)
                    <article {{ $attributes->merge(['class' => 'flex flex-col']) }}>
                        <img class="h-64 w-full rounded-lg object-cover object-center lg:h-52" src="{{ asset($article->cover) }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">

                        <div class="mt-2 flex flex-grow flex-col">
                            <span class="text-primary dark:text-dark-primary uppercase">
                                {{ $article->category->category ?? $article->category_id }}
                            </span>

                            <h2 class="text-dark dark:text-dark-light hover:text-muted dark:hover:text-dark-muted mt-1 text-xl font-semibold hover:underline">
                                <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <div class="mt-auto flex items-center justify-between pt-4">
                                <div>
                                    <a class="text-accent dark:text-dark-accent hover:text-info dark:hover:text-dark-info text-lg font-medium" href="{{ route('article.user', $article->user->username) }}">
                                        {{ $article?->user?->username }}
                                    </a>

                                    <p class="text-muted dark:text-dark-muted text-sm">
                                        {{ $article->published_at->format('F j, Y') }}
                                    </p>
                                </div>

                                <a class="text-primary dark:text-dark-primary hover:text-accent dark:hover:text-dark-accent inline-block underline" href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                    Read more
                                </a>
                            </div>
                        </div>
                    </article>

                @empty
                    <p class="my-2">No Posts Available</p>
                @endforelse
            </div>
        </section>
    @break

@endswitch
