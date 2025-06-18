@section('title', $article->title ?? ($data['title'] ?? $data['web_setting']['web_name']))
@section('meta_description', $article->excerpt ?? ($data['description'] ?? ''))
@section('meta_keywords', $article->keywords ?? ($data['keywords'] ?? ''))
@section('og_title', $article->title . ' | ' . $data['web_setting']['web_name'] ?? ($data['title'] ?? $data['web_setting']['web_name']))
@section('og_description', $article->excerpt ?? ($data['og_description'] ?? ''))
@section('og_image', $article->cover ?? ($data['og_image'] ?? ''))


<x-app-front-layout>
    <div class="container mt-6">
        <x-breadcrumb :showSegment="5" :items="[['text' => 'Home', 'link' => '/'], ['text' => 'Blog', 'link' => route('article.index')], ['text' => request()->segment(2), 'link' => route('article.year', ['year' => request()->segment(2)])], ['text' => $article->category->category ?? 'Uncategorized', 'link' => route('article.category', ['slug' => $article->category->slug ?? 'uncategorized'])], ['link' => route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug])], ['text' => $article->title]]" />

        <div class="text-dark dark:text-dark-light flex flex-auto flex-grow flex-col flex-wrap gap-4 md:flex-row" id="main">
            <div class="w-full md:w-[60%] md:flex-grow" id="post">
                <div class="" id="main-content">
                    <div class="mb-3 py-1" id="post-header">
                        <h1 class="mb-2 text-3xl font-bold md:text-4xl">{{ $article->title }}</h1>
                        <div class="inline-flex items-center">
                            <a class="after:text-secondary hover:text-primary dark:after:text-dark-secondary dark:hover:text-dark-primary inline-flex items-center gap-1 after:relative after:top-[-3px] after:mx-2 after:px-1 after:font-black after:content-['.']" href="{{ route('article.user', $article->user->username) }}" target="_blank" rel="noopener noreferrer">
                                <img class="w-6" src="{{ $article->user->profile_photo_path }}" alt="author {{ $article->user->username }}">{{ $article->user->username }}
                            </a>
                            <a class="hover:text-primary dark:hover:text-dark-primary" href="{{ route('article.month', ['year' => $article->published_at->format('Y'), 'month' => $article->published_at->format('m')]) }}" target="_blank" rel="noopener noreferrer">{{ $article->published_at->format('d M Y') }}</a>
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

                @if ($sectionsContent['home_section_5']['config']['is_visible'] == '1' ?? false)
                    <x-home-section-layout :sectionKey="$sectionsContent['home_section_5']['itemsKey']" :sectionData="$sectionsContent['home_section_5']" />
                @endif

                <section id="comments">
                    <div class="mb-3">
                        <h4 class="text-3xl font-bold">Comments</h4>
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

                @if ($sectionsContent['ads_bottom_1']['config']['is_visible'] == '1' ?? false)
                    <x-home-section-layout :sectionKey="$sectionsContent['ads_bottom_1']['itemsKey']" :sectionData="$sectionsContent['ads_bottom_1']" />
                @endif
            </div>

            <div class="text-dark dark:text-dark-light mt-10 w-full md:mt-8 md:w-[30%]" id="sidebar">
                @if ($sectionsContent['home_sidebar_1']['config']['is_visible'] == '1' ?? false)
                    <x-home-sidebar-layout :sectionKey="$sectionsContent['home_sidebar_1']['itemsKey']" :sectionData="$sectionsContent['home_sidebar_1']" />
                @endif
                @if ($sectionsContent['ads_sidebar_1']['config']['is_visible'] == '1' ?? false)
                    <x-home-sidebar-layout :sectionKey="$sectionsContent['ads_sidebar_1']['itemsKey']" :sectionData="$sectionsContent['ads_sidebar_1']" />
                @endif
                @if ($sectionsContent['home_sidebar_2']['config']['is_visible'] == '1' ?? false)
                    <x-home-sidebar-layout :sectionKey="$sectionsContent['home_sidebar_2']['itemsKey']" :sectionData="$sectionsContent['home_sidebar_2']" />
                @endif
                @if ($sectionsContent['home_sidebar_3']['config']['is_visible'] == '1' ?? false)
                    <x-home-sidebar-layout :sectionKey="$sectionsContent['home_sidebar_3']['itemsKey']" :sectionData="$sectionsContent['home_sidebar_3']" />
                @endif
                @if ($sectionsContent['home_sidebar_4']['config']['is_visible'] == '1' ?? false)
                    <x-home-sidebar-layout :sectionKey="$sectionsContent['home_sidebar_4']['itemsKey']" :sectionData="$sectionsContent['home_sidebar_4']" />
                @endif
                @if ($sectionsContent['ads_sidebar_2']['config']['is_visible'] == '1' ?? false)
                    <x-home-sidebar-layout :sectionKey="$sectionsContent['ads_sidebar_2']['itemsKey']" :sectionData="$sectionsContent['ads_sidebar_2']" />
                @endif
            </div>
        </div>
    </div>


    @push('css')
        @vite('resources/css/ckeditor.css')
        <link href="{{ asset('assets/css/prism.css') }}" rel="stylesheet">
    @endpush

    @push('javascript')
        <script src="{{ asset('assets/js/prism.js') }}"></script>
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
