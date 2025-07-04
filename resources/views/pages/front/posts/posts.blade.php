@section('title', $data['title'] ?? 'Blog')

@section('og_title', $data['og_title'] ?? ($data['title'] ?? $data['web_setting']['web_name']))

<x-app-front-layout>
    <!-- Sticky/Featured/Popular Blog Post -->
    @unless ((request()->has('search') && request()->get('search') != '') || ((request()->has('page') && request()->get('page') != 1) || request()->url() !== url('/blog')))
        @if ($sectionsContent['home_feature_section']['config']['is_visible'] == '1' ?? false)
            @unless ((request()->has('search') && request()->get('search') != '') || (request()->has('page') && request()->get('page') != 1))
                <section class="pb-0 pt-4">
                    <div class="mx-auto px-3 2xl:container sm:px-4 xl:px-2">
                        <!-- big grid 1 -->
                        <div class="flex flex-row flex-wrap">
                            <!--Start left cover-->
                            <div class="w-full max-w-full flex-shrink pb-1 lg:w-1/2 lg:pb-0 lg:pr-1">
                                @foreach ($sectionsContent['home_feature_section']['data']->take(1) as $article)
                                    <div class="hover-img relative h-full max-h-[25rem] overflow-hidden">
                                        <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                            <div class="bg-gradient-cover absolute left-0 top-0 h-full w-full"></div>
                                            <img class="mx-auto h-auto w-full max-w-full" src="{{ asset($article->cover) }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                                        </a>
                                        <div class="absolute bottom-0 w-full px-5 pb-5">
                                            <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                                <h2 class="text-light hover:text-info dark:hover:text-dark-info mb-3 line-clamp-2 text-3xl font-bold capitalize">{{ $article->title }}</h2>
                                            </a>
                                            <p class="text-base-100 line-clamp-3 sm:inline-block">{{ $article->excerpt }}</p>
                                            <div class="pt-2">
                                                <div class="text-base-100">
                                                    <div class="border-accent dark:border-dark-accent mr-2 inline-block h-3 border-l-2"></div>{{ $article->published_at->format('F j, Y') }}
                                                    <div class="border-accent dark:border-dark-accent ml-2 mr-2 inline-block h-3 border-l-2"></div>{{ $article->category->category ?? $article->category_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!--Start box news-->
                            <div class="w-full max-w-full flex-shrink lg:w-1/2">
                                <div class="flex flex-row flex-wrap">
                                    @foreach ($sectionsContent['home_feature_section']['data']->skip(1)->take(4) as $article)
                                        <article class="my-1 w-full max-w-full flex-shrink px-1 pb-1 sm:w-1/2">
                                            <div class="hover-img relative h-full max-h-48 overflow-hidden">
                                                <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                                    <div class="bg-gradient-cover absolute left-0 top-0 h-full w-full"></div>
                                                    <img class="mx-auto h-auto w-full max-w-full" src="{{ asset($article->cover) }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                                                </a>
                                                <div class="absolute bottom-0 w-full px-4 pb-4">
                                                    <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                                        <h2 class="text-light hover:text-info dark:hover:text-dark-info mb-1 line-clamp-3 text-lg font-bold capitalize leading-tight">{{ $article->title }}</h2>
                                                    </a>
                                                    <div class="pt-1">
                                                        <div class="text-base-100">
                                                            <div class="border-accent dark:border-dark-accent mr-2 inline-block h-3 border-l-2"></div>{{ $article->published_at->format('F j, Y') }}
                                                            <div class="border-accent dark:border-dark-accent ml-2 mr-2 inline-block h-3 border-l-2"></div>{{ $article->category->category ?? $article->category_id }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endunless
        @endif

        @if ($sectionsContent['ads_featured']['config']['is_visible'] == '1' ?? false)
            <div class="container mt-3">
                <x-home-section-layout :sectionKey="$sectionsContent['ads_featured']['itemsKey']" :sectionData="$sectionsContent['ads_featured']" />
            </div>
        @endif
    @endunless

    @if (!request()->is('blog') || request()->is('blog/*') || request()->query('search'))
        <x-breadcrumb class="container -mb-5 flex-auto px-6 pt-10 md:px-4" :items="request()->has('search') && request()->get('search') ? [['text' => 'Blog', 'link' => '/blog'], ['text' => 'Search', 'link' => '#'], ['text' => request()->get('search')]] : generate_breadcrumbs()" />
    @endif

    <!-- Recent Blog Post -->
    <section class="fluid text-dark dark:text-dark-light container px-6 pb-6 pt-8 md:px-4">
        <div class="mb-6 text-3xl font-semibold">
            <h2>
                {{ request()->query('search') ? 'Search Result' : (request()->routeIs('article.user') ? 'User Posts: ' . request()->segment(3) : 'Recent Post') }}
            </h2>
            <div class="to-secondary dark:to-dark-secondary -z-1 float-end h-[4px] w-[50%] -translate-y-4 bg-gradient-to-r from-transparent md:w-[84%]"></div>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($articles as $article)
                <x-card-post-desc :article="$article" />
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
