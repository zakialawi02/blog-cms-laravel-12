<article class="max-w-full p-2 text-base md:px-5 md:py-3" id="zkcomment_0212{{ $comment->id }}">
    <div class="mb-2 flex items-center justify-between">
        <div class="flex items-center">
            <p class="mr-3 inline-flex items-center text-sm font-semibold text-gray-900 dark:text-white"><img class="mr-2 h-6 w-6 rounded-full" src={{ $comment->user->profile_photo_path }} alt="{{ $comment->user->name }}">{{ $comment->user->name }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><time title="{{ $comment->created_at->format('F j, Y') }}" pubdate datetime="{{ $comment->created_at->format('Y-m-d') }}">{{ $comment->created_at->format('F j, Y') }}</time></p>
        </div>
    </div>

    <p class="text-gray-500 dark:text-gray-400">{{ $comment->content }}</p>

    <div class="my-2 flex items-center space-x-4">
        <button class="flex items-center text-sm font-medium text-gray-500 hover:underline dark:text-gray-400" type="button" onclick="toggleReplyForm({{ $comment->id }})">
            <i class="ri-message-2-line"></i>
            Reply
        </button>
        @if (auth()->check() && auth()->user()->id === $comment->user_id)
            <button class="text-back-error flex items-center text-sm font-medium hover:underline" type="button" onclick="deleteComment({{ $comment->id }})">
                <i class="ri-delete-bin-5-line"></i>
                Delete
            </button>
        @endif
    </div>
    <!-- Form Reply -->
    <form class="reply_comment mb-3" id="reply-form-zkc_0212{{ $comment->id }}" style="display: none;" action="{{ route('comment.store', request()->segment(3)) }}" method="POST">
        @csrf
        <input name="parent_id" type="hidden" value="zkc_0212{{ $comment->id }}">
        <textarea class="w-full rounded-lg border-0 p-2 text-sm text-gray-900 focus:outline-none focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400" id="reply_input{{ $comment->id }}" name="comment" placeholder="Write a reply..."></textarea>
        <x-dashboard.primary-button class="btn-submit-reply" type="submit">Submit</x-dashboard.primary-button>
    </form>

    <!-- Nested Comments -->
    @if ($comment->replies->count())
        <div class="relative mb-3 ml-2 max-w-full p-3 text-base before:absolute before:bottom-0 before:left-[-2px] before:top-0 before:w-[2px] before:bg-gray-300 lg:ml-10 dark:before:bg-gray-600">
            @foreach ($comment->replies as $reply)
                <x-comment-item :comment="$reply" />
            @endforeach
        </div>
    @endif
</article>
