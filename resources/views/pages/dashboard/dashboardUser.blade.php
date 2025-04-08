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

    @if ($myRequest !== null && $myRequest->valid_code_until > now())
        <section class="p-1 md:px-4">
            <x-card>
                <div class="mb-2 text-center">
                    <h3 class="font-size-18 mt-3">Enter your code to confirm your request to become a contributor</h3>
                    <p class="text-muted">We've sent a code to your mail, {{ Auth::user()->email }}</p>
                </div>
                <form class="mx-auto max-w-sm text-center" id="codeForm" action="{{ route('admin.confirmCodeContributor') }}" method="POST">
                    @csrf
                    <div class="mb-2 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-1" value="First code" />
                            <x-dashboard.text-input class="h-9! w-9!" id="code-1" name="code" data-focus-input-init data-focus-input-next="code-2" type="text" maxlength="1" required />
                        </div>
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-2" value="Second code" />
                            <x-dashboard.text-input class="h-9! w-9!" id="code-2" name="code" data-focus-input-init data-focus-input-prev="code-1" data-focus-input-next="code-3" type="text" maxlength="1" required />
                        </div>
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-3" value="Third code" />
                            <x-dashboard.text-input class="h-9! w-9!" id="code-3" name="code" data-focus-input-init data-focus-input-prev="code-2" data-focus-input-next="code-4" type="text" maxlength="1" required />
                        </div>
                        <div>
                            <x-dashboard.input-label class="sr-only" for="code-4" value="Fourth code" />
                            <x-dashboard.text-input class="h-9! w-9!" id="code-4" name="code" data-focus-input-init data-focus-input-prev="code-3" type="text" maxlength="1" required />
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" id="helper-text-explanation">Please input the 4 digit code we sent via email.</p>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('code')" />

                    <div class="mt-4 flex items-center justify-center gap-4">
                        <x-dashboard.primary-button id="submitCode" type="submit" :size="'small'">
                            Submit
                        </x-dashboard.primary-button>

                        <!-- Resend button as a JS-triggered form -->
                        <x-dashboard.light-button id="resendCodeBtn" type="button" :size="'small'">
                            Resend
                        </x-dashboard.light-button>
                    </div>
                </form>

                <!-- Hidden resend form -->
                <form id="resendForm" style="display: none;" action="{{ route('admin.requestsContributors') }}?resend={{ Auth::user()->id }}" method="POST">
                    @csrf
                </form>

            </x-card>
        </section>
    @endif

    <section class="p-1 md:px-4">
        <div class="grid grid-cols-1 gap-1 lg:grid-cols-2 lg:gap-3">
            <div class="mb-2 space-y-2">
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
                @if ($myRequest === null || ($myRequest->valid_code_until < now() && $data['web_setting']['can_join_contributor']))
                    <x-card>
                        <p>Want to be a part of our community and contribute as a writer? Click the button below to join our team!</p>
                        <form action="{{ route('admin.requestsContributors') }}" method="POST">
                            @csrf

                            <x-dashboard.primary-button type="submit" :size="'small'">
                                Join as Contributor/Writer
                            </x-dashboard.primary-button>
                        </form>
                    </x-card>
                @endif
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

    <section class="p-1 md:px-4">
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

            // JavaScript to handle resend
            document.getElementById('resendCodeBtn').addEventListener('click', function() {
                document.getElementById('resendForm').submit();
            });

            $("#codeForm").submit(function(e) {
                e.preventDefault();
                const code = $('input[name="code"]').map(function() {
                    return $(this).val();
                }).get().join('');
                console.log(code);

                const url = $(this).attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        code: code
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $(this).find(".btn-submit").prop("disabled", true).html("Sending...");
                    },
                    success: function(response) {
                        console.log(response);
                        MyZkToast.success(response.message);
                        MyZkToast.info(response.info);
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000)
                    },
                    error: function(error) {
                        console.error(error);
                        MyZkToast.error(error.responseJSON.message ?? error.statusText);
                    },
                    complete: function() {
                        $(this).find(".btn-submit").prop("disabled", false).html("Submit");
                    }
                });
            });
        </script>

        <script>
            // use this simple function to automatically focus on the next input
            function focusNextInput(el, prevId, nextId) {
                if (el.value.length === 0) {
                    if (prevId) {
                        document.getElementById(prevId).focus();
                    }
                } else {
                    if (nextId) {
                        document.getElementById(nextId).focus();
                    }
                }
            }

            document.querySelectorAll('[data-focus-input-init]').forEach(function(element) {
                element.addEventListener('keyup', function() {
                    const prevId = this.getAttribute('data-focus-input-prev');
                    const nextId = this.getAttribute('data-focus-input-next');
                    focusNextInput(this, prevId, nextId);
                });

                // Handle paste event to split the pasted code into each input
                element.addEventListener('paste', function(event) {
                    event.preventDefault();
                    const pasteData = (event.clipboardData || window.clipboardData).getData('text');
                    const digits = pasteData.replace(/\D/g, ''); // Only take numbers from the pasted data

                    // Get all input fields
                    const inputs = document.querySelectorAll('[data-focus-input-init]');

                    // Iterate over the inputs and assign values from the pasted string
                    inputs.forEach((input, index) => {
                        if (digits[index]) {
                            input.value = digits[index];
                            // Focus the next input after filling the current one
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
