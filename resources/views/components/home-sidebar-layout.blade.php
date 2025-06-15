@props(['sectionKey' => '', 'sectionData' => []])

@switch($sectionKey)
    @case('popular-posts')
        <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="{{ $sectionData['itemsKey'] }}-sidebar">
            <div class="text-center text-xl font-bold">
                <h5>{{ $sectionData['config']['label'] }}</h5>
            </div>

            <div class="mx-auto p-2">
                @forelse ($sectionData['data'] as $popular)
                    <article>
                        <div class="flex items-center gap-1">
                            <a class="mr-2 block shrink-0" href="{{ route('article.show', ['year' => $popular->published_at->format('Y'), 'slug' => $popular->slug]) }}">
                                <img class="size-14 rounded-3xl object-cover" src="{{ $popular->cover }}" alt="post image" />
                            </a>

                            <div>
                                <h5 class="line-clamp-2 font-medium sm:text-lg">
                                    <a class="hover:text-primary dark:hover:text-dark-primary block" href="{{ route('article.show', ['year' => $popular->published_at->format('Y'), 'slug' => $popular->slug]) }}">{{ $popular->title }}</a>
                                </h5>

                                <div class="mt-2 sm:flex sm:items-center sm:gap-2">
                                    <p class="hidden sm:block sm:text-xs">Posted by <a class="hover:text-primary dark:hover:text-dark-primary font-medium" href="{{ route('article.user', $popular->user->username) }}">{{ $popular->user->username }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="font-regular my-2 text-center">No Data</p>
                @endforelse
            </div>
        </div>
    @break

    @case('all-categories-widget')
        <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="{{ $sectionData['itemsKey'] }}-sidebar">
            <div class="text-center text-xl font-bold">
                <h5>{{ $sectionData['config']['label'] }}</h5>
            </div>
            <div class="mx-auto p-2">
                <ul class="flex flex-col gap-2 p-2">
                    @forelse ($sectionData['data'] as $category)
                        <li><a class="hover:text-primary dark:hover:text-dark-primary font-bold" href="{{ route('article.category', $category->slug) }}"><i class="ri-skip-right-line text-info dark:text-dark-info mr-2 text-xl"></i>{{ $category->category }}</a></li>
                    @empty
                        <p class="font-regular my-2 text-center">No Data</p>
                    @endforelse
                </ul>
            </div>
        </div>
    @break

    @case('all-tags-widget')
        <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="{{ $sectionData['itemsKey'] }}-sidebar">
            <div class="text-center text-xl font-bold">
                <h5>{{ $sectionData['config']['label'] }}</h5>
            </div>
            <div class="mx-auto p-2">
                <ul class="flex flex-wrap">
                    @forelse ($sectionData['data'] as $tag)
                        <li class="border-secondary dark:border-dark-secondary hover:border-primary hover:text-primary dark:hover:text-dark-primary dark:hover:border-dark-primary mb-2 mr-1 rounded-2xl border-[1px] px-[0.40rem] py-[0.15rem] transition-all duration-300">
                            <a href="{{ route('article.tag', $tag->slug) }}"># {{ $tag->tag_name }}</a>
                        </li>
                    @empty
                        <p class="font-regular my-2 text-center">No Data</p>
                    @endforelse
                </ul>
            </div>
        </div>
    @break

    @case('js-script')
        <div class="mb-1 max-w-[300px]" id="{{ $sectionData['itemsKey'] }}-sidebar">
            {!! $sectionData['config']['total'] !!}
        </div>
    @break

    @default
        <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="{{ $sectionData['itemsKey'] }}-sidebar">
            <div class="text-center text-xl font-bold">
                <h3>{{ $sectionData['config']['label'] }}</h3>
            </div>

            <div class="mx-auto p-2">
                @forelse ($sectionData['data'] as $popular)
                    <article>
                        <div class="flex items-center gap-1">
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
                    <p class="font-regular my-2 text-center">No Data</p>
                @endforelse
            </div>
        </div>
    @break

@endswitch
