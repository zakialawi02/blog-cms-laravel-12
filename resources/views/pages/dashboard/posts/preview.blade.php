@section('title', $article->title ?? ($data['title'] ?? $data['web_setting']['web_name']))
@section('meta_description', $article->excerpt ?? ($data['description'] ?? ''))
@section('meta_keywords', $article->keywords ?? ($data['keywords'] ?? ''))
@section('meta_robots', 'noindex, nofollow')

<x-app-front-layout>
    <div class="relative">
        <div class="z-1000 pointer-events-none absolute inset-0 h-full w-full">
            <div class="px-62 top-50 -left-50 absolute origin-top-left -translate-y-2 translate-x-1/4 -rotate-45 transform bg-red-800 py-1 text-lg font-bold text-white shadow-md">Preview Mode</div>
        </div>
    </div>
    <div class="container mt-6">
        <x-breadcrumb :showSegment="5" :items="[['text' => 'Home', 'link' => '/'], ['text' => 'Blog', 'link' => route('article.index')], ['text' => request()->segment(2), 'link' => '#'], ['text' => 'Preview Posts', 'link' => '#'], ['link' => '#'], ['text' => $article->title]]" />

        <div class="text-dark dark:text-dark-light flex flex-auto flex-grow flex-col flex-wrap gap-4 md:flex-row" id="main">
            <div class="w-full md:w-[60%] md:flex-grow" id="post">
                <div class="" id="main-content">
                    <div class="mb-3 py-1" id="post-header">
                        <h1 class="mb-2 text-3xl font-bold md:text-4xl">{{ $article->title }}</h1>
                        <div class="inline-flex items-center">
                            <a class="after:text-secondary hover:text-primary dark:after:text-dark-secondary dark:hover:text-dark-primary inline-flex items-center gap-1 after:relative after:top-[-3px] after:mx-2 after:px-1 after:font-black after:content-['.']" href="{{ route('article.user', $article->user->username) }}" target="_blank">
                                <img class="w-6" src="{{ $article->user->profile_photo_path }}" alt="author {{ $article->user->username }}">{{ $article->user->username }}
                            </a>
                            <a class="hover:text-primary dark:hover:text-dark-primary" href="#" target="_blank">Published date</a>
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

                    <x-card class="h-40">
                        <p>Display no avaliable in preview page</p>
                    </x-card>
                </section>
            </div>

            <div class="text-dark dark:text-dark-light mt-10 w-full md:mt-8 md:w-[30%]" id="sidebar">
                <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="popular-posts">
                    <div class="text-center text-xl font-bold">
                        <h3>Popular Posts</h3>
                    </div>

                    <div class="mx-auto p-2">
                        @for ($i = 0; $i < 4; $i++)
                            <x-card>
                                <p>Display no avaliable in preview page</p>
                            </x-card>
                        @endfor
                    </div>
                </div>
                <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="categories">
                    <div class="text-center text-xl font-bold">
                        <h3>Categories</h3>
                    </div>
                    <div class="mx-auto p-2">
                        <ul class="flex flex-col gap-2 p-2">
                            @for ($i = 0; $i < 4; $i++)
                                <x-card>
                                    <p>Display no avaliable in preview page</p>
                                </x-card>
                            @endfor
                        </ul>
                    </div>
                </div>
                <div class="border-neutral dark:border-dark-neutral mb-3 rounded-lg border-2 p-2" id="categories">
                    <div class="text-center text-xl font-bold">
                        <h3>Tags</h3>
                    </div>
                    <div class="mx-auto p-2">
                        <ul class="flex flex-wrap">
                            <x-card>
                                <p>Display no avaliable in preview page</p>
                            </x-card>
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
</x-app-front-layout>
