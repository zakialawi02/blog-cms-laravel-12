@section('title', $data['title'] ?? 'Blog')

@section('og_title', $data['og_title'] ?? ($data['title'] ?? $data['web_setting']['web_name']))

<x-app-front-layout>
    <!-- Blog Post -->
    <section class="fluid text-dark dark:text-dark-light container px-6 py-10 md:px-4">
        <div class="mb-6 text-3xl font-semibold">
            <h2>
                Archive: {{ request()->segment(4) ? date('F', strtotime(request()->segment(3) . '-' . request()->segment(4) . '-01')) . ' ' . request()->segment(3) : request()->segment(3) }}
            </h2>
            <div class="to-secondary dark:to-dark-secondary -z-1 float-end h-[4px] w-[50%] -translate-y-4 bg-gradient-to-r from-transparent md:w-[84%]"></div>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($articles as $article)
                <article>
                    <img class="h-64 w-full rounded-lg object-cover object-center lg:h-80" src="{{ asset($article->cover) }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">

                    <div class="mt-2">
                        <span class="text-primary dark:text-dark-primary uppercase">
                            {{ $article->category->category ?? $article->category_id }}
                        </span>

                        <h2 class="text-dark dark:text-dark-light hover:text-muted dark:hover:text-dark-muted mt-1 text-xl font-semibold hover:underline">
                            <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                {{ $article->title }}
                            </a>
                        </h2>

                        <p class="text-muted dark:text-dark-muted mt-2 line-clamp-3">
                            {{ $article->excerpt }}
                        </p>

                        <div class="mt-4 flex items-center justify-between">
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
                <p class="my-2">No Article Posts Available</p>
            @endforelse
        </div>
        <div class="my-8 mt-20">
            {{ $articles->links() }}
        </div>
    </section>

    @if ($sectionsContent['ads_bottom_1']['config']['is_visible'] == '1' ?? false)
        <div class="container flex items-center justify-center">
            <x-home-section-layout :sectionKey="$sectionsContent['ads_bottom_1']['itemsKey']" :sectionData="$sectionsContent['ads_bottom_1']" />
        </div>
    @endif

    <!-- You Missed/Random Posts Section -->
    @if ($sectionsContent['home_bottom_section_1']['config']['is_visible'] == '1' ?? false)
        <section class="fluid container px-6 py-5 md:px-4">
            <h2 class="text-dark dark:text-dark-light mb-5 text-2xl font-bold">{{ $sectionsContent['home_bottom_section_1']['config']['label'] }}</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
                @forelse ($sectionsContent['home_bottom_section_1']['data']->take(4) as $post)
                    <div class="group relative overflow-hidden rounded-lg">
                        <img class="h-48 w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $post->cover }}" alt="Post Cover" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                        <div class="absolute inset-0 flex flex-col justify-end bg-black/50 p-4 dark:bg-black/20">
                            <div class="mb-2 flex space-x-2">
                                <span class="text-accent dark:text-dark-accent bg-light dark:bg-dark-light rounded-full px-2 py-1 text-xs font-semibold">{{ $post->category->category ?? 'Uncategorized' }}</span>
                            </div>
                            <h3 class="text-light hover:text-accent dark:hover:text-dark-accent all line-clamp-3 text-lg font-semibold transition duration-300">
                                <a href={{ route('article.show', ['year' => $post->published_at->format('Y'), 'slug' => $post->slug]) }}>{{ $post->title }}</a>
                            </h3>
                        </div>
                    </div>
                @empty
                    <p class="my-2 text-center">No Posts Available</p>
                @endforelse
            </div>
        </section>
    @endif

    @if ($sectionsContent['ads_bottom_2']['config']['is_visible'] == '1' ?? false)
        <div class="container flex items-center justify-center">
            <x-home-section-layout :sectionKey="$sectionsContent['ads_bottom_2']['itemsKey']" :sectionData="$sectionsContent['ads_bottom_2']" />
        </div>
    @endif

    @push('javascript')
    @endpush
</x-app-front-layout>
