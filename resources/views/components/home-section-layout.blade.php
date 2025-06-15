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
                    <x-card-post-simple :article="$article" />
                @empty
                    <p class="my-2">No Posts Available</p>
                @endforelse
            </div>
        </section>
    @break

@endswitch
