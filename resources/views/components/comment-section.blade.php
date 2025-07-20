@props(['comments' => [], 'showCommentsSection' => true])

<div class="mb-3">
    <div class="mb-2 mt-6" id="content-comment-container">
        @if ($showCommentsSection)
            @forelse ($comments as $comment)
                <x-comment-item :comment="$comment" />
            @empty
                <p class="text-gray-500 dark:text-gray-400">{{ __('No comments yet. Be the first to comment!') }}</p>
            @endforelse
        @endif
    </div>
</div>

@push('javascript')
    <script>
        function toggleReplyForm(commentId) {
            document.getElementById('reply-form-zkc_0212' + commentId).style.display =
                document.getElementById('reply-form-zkc_0212' + commentId).style.display === 'none' ? 'block' : 'none';
        }

        function deleteComment(commentId) {
            const slug = "{{ request()->segment(3) }}";
            const url = "{{ route('comment.destroy', ':commentId') }}".replace(':commentId', commentId);
            ZkPopAlert.show({
                message: "Are you sure you want to delete this comment?",
                confirmText: "Yes, delete it",
                cancelText: "No, cancel",
                onConfirm: () => {
                    ajaxRequest(url).done(() => {
                        loadComments();
                    })
                }
            });
        }

        function ajaxRequest(url, type = 'DELETE', extraOptions = {}) {
            return $.ajax({
                type: type,
                url: url,
                ...extraOptions, // merge options dari luar
                success: function(response) {
                    MyZkToast.success(response.message);
                },
                error: function(error) {
                    MyZkToast.error(error.statusText)
                }
            });
        }

        $(document).on("submit", ".reply_comment", function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const formData = form.serialize();
            const submitButton = form.find(".btn-submit-reply");

            $.ajax({
                type: "POST",
                url: "{{ route('comment.store', request()->segment(3)) }}",
                data: formData,
                dataType: "json",
                beforeSend: function() {
                    form.find("#reply-back-error").html("");
                    submitButton.prop("disabled", true).html("Sending...");
                },
                success: function(response) {
                    MyZkToast.success(response?.message ?? "Comment posted successfully");
                    loadComments();
                    form[0].reset();
                },
                error: function(response) {
                    console.log(response?.responseJSON?.errors?.comment[0]);
                    MyZkToast.error(response?.responseJSON?.message + ': <br>' + response?.responseJSON?.errors?.comment[0] ?? "Failed to post comment");
                },
                complete: function() {
                    submitButton.prop("disabled", false).html("Submit");
                }
            });
        });
    </script>
@endpush
