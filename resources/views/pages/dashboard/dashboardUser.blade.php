@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="mb-3 px-1 pt-1 md:px-4 md:pt-4">
        <x-card class="relative overflow-hidden border-0 bg-gradient-to-r from-sky-500/10 via-indigo-500/10 to-emerald-500/10 p-4 md:p-6">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-sky-500/15 blur-2xl"></div>
            <div class="relative flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-back-muted dark:text-back-dark-muted">User
                        Dashboard</p>
                    <h2 class="text-2xl font-semibold md:text-3xl">
                        {{ __('Welcome') }}, {{ Auth::user()->name }}
                    </h2>
                    <p class="text-base text-back-muted dark:text-back-dark-muted">Manage your comment activity and
                        contributor application progress.</p>
                </div>
                <x-dashboard.primary-button id="refreshDashboard" type="button" :size="'small'">
                    <i class="ri-refresh-line"></i>
                    Refresh
                </x-dashboard.primary-button>
            </div>
        </x-card>
    </section>

    @if ($myRequest !== null && $myRequest->valid_code_until > now())
        <section class="mb-3 px-1 md:px-4">
            <x-card class="p-4 md:p-6">
                <div class="mb-4 text-center">
                    <h3 class="text-lg font-semibold md:text-xl">Confirm Your Contributor Code</h3>
                    <p class="text-base text-back-muted dark:text-back-dark-muted">We sent a verification code to <span class="font-medium">{{ Auth::user()->email }}</span>.</p>
                </div>

                <form class="mx-auto max-w-sm text-center" id="codeForm" action="{{ route('admin.confirmCodeContributor') }}" method="POST">
                    @csrf
                    <div class="mb-2 flex items-center justify-center gap-2">
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-1" value="First code" />
                            <x-dashboard.text-input class="h-10! w-10! text-center text-lg font-semibold" id="code-1" name="code" data-focus-input-init data-focus-input-next="code-2" type="text" maxlength="1" required />
                        </div>
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-2" value="Second code" />
                            <x-dashboard.text-input class="h-10! w-10! text-center text-lg font-semibold" id="code-2" name="code" data-focus-input-init data-focus-input-prev="code-1" data-focus-input-next="code-3" type="text" maxlength="1"
                                required />
                        </div>
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-3" value="Third code" />
                            <x-dashboard.text-input class="h-10! w-10! text-center text-lg font-semibold" id="code-3" name="code" data-focus-input-init data-focus-input-prev="code-2" data-focus-input-next="code-4" type="text" maxlength="1"
                                required />
                        </div>
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-4" value="Fourth code" />
                            <x-dashboard.text-input class="h-10! w-10! text-center text-lg font-semibold" id="code-4" name="code" data-focus-input-init data-focus-input-prev="code-3" type="text" maxlength="1" required />
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-back-muted dark:text-back-dark-muted">Enter the 4-digit verification
                        code from your email.</p>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('code')" />

                    <div class="mt-4 flex items-center justify-center gap-3">
                        <x-dashboard.primary-button id="submitCode" type="submit" :size="'small'">Submit</x-dashboard.primary-button>
                        <x-dashboard.light-button id="resendCodeBtn" type="button" :size="'small'">Resend</x-dashboard.light-button>
                    </div>
                </form>

                <form id="resendForm" class="hidden" action="{{ route('admin.requestsContributors') }}?resend={{ Auth::user()->id }}" method="POST">
                    @csrf
                </form>
            </x-card>
        </section>
    @endif

    <section class="px-1 md:px-4">
        <div class="grid grid-cols-1 gap-3 xl:grid-cols-3">
            <div class="space-y-3 xl:col-span-1">
                <x-card class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-wide text-back-muted dark:text-back-dark-muted">
                                My Comments</p>
                            <h4 class="mt-1 text-2xl font-semibold" id="myCommentsCount">
                                <span class="inline-block h-4 w-16 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700"></span>
                            </h4>
                            <p class="mt-1 text-sm text-back-muted dark:text-back-dark-muted">Total comments you have
                                posted.</p>
                        </div>
                        <i class="ri-message-2-fill rounded-xl bg-back-secondary/10 p-2 text-2xl text-back-secondary dark:text-back-dark-secondary"></i>
                    </div>
                </x-card>

                @if ($myRequest === null || ($myRequest->valid_code_until < now() && $data['web_setting']['can_join_contributor']))
                    <x-card class="p-4">
                        <h4 class="text-lg font-semibold">Join as Contributor</h4>
                        <p class="mt-1 text-base text-back-muted dark:text-back-dark-muted">Want to write and share your
                            insights? Apply now to become a contributor.</p>
                        <form class="mt-3" action="{{ route('admin.requestsContributors') }}" method="POST">
                            @csrf
                            <x-dashboard.primary-button type="submit" :size="'small'">
                                Join as Contributor/Writer
                            </x-dashboard.primary-button>
                        </form>
                    </x-card>
                @endif

                <x-card class="p-4">
                    <h4 class="text-lg font-semibold">What Next</h4>
                    <p class="mt-1 text-base text-back-muted dark:text-back-dark-muted">Build your reputation through
                        quality comments, then start publishing as a contributor.</p>
                </x-card>
            </div>

            <div class="xl:col-span-2">
                <x-card class="p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <h4 class="text-xl font-semibold">My Recent Comments</h4>
                            <p class="text-base text-back-muted dark:text-back-dark-muted">Your most recent comments
                                across posts.</p>
                        </div>
                        <x-dashboard.light-button href="{{ route('admin.mycomments.index') }}" :size="'small'">
                            View All
                        </x-dashboard.light-button>
                    </div>

                    <div class="space-y-2" id="comments-container">
                        <span class="my-2 block h-20 min-w-full animate-pulse rounded bg-gray-200 dark:bg-gray-700"></span>
                    </div>
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
                            $('#myCommentsCount').html(response.myCommentsCount ?? '-');

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

            const resendButton = document.getElementById('resendCodeBtn');
            const resendForm = document.getElementById('resendForm');
            if (resendButton && resendForm) {
                resendButton.addEventListener('click', function() {
                    resendForm.submit();
                });
            }

            $("#codeForm").submit(function(e) {
                e.preventDefault();
                const $form = $(this);
                const code = $form.find('input[name="code"]').map(function() {
                    return $(this).val();
                }).get().join('');

                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: {
                        code: code
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $form.find("#submitCode").prop("disabled", true).text("Sending...");
                    },
                    success: function(response) {
                        MyZkToast.success(response.message);
                        if (response.info) {
                            MyZkToast.info(response.info);
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(error) {
                        MyZkToast.error(error.responseJSON?.message ?? error.statusText);
                    },
                    complete: function() {
                        $form.find("#submitCode").prop("disabled", false).text("Submit");
                    }
                });
            });
        </script>

        <script>
            function focusNextInput(el, prevId, nextId) {
                if (el.value.length === 0 && prevId) {
                    document.getElementById(prevId).focus();
                    return;
                }
                if (el.value.length > 0 && nextId) {
                    document.getElementById(nextId).focus();
                }
            }

            document.querySelectorAll('[data-focus-input-init]').forEach(function(element) {
                element.addEventListener('keyup', function() {
                    const prevId = this.getAttribute('data-focus-input-prev');
                    const nextId = this.getAttribute('data-focus-input-next');
                    focusNextInput(this, prevId, nextId);
                });

                element.addEventListener('paste', function(event) {
                    event.preventDefault();
                    const pasteData = (event.clipboardData || window.clipboardData).getData('text');
                    const digits = pasteData.replace(/\D/g, '');
                    const inputs = document.querySelectorAll('[data-focus-input-init]');

                    inputs.forEach((input, index) => {
                        if (digits[index]) {
                            input.value = digits[index];
                            const nextId = input.getAttribute('data-focus-input-next');
                            if (nextId) {
                                document.getElementById(nextId).focus();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
