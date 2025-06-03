@section('title', $article->title ?? ($data['title'] ?? $data['web_setting']['web_name']))
@section('meta_description', $article->excerpt ?? ($data['description'] ?? ''))
@section('meta_keywords', $article->keywords ?? ($data['keywords'] ?? ''))


<x-app-front-layout>
    <div class="container mt-6">
        <x-breadcrumb :showSegment="5" :items="[['text' => 'Home', 'link' => '/'], ['text' => 'Blog', 'link' => route('article.index')], ['text' => request()->segment(2), 'link' => route('article.year', ['year' => request()->segment(2)])], ['text' => $article->category->category ?? 'Uncategorized', 'link' => route('article.category', ['slug' => $article->category->slug ?? 'uncategorized'])], ['link' => route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug])], ['text' => $article->title]]" />

        <div class="text-dark dark:text-dark-light flex flex-auto flex-grow flex-col flex-wrap gap-4 md:flex-row" id="main">
            <div class="w-full md:w-[60%] md:flex-grow" id="post">
                <div class="" id="main-content">
                    <div class="mb-3 py-1" id="post-header">
                        <h1 class="mb-2 text-3xl font-bold md:text-4xl">{{ $article->title }}</h1>
                        <div class="inline-flex items-center">
                            <a class="after:text-secondary hover:text-primary dark:after:text-dark-secondary dark:hover:text-dark-primary inline-flex items-center gap-1 after:relative after:top-[-3px] after:mx-2 after:px-1 after:font-black after:content-['.']" href="{{ route('article.user', $article->user->username) }}" target="_blank">
                                <img class="w-6" src="{{ $article->user->profile_photo_path }}" alt="author {{ $article->user->username }}">{{ $article->user->username }}
                            </a>
                            <a class="hover:text-primary dark:hover:text-dark-primary" href="{{ route('article.month', ['year' => $article->published_at->format('Y'), 'month' => $article->published_at->format('m')]) }}" target="_blank">{{ $article->published_at->format('d M Y') }}</a>
                        </div>
                    </div>
                </div>
                <div class="mb-3" id="feature-image">
                    <img class="max-h-[26rem] w-full rounded-lg object-cover object-center" src="{{ $article->cover }}" alt="Feature Image" loading="lazy">
                </div>
                <div class="ck ck-content text-lg" id="post-content">

                    {!! $article->content !!}

                </div>

                <div class="border-dark dark:border-dark-muted my-2 border-b-2 border-opacity-40 py-1"></div>

                <div class="post-bottom">
                    <div class="text-secondary dark:text-dark-secondary flex items-center justify-between">
                        <div class="">
                            <!-- tags -->
                            @foreach ($article->tags->take(4) as $tag)
                                <a class="border-secondary dark:border-dark-secondary hover:border-primary hover:text-primary dark:hover:text-dark-primary dark:hover:border-dark-primary mb-2 mr-1 rounded-2xl border-[1px] px-[0.40rem] py-[0.15rem] transition-all duration-300" href="{{ route('article.tag', $tag->tag_name) }}">#{{ $tag->tag_name }}</a>
                            @endforeach
                        </div>
                        <div class="text-dark dark:text-dark-light text-2xl">
                            <p class="text-sm">Share:</p>
                            <!-- AddToAny BEGIN -->
                            <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                                <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                <a class="a2a_button_facebook"></a>
                                <a class="a2a_button_email"></a>
                                <a class="a2a_button_whatsapp"></a>
                                <a class="a2a_button_linkedin"></a>
                                <a class="a2a_button_telegram"></a>
                                <a class="a2a_button_x"></a>
                            </div>
                            <script async src="https://static.addtoany.com/menu/page.js"></script>
                            <!-- AddToAny END -->
                        </div>
                    </div>
                </div>

                <div id="author">

                </div>

                <div class="border-dark dark:border-dark-muted my-2 border-b-2 border-opacity-40 py-1"></div>

                <section id="comments">
                    <div class="mb-3">
                        <h3 class="text-3xl font-bold">Comments</h3>
                    </div>

                    <form class="mb-6" id="comment-form" action="#" method="POST">
                        <div class="mb-4 rounded-lg rounded-t-lg border border-gray-200 bg-white px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
                            <label class="sr-only" for="comment">Your comment</label>
                            <textarea class="w-full border-0 px-0 text-sm text-gray-900 focus:outline-none focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400" id="comment_input" name="comment" cols="10" rows="5" placeholder="Write a comment..." required></textarea>
                            <p><span class="text-error text-sm" id="comment-error"></span></p>
                        </div>
                        @if (Auth::check())
                            <x-dashboard.primary-button id="btn-submit-comment" type="submit">Post comment</x-dashboard.primary-button>
                        @else
                            <x-dashboard.primary-button href="{{ route('login') }}">Login to comment</x-dashboard.primary-button>
                        @endif
                    </form>

                    <x-comment-section :showCommentsSection="false" />
                </section>
            </div>

            <div class="text-dark dark:text-dark-light mt-10 w-full md:mt-8 md:w-[30%]" id="sidebar">
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
                        <h3>Categories</h3>
                    </div>
                    <div class="mx-auto p-2">
                        <ul class="flex flex-col gap-2 p-2">
                            @forelse ($categories as $category)
                                <li><a class="hover:text-primary dark:hover:text-dark-primary font-bold" href="{{ route('article.category', $category->slug) }}"><i class="ri-skip-right-line text-info dark:text-dark-info mr-2 text-xl"></i>{{ $category->category }}</a></li>
                            @empty
                                <p class="font-regular my-2 text-center">No Category Available</p>
                            @endforelse
                        </ul>
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
    </div>


    @push('css')
        @vite('resources/css/ckeditor.css')
        <link href="{{ asset('assets/css/prism.css') }}" rel="stylesheet">
    @endpush

    @push('javascript')
        <script>
            const query = new URLSearchParams(window.location.search);
            const source = query.get('source');
            const commentId = query.get('commentId');
            setTimeout(() => {
                loadComments().done(() => {
                    if (source == "comments" && commentId) {
                        scrollToElement(`#${commentId}`, 50, 200);
                    }
                })
            }, 500);

            $(document).on("submit", "#comment-form", function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "{{ route('comment.store', $article->slug) }}",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        $("#comment-error").html("");
                        $("#btn-submit-comment").prop("disabled", true).html("Sending...");
                    },
                    success: function(response) {
                        MyZkToast.success(response?.message ?? "Comment posted successfully");
                        loadComments();
                        $("#comment-form")[0].reset();
                    },
                    error: function(response) {
                        $("#comment-error").html(response.responseJSON.message);
                    },
                    complete: function() {
                        $("#btn-submit-comment").prop("disabled", false).html("Post Comment");
                    }
                });
            });

            function loadComments() {
                return $.ajax({
                    type: "GET",
                    url: "{{ url()->full() }}",
                    data: {
                        ajax: true
                    },
                    dataType: "html",
                    beforeSend: function() {
                        $("#content-comment-container").empty();
                        $("#content-comment-container").prepend("Loading & fetching comments...");
                    },
                    success: function(response) {
                        $("#content-comment-container").empty();
                        $("#content-comment-container").html(response);
                    },
                    error: function(response) {
                        MyZkToast.error(response.responseJSON.message);
                        $("#content-comment-container").empty();
                        $("#content-comment-container").prepend("Load Comments (error)");
                    }
                });
            }

            function scrollToElement(selector, offset = 0, delay = 0) {
                setTimeout(function() {
                    window.scrollTo({
                        top: document.querySelector(selector).offsetTop - offset,
                        behavior: 'smooth'
                    });
                }, delay);
            }
        </script>
    @endpush
</x-app-front-layout>
