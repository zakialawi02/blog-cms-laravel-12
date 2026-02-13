@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="mb-3 px-1 pt-1 md:px-4 md:pt-4">
        <x-card class="relative overflow-hidden border-0 bg-gradient-to-r from-emerald-500/10 via-sky-500/10 to-amber-500/10 p-4 md:p-6">
            <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 h-28 w-28 rounded-full bg-sky-500/10 blur-2xl"></div>
            <div class="relative flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-back-muted dark:text-back-dark-muted">Writer
                        Dashboard</p>
                    <h2 class="text-2xl font-semibold md:text-3xl">
                        {{ __('Welcome') }}, {{ Auth::user()->name }}
                    </h2>
                    <p class="text-base text-back-muted dark:text-back-dark-muted">Track your content performance and
                        reader engagement in one place.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full border border-back-primary/20 px-3 py-1 text-sm font-medium text-back-muted dark:text-back-dark-muted" id="server-time" data-time="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}"></span>
                    <x-dashboard.primary-button id="refreshDashboard" type="button" :size="'small'">
                        <i class="ri-refresh-line"></i>
                        Refresh
                    </x-dashboard.primary-button>
                </div>
            </div>
        </x-card>
    </section>

    <section class="mb-2 px-1 md:px-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-card class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-back-muted dark:text-back-dark-muted">
                            My Posts</p>
                        <h4 class="mt-1 text-2xl font-semibold" id="myPostsCount">
                            <span class="inline-block h-4 w-16 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                        </h4>
                        <p class="mt-1 text-sm text-back-muted dark:text-back-dark-muted">Total posts you have created.
                        </p>
                    </div>
                    <i class="ri-archive-stack-fill rounded-xl bg-back-primary/10 p-2 text-2xl text-back-primary dark:text-back-dark-primary"></i>
                </div>
            </x-card>
            <x-card class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-back-muted dark:text-back-dark-muted">
                            Published</p>
                        <h4 class="mt-1 text-2xl font-semibold" id="myPostsPublishedCount">
                            <span class="inline-block h-4 w-16 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                        </h4>
                        <p class="mt-1 text-sm text-back-muted dark:text-back-dark-muted">Posts published and live.</p>
                    </div>
                    <i class="ri-rocket-2-fill rounded-xl bg-back-success/10 p-2 text-2xl text-back-success"></i>
                </div>
            </x-card>
            <x-card class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-back-muted dark:text-back-dark-muted">
                            My Comments</p>
                        <h4 class="mt-1 text-2xl font-semibold" id="myCommentsCount">
                            <span class="inline-block h-4 w-16 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                        </h4>
                        <p class="mt-1 text-sm text-back-muted dark:text-back-dark-muted">Comments you have posted.</p>
                    </div>
                    <i class="ri-message-2-fill rounded-xl bg-back-secondary/10 p-2 text-2xl text-back-secondary dark:text-back-dark-secondary"></i>
                </div>
            </x-card>
            <x-card class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-back-muted dark:text-back-dark-muted">
                            Views</p>
                        <h4 class="mt-1 text-2xl font-semibold" id="viewsMyPosts">
                            <span class="inline-block h-4 w-16 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                        </h4>
                        <p class="mt-1 text-sm text-back-muted dark:text-back-dark-muted">Total views across all your
                            posts.</p>
                    </div>
                    <i class="ri-bar-chart-box-fill rounded-xl bg-amber-500/10 p-2 text-2xl text-amber-500"></i>
                </div>
            </x-card>
        </div>
    </section>

    <section class="px-1 md:px-4">
        <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
            <div class="space-y-3">
                <x-card class="p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <h4 class="text-xl font-semibold">Recent Posts</h4>
                            <p class="text-base text-back-muted dark:text-back-dark-muted">Your latest created posts.
                            </p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.posts.index') }}" :size="'small'">View
                            All</x-dashboard.light-button>
                    </div>
                    <div class="space-y-2" id="posts-container">
                        <span class="my-2 block h-20 min-w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                    </div>
                </x-card>

                <x-card class="p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <h4 class="text-xl font-semibold">Recent Comments</h4>
                            <p class="text-base text-back-muted dark:text-back-dark-muted">Recent comments on your
                                posts.</p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.comments.index') }}" :size="'small'">View
                            All</x-dashboard.light-button>
                    </div>
                    <div class="space-y-2" id="comments-container">
                        <span class="my-2 block h-20 min-w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                    </div>
                </x-card>
            </div>

            <div class="space-y-3">
                <x-card class="p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <h4 class="text-xl font-semibold">Popular Posts</h4>
                            <p class="text-base text-back-muted dark:text-back-dark-muted">Your top posts by view count.
                            </p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.posts.statsview') }}" :size="'small'">Analytics</x-dashboard.light-button>
                    </div>

                    <div class="table-container" id="popularPosts">
                        <table class="display table" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col">Post</th>
                                    <th style="width: 100px" scope="col">Views</th>
                                </tr>
                            </thead>
                            <tbody id="popular-posts">
                                <tr>
                                    <td colspan="2">
                                        <span class="my-2 block h-20 w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </x-card>

                <x-card class="p-4">
                    <h4 class="text-xl font-semibold">Next Improvements</h4>
                    <p class="mt-1 text-sm text-back-muted dark:text-back-dark-muted">More detailed analytics and topic
                        recommendations are coming soon.</p>
                </x-card>
            </div>
        </div>
    </section>

    @push('javascript')
        <script>
            $(document).ready(function() {
                const loader =
                    `<span class="inline-block h-4 w-16 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>`;
                const loader2 =
                    `<span class="my-2 block h-20 min-w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>`;

                getData();
                $("#refreshDashboard").click(function(e) {
                    e.preventDefault();
                    getData({
                        beforeSend: function() {
                            $('#myPostsCount').html(loader);
                            $('#myPostsPublishedCount').html(loader);
                            $('#myCommentsCount').html(loader);
                            $('#viewsMyPosts').html(loader);
                            $('#posts-container').html(loader2);
                            $('#comments-container').html(loader2);
                            $('#popular-posts').html(`
                                <tr>
                                    <td colspan="2">
                                        <span class="my-2 block h-20 w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                                    </td>
                                </tr>
                            `);
                        }
                    });
                });

                function getData(extraOptions = {}) {
                    return $.ajax({
                        type: "GET",
                        url: "{{ url()->full() }}",
                        data: {
                            ajax: true
                        },
                        dataType: "json",
                        ...extraOptions,
                        success: function(response) {
                            $('#myPostsCount').html(response.myPostsCount ?? '-');
                            $('#myPostsPublishedCount').html(response.myPostsPublishedCount ?? '-');
                            $('#myCommentsCount').html(response.myCommentsCount ?? '-');
                            $('#viewsMyPosts').html(response.viewsMyPosts ?? '-');

                            const articleRoute =
                                "{{ route('article.show', ['year' => ':year', 'slug' => ':slug']) }}";
                            const posts = response.allposts ?? [];
                            const container = $('#posts-container');
                            let cardsHtml = '';

                            if (posts.length === 0) {
                                cardsHtml = `
                                    <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-back-muted dark:border-gray-700 dark:text-back-dark-muted">
                                        No recent posts yet.
                                    </div>
                                `;
                            } else {
                                posts.forEach(function(post) {
                                    const postYear = new Date(post.published_at ?? post.created_at)
                                        .getFullYear();
                                    cardsHtml += `
                                        <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-800">
                                            <div class="line-clamp-2 text-base font-semibold">
                                                <a class="hover:text-back-primary hover:underline dark:hover:text-back-dark-primary" href="${articleRoute.replace(':year', postYear).replace(':slug', post.slug)}" target="_blank">
                                                    ${post.title}
                                                </a>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-sm">
                                                <p class="text-back-muted dark:text-back-dark-muted">By:
                                                    <a class="text-back-primary hover:underline dark:text-back-dark-primary" href="/dashboard/posts?status=all&author=${post.user?.username}&category=all&page=1&limit=10">
                                                        ${post.user?.name || 'Unknown'}
                                                    </a>
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    <span class="rounded bg-${post.status === 'draft' || post.status === 'pending' ? 'back-secondary dark:bg-back-dark-secondary/50' : 'back-primary dark:bg-back-dark-primary/50'} px-2 py-0.5 text-sm text-back-light">${post.status}</span>
                                                    <span class="text-sm text-back-muted dark:text-back-dark-muted">${timeAgo(post.published_at ?? post.created_at)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                            }
                            container.html(cardsHtml);

                            const popularPosts = response.popularPosts ?? [];
                            const tbody = $('#popular-posts');
                            let rows = '';
                            if (popularPosts.length === 0) {
                                rows = `
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-back-muted dark:text-back-dark-light/80">No data available.</td>
                                    </tr>
                                `;
                            } else {
                                popularPosts.forEach(function(post) {
                                    const postYear = new Date(post.published_at ?? post.created_at)
                                        .getFullYear();
                                    rows += `
                                        <tr>
                                            <td>
                                                <a href="${articleRoute.replace(':year', postYear).replace(':slug', post.slug)}" class="text-blue-600 hover:underline" target="_blank">${post.title}</a>
                                            </td>
                                            <td>${post.total_views}</td>
                                        </tr>
                                    `;
                                });
                            }
                            tbody.html(rows);

                            const commentsContainer = $('#comments-container');
                            const comments = response.recentComment ?? [];
                            if (comments.length === 0) {
                                commentsContainer.html(
                                    '<p class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-back-muted dark:border-gray-700 dark:text-back-dark-muted">No recent comments yet.</p>'
                                );
                                return;
                            }

                            let html = '';
                            const authUserId = @json(auth()->id());
                            comments.forEach(comment => {
                                const isSelf = comment.user?.id === authUserId;
                                const nameUser = comment.user?.name ?? 'Unknown';
                                const avatarUrl = comment.user?.profile_photo_path;
                                const articleTitle = comment.article?.title ?? 'Untitled';
                                const content = comment.content ?? '';
                                const createdAt = timeAgo(comment.created_at);
                                const avatarElement = avatarUrl ?
                                    `<img class="h-8 w-8 rounded-full object-cover" src="${avatarUrl}" alt="Profile Picture" width="32" height="32" />` :
                                    `<div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-sm font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-200">${nameUser.charAt(0)}</div>`;

                                html += `
                                    <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 transition-colors hover:bg-back-muted/20 dark:border-gray-800">
                                        ${avatarElement}
                                        <div class="grid gap-1">
                                            <p class="text-sm">
                                                <span class="font-medium">${nameUser}${isSelf ? ' (You)' : ''}</span>
                                                <span class="text-back-muted dark:text-back-dark-muted"> commented on </span>
                                                <span class="font-medium">\"${articleTitle}\"</span>
                                            </p>
                                            <p class="text-sm text-back-muted dark:text-back-dark-muted">${content}</p>
                                            <p class="text-sm text-back-muted dark:text-back-dark-muted">${createdAt}</p>
                                        </div>
                                    </div>
                                `;
                            });
                            commentsContainer.html(html);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data:", error);
                            MyZkToast.error(xhr?.statusText || error || 'Failed to load dashboard data');
                        }
                    });
                }
            });
        </script>

        <script>
            const timeElement = document.getElementById('server-time');
            if (timeElement) {
                let currentTime = new Date(timeElement.dataset.time);
                const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                function updateClock() {
                    currentTime.setSeconds(currentTime.getSeconds() + 1);
                    const day = dayNames[currentTime.getDay()];
                    const dateText = currentTime.toLocaleDateString('en-US', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    const timeText = currentTime.toLocaleTimeString('en-US');

                    timeElement.innerText = `${day}, ${dateText} ${timeText}`;
                }

                updateClock();
                setInterval(updateClock, 1000);
            }
        </script>
    @endpush
</x-app-layout>
