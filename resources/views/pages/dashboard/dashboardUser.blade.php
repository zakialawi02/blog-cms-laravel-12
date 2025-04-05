@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="mb-1 flex items-center justify-between p-1 text-xl md:px-4 md:pt-4">
        <p>
            {{ __('Welcome') }}, {{ Auth::user()->name }}
        </p>
        <x-dashboard.primary-button id="refreshDashboard" type="button" :size="'small'">
            <i class="ri-refresh-line"></i>
            Refresh
        </x-dashboard.primary-button>
    </section>

    <section class="p-1 md:px-4">
        <div class="grid grid-cols-1 gap-1 lg:grid-cols-2 lg:gap-3">
            <div class="mb-2">
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
            </div>
            <div class="mb-2">
                <x-card class="">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="">
                            <h4 class="mb-0 text-xl">My Comments</h4>
                            <p class="text-back-muted dark:text-back-dark-muted">Your recent comments</p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.mycomments.index') }}" :size="'small'">
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
        </div>
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
                            $('#myCommentsCount').html(loader);
                            $('#comments-container').html(loader2);
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
                            $('#myCommentsCount').html(response.myCommentsCount);

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
    @endpush
</x-app-layout>
