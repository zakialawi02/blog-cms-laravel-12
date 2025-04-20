@section('title', $data['title'] ?? 'Blog')

@section('og_title', $data['og_title'] ?? ($data['title'] ?? $data['web_setting']['web_name']))

<x-app-front-layout>
    <!-- Sticky/Featured/Popular Blog Post -->
    @unless ((request()->has('search') && request()->get('search') != '') || (request()->has('page') && request()->get('page') != 1))
        <section class="pb-0 pt-4">
            <div class="mx-auto px-3 2xl:container sm:px-4 xl:px-2">
                <!-- big grid 1 -->
                <div class="flex flex-row flex-wrap">
                    <!--Start left cover-->
                    <div class="w-full max-w-full flex-shrink pb-1 lg:w-1/2 lg:pb-0 lg:pr-1">
                        @foreach ($featured->take(1) as $article)
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
                            @foreach ($featured->skip(1)->take(4) as $article)
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


    <div class="container grid grid-cols-1 gap-2 md:gap-4 lg:grid-cols-4">
        <div class="lg:col-span-3">
            <!-- Recent Blog Post -->
            <section class="text-dark dark:text-dark-light container mb-6 px-2">
                <div class="mb-3 flex w-full items-center gap-4 align-middle">
                    <h3 class="whitespace-nowrap text-3xl font-semibold">{{ request()->query('search') ? 'Search Result' : (request()->routeIs('article.user') ? 'User Posts: ' . request()->segment(3) : 'Recent Post') }}</h3>
                    <div class="to-secondary dark:to-dark-secondary h-[4px] flex-grow bg-gradient-to-r from-transparent"></div>
                    <x-dashboard.primary-button class="px-1! py-0.5!" href="{{ route('article.index') }}">More »</x-dashboard.primary-button>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($articles as $article)
                        <article>
                            <img class="h-64 w-full rounded-lg object-cover object-center lg:h-52" src="{{ asset($article->cover) }}" alt="{{ $article->title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">

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
                {{-- <div class="my-8 mt-20">
                    {{ $articles->links() }}
                </div> --}}
            </section>

            <section class="text-dark dark:text-dark-light container px-2">
                <div class="mb-3 flex w-full items-center gap-4 align-middle">
                    <h3 class="whitespace-nowrap text-3xl font-semibold">Section 2</h3>
                    <div class="to-secondary dark:to-dark-secondary h-[4px] flex-grow bg-gradient-to-r from-transparent"></div>
                    <x-dashboard.primary-button class="px-1! py-0.5!" href="#">More »</x-dashboard.primary-button>
                </div>

                <!-- Section Here -->

            </section>
        </div>

        <div class="text-dark dark:text-dark-light mt-2 pt-4">
            <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="popular-posts">
                <div class="text-center text-xl font-bold">
                    <h3>Popular Posts</h3>
                </div>

                <div class="mx-auto p-2">
                    @forelse ($popularPosts as $popular)
                        <article>
                            <div class="flex items-center gap-2 p-1">
                                <a class="mr-2 block shrink-0" href="{{ route('article.show', ['year' => $popular->published_at->format('Y'), 'slug' => $popular->slug]) }}">
                                    <img class="size-14 rounded-3xl object-cover" src="{{ $popular->cover }}" alt="post image" />
                                </a>

                                <div>
                                    <h3 class="line-clamp-2 font-medium sm:text-lg">
                                        <a class="hover:text-primary dark:hover:text-dark-primary block" href="{{ route('article.show', ['year' => $popular->published_at->format('Y'), 'slug' => $popular->slug]) }}">{{ $popular->title }}</a>
                                    </h3>

                                    <div class="mt-2 sm:flex sm:items-center sm:gap-2">
                                        <p class="hidden sm:block sm:text-xs">Posted by <a class="hover:text-primary dark:hover:text-dark-primary font-medium" href="{{ route('article.user', $popular->user->username) }}">{{ $popular->user->username }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="font-regular my-2 text-center">No popular posts</p>
                    @endforelse
                </div>
            </div>
            <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="categories">
                <div class="text-center text-xl font-bold">
                    <h3>Tags</h3>
                </div>
                <div class="mx-auto p-2">
                    <ul class="flex flex-wrap">
                        @forelse ($tags as $tag)
                            <a class="border-secondary dark:border-dark-secondary hover:border-primary hover:text-primary dark:hover:text-dark-primary dark:hover:border-dark-primary mb-2 mr-1 rounded-2xl border-[1px] px-[0.40rem] py-[0.15rem] transition-all duration-300" href="{{ route('article.tag', $tag->slug) }}"># {{ $tag->tag_name }}</a>
                        @empty
                            <p class="font-regular my-2 text-center">No Tag Available</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- You Missed/Random Posts Section -->
    <section class="fluid container px-6 py-5 md:px-4">
        <h2 class="text-dark dark:text-dark-light mb-5 text-2xl font-bold">You Missed</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
            @foreach ($randomPosts->take(4) as $post)
                <div class="group relative overflow-hidden rounded-lg">
                    <img class="h-48 w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $post->cover }}" alt="Post Cover" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                    <div class="absolute inset-0 flex flex-col justify-end bg-black/50 p-4 dark:bg-black/20">
                        <div class="mb-2 flex space-x-2">
                            <span class="text-accent dark:text-dark-accent bg-light dark:bg-dark-light rounded-full px-2 py-1 text-xs font-semibold">{{ $post->category->category }}</span>
                        </div>
                        <h3 class="text-light hover:text-accent dark:hover:text-dark-accent all line-clamp-3 text-lg font-semibold transition duration-300">
                            <a href={{ route('article.show', ['year' => $post->published_at->format('Y'), 'slug' => $post->slug]) }}>{{ $post->title }}</a>
                        </h3>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @push('javascript')
    @endpush
</x-app-front-layout>
