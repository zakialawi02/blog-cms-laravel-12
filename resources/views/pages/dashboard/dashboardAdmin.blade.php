@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="mb-1 flex flex-col items-center p-1 text-xl md:flex-row md:justify-between md:px-4 md:pt-4">
        <h2>
            {{ __('Welcome') }}, {{ Auth::user()->name }}
        </h2>

        <div class="">
            <!-- Ambil waktu awal dari server -->
            <span class="text-sm" id="server-time" data-time="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}"></span>
            <x-dashboard.primary-button id="refreshDashboard" type="button" :size="'small'">
                <i class="ri-refresh-line"></i>
                Refresh
            </x-dashboard.primary-button>
        </div>
    </section>

    <section class="mb-2 p-1 md:px-4">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-2 md:gap-3 lg:grid-cols-4">
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">My Posts</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="myPostsCount">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            posts
                        </p>
                    </div>
                    <div><i class="ri-archive-stack-fill text-back-primary dark:text-back-dark-primary text-5xl"></i></div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">My Posts Published</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="myPostsPublishedCount">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            posts
                        </p>
                    </div>
                    <div><i class="ri-mac-line text-back-success text-5xl"></i></div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">My Comments</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="myCommentsCount">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            comments
                        </p>
                    </div>
                    <div><i class="ri-message-2-fill text-back-muted dark:text-back-dark-muted text-5xl"></i></div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">Visitors</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="visitors">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            visitors
                        </p>
                    </div>
                    <div><i class="ri-bar-chart-grouped-fill text-back-warning text-5xl"></i></div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">Total All Posts</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="allPostsCount">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            posts
                        </p>
                    </div>
                    <div><i class="ri-archive-stack-fill text-back-muted dark:text-back-dark-muted text-5xl"></i></div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">All Posts Published</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="allPostsPublishedCount">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            posts
                        </p>
                    </div>
                    <div><i class="ri-mac-line text-back-success text-5xl"></i></div>
                </div>
            </x-card>
            @if (Auth::user()->role == 'superadmin')
                <x-card>
                    <div class="flex items-center justify-between p-2">
                        <div>
                            <h4 class="mb-0 text-lg">Total All Comments</h4>
                            <p class="text-back-muted dark:text-back-dark-muted items-center">
                                <span id="allCommentsCount">
                                    <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                                </span>
                                comments
                            </p>
                        </div>
                        <div><i class="ri-message-2-fill text-back-secondary text-5xl"></i></div>
                    </div>
                </x-card>
            @else
                <x-card>
                    <div class="flex items-center justify-between p-2">
                        <div>
                            <h4 class="mb-0 text-lg">Views My Posts</h4>
                            <p class="text-back-muted dark:text-back-dark-muted items-center">
                                <span id="viewsMyPosts">
                                    <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                                </span>
                                views
                            </p>
                        </div>
                        <div><i class="ri-bar-chart-2-line text-back-secondary dark:text-back-dark-secondary text-5xl"></i></div>
                    </div>
                </x-card>
            @endif
            <x-card>
                <div class="flex items-center justify-between p-2">
                    <div>
                        <h4 class="mb-0 text-lg">Total User</h4>
                        <p class="text-back-muted dark:text-back-dark-muted items-center">
                            <span id="usersCount">
                                <span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </span>
                            users
                        </p>
                    </div>
                    <div><i class="ri-group-fill text-back-error text-5xl"></i></div>
                </div>
            </x-card>
        </div>
    </section>

    <section class="p-1 md:px-4">
        <div class="grid grid-cols-1 gap-1 lg:grid-cols-2 lg:gap-3">
            <div class="mb-2 space-y-2">
                <x-card class="">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="">
                            <h4 class="mb-0 text-xl">Recent Posts</h4>
                            <p class="text-back-muted dark:text-back-dark-muted">All recently created blog posts</p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.posts.index') }}" :size="'small'">
                            View All
                        </x-dashboard.light-button>
                    </div>

                    <!-- Ajax Posts -->
                    <div class="space-y-2" id="posts-container"><span class="my-2 block h-20 min-w-full max-w-sm animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span></div>
                </x-card>
                <x-card class="">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="">
                            <h4 class="mb-0 text-xl">Recent Comments</h4>
                            <p class="text-back-muted dark:text-back-dark-muted">Recent comments activity</p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.comments.index') }}" :size="'small'">
                            View All
                        </x-dashboard.light-button>
                    </div>

                    <div class="space-y-4">
                        <!-- Ajax Comments -->
                        <div class="space-y-2" id="comments-container">
                            <span class="my-2 block h-20 min-w-full max-w-sm animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                        </div>
                    </div>
                </x-card>
            </div>
            <div class="mb-2">
                <x-card>
                    <div class="mb-2 flex items-center justify-between">
                        <div class="">
                            <h4 class="mb-0 text-xl">Popular Posts</h4>
                            <p class="text-back-muted dark:text-back-dark-muted">All popular content post</p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.posts.statsview') }}" :size="'small'">
                            Analytics
                        </x-dashboard.light-button>
                    </div>

                    <div class="table-container" id="popularPosts">
                        <table class="display table" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col">Post</th>
                                    <th style="width: 100px" scope="col">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Ajax data -->
                            <tbody id="popular-posts">
                                <tr>
                                    <td colspan="2">
                                        <span class="my-2 block h-20 w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                                    </td>
                                </tr>
                            </tbody>
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>
    </section>

    <section class="mb-5 p-1 md:px-4">
        <x-card>
            <div class="mb-3">
                <h4 class="mb-0 text-2xl">Coming Soon</h4>
            </div>
            <p>Coming Soon new features</p>
        </x-card>
    </section>

    @push('javascript')
        <script>
            $(document).ready(function() {
                const loader = `<span class="inline-block h-3 w-12 max-w-sm animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>`;
                const loader2 = `<span class="my-2 block h-20 min-w-full max-w-sm animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>`;

                getData();
                $("#refreshDashboard").click(function(e) {
                    e.preventDefault();
                    getData({
                        beforeSend: function() {
                            $('#myPostsCount').html(loader);
                            $('#allPostsPublishedCount').html(loader);
                            $('#myPostsPublishedCount').html(loader);
                            $('#allPostsCount').html(loader);
                            $('#allCommentsCount').html(loader);
                            $('#myCommentsCount').html(loader);
                            $('#viewsMyPosts').html(loader);
                            $('#visitors').html(loader);
                            $('#usersCount').html(loader);
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
                            console.log(response);
                            $('#myPostsCount').html(response.myPostsCount);
                            $('#allPostsPublishedCount').html(response.allPostsPublishedCount);
                            $('#myPostsPublishedCount').html(response.myPostsPublishedCount);
                            $('#allPostsCount').html(response.allPostsCount);
                            $('#allCommentsCount').html(response.allCommentsCount);
                            $('#myCommentsCount').html(response.myCommentsCount);
                            $('#visitors').html(response.visitors + ' all posts');
                            $('#viewsMyPosts').html(response.viewsMyPosts);
                            $('#usersCount').html(response.usersCount);
                            const articleRoute = "{{ route('article.show', ['year' => ':year', 'slug' => ':slug']) }}";

                            const posts = response.allposts;
                            const container = $('#posts-container');
                            let cardsHtml = '';
                            if (posts.length === 0) {
                                cardsHtml = `
                                    <div class="flex items-center gap-2">
                                        <i class="ri-article-line text-back-muted dark:text-back-dark-muted"></i>
                                        <p class="text-back-muted dark:text-back-dark-muted">No posts found.</p>
                                    </div>
                                `;
                            } else {
                                posts.forEach(function(post) {
                                    cardsHtml += `
                                        <x-card class="p-2!">
                                            <div class="line-clamp-2 text-lg font-semibold">
                                                <a class="text-back-dark dark:text-back-dark-light hover:text-back-primary/80 dark:hover:text-back-dark-primary/80 hover:underline" href="${articleRoute.replace(':year', new Date(post.published_at).getFullYear()).replace(':slug', post.slug)}" target="_blank">
                                                    ${post.title}
                                                </a>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <p class="text-back-muted dark:text-back-dark-muted">By:
                                                    <a class="text-back-primary dark:text-back-dark-primary hover:text-back-primary/80 dark:hover:text-back-dark-primary/80 hover:underline" href="/dashboard/posts?status=all&author=${post.user?.username}&category=all&page=1&limit=10">
                                                        ${post.user?.name || 'Unknown'}
                                                    </a>
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    <div>
                                                        <i class="ri-article-line text-back-muted dark:text-back-dark-muted"></i>
                                                        <span class="bg-${post.status === 'draft' || post.status === 'pending' ? 'back-secondary dark:bg-back-dark-secondary/50' : 'back-primary dark:bg-back-dark-primary/50'} rounded px-1 text-back-light">${post.status}</span>
                                                    </div>
                                                    <div>
                                                        <i class="ri-time-line text-back-muted dark:text-back-dark-muted"></i>
                                                        <span class="text-back-muted dark:text-back-dark-muted">${timeAgo(post.published_at ?? post.created_at)}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </x-card>
                                    `;
                                });
                            }
                            container.html(cardsHtml);

                            const popularPosts = response.popularPosts;
                            const tbody = $('#popular-posts');
                            let rows = '';
                            if (popularPosts.length === 0) {
                                rows = `
                                    <tr>
                                        <td colspan="2" class="text-center text-back-muted dark:text-back-dark-light/80">No data available.</td>
                                    </tr>
                                `;
                            } else {
                                popularPosts.forEach(function(post) {
                                    rows += `
                                        <tr>
                                            <td>
                                                <a href="${articleRoute.replace(':year', new Date(post.published_at).getFullYear()).replace(':slug', post.slug)}" class="text-blue-600 hover:underline" target="_blank">${post.title}</a>
                                            </td>
                                            <td>${post.total_views}</td>
                                        </tr>
                                    `;
                                });
                            }
                            tbody.html(rows);

                            const commentsContainer = $('#comments-container');
                            const comments = response.recentComment;
                            if (comments.length === 0) {
                                commentsContainer.html('<p class="text-sm text-gray-500">No recent comments.</p>');
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
                                html += `
                                    <div class="hover:bg-back-muted/50 flex items-start gap-4 rounded-lg p-2 transition-colors">
                                        <img class="rounded-full" src="${avatarUrl}" alt="Profile Picture" width="32" height="32" />
                                        <div class="grid gap-1">
                                            <p class="text-sm">
                                                 <span class="font-medium">${nameUser}${isSelf ? ' (You)' : ''}</span>
                                                <span class="text-back-muted dark:text-back-dark-muted"> commented on </span>
                                                <span class="font-medium">"${articleTitle}"</span>
                                                <span>:</span>
                                            </p>
                                            <p class="text-sm">${content}</p>
                                            <p class="text-back-muted text-xs">${createdAt}</p>
                                        </div>
                                    </div>
                                `;
                            });
                            commentsContainer.html(html);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data:", err);
                            MyZkToast.error(error.statusText)
                        }
                    });
                }
            });
        </script>
        <script>
            const timeElement = document.getElementById('server-time');
            let currentTime = new Date(timeElement.dataset.time);

            const hariIndo = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            function updateClock() {
                currentTime.setSeconds(currentTime.getSeconds() + 1);
                const hari = hariIndo[currentTime.getDay()];
                const tanggal = currentTime.toLocaleDateString('en-US', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const jam = currentTime.toLocaleTimeString('en-US');

                timeElement.innerText = `${hari}, ${tanggal} ${jam}`;
            }

            updateClock(); // initial
            setInterval(updateClock, 1000);
        </script>
    @endpush
</x-app-layout>
