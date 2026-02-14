<x-guest-layout>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Unsubscribed Successfully</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">You have been successfully unsubscribed from our newsletter. We're sorry to see you go!</p>
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-150 ease-in-out">
                        Return to Home
                    </a>
                    <a href="{{ URL::signedRoute('newsletter.resubscribe', ['newsletter' => $newsletter->id]) }}" class="ml-2 inline-block bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-150 ease-in-out">
                        Mistake? Resubscribe
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
