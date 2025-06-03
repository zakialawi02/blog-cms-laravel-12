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
