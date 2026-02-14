@section('title', 'AI Article Generator')
@section('meta_description', 'Generate high-quality articles using AI')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-3 text-xl font-medium">
            <h2>AI Article Generator</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Form Section -->
            <div class="lg:col-span-1">
                <x-card>
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Generation</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Create a new article with AI.</p>
                    </div>

                    <form id="ai-generate-form" action="{{ route('admin.posts.ai-generator.store') }}" method="POST">
                        @csrf

                        <!-- Category & Topic -->
                        <div class="mb-4">
                            <x-dashboard.input-label for="category" :value="__('Category')" />
                            <div class="flex gap-2">
                                <select id="category" name="category" class="mt-1 block w-1/3 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="generateIdeas()" id="btn-generate-ideas" class="mt-1 flex items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:bg-purple-500 dark:hover:bg-purple-600">
                                    <i class="ri-lightbulb-flash-line mr-2"></i> Generate Ideas
                                </button>
                            </div>
                        </div>

                        <!-- Ideas Result Container -->
                        <div id="ideas-container" class="mb-4 hidden rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-900 dark:bg-purple-900/20">
                            <h4 class="mb-2 text-sm font-semibold text-purple-900 dark:text-purple-300">Topic Ideas (Click to select):</h4>
                            <div id="ideas-list" class="flex flex-col gap-2">
                                <!-- Ideas will be injected here -->
                            </div>
                        </div>

                        <!-- Topic -->
                        <div class="mb-4">
                            <x-dashboard.input-label for="topic" :value="__('Topic / Title')" />
                            <x-dashboard.text-input id="topic" name="topic" type="text" class="mt-1 block w-full" placeholder="e.g. The Future of Web Development" required />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Describe what you want to write about.</p>
                        </div>

                        <!-- Language -->
                        <div class="mb-4">
                            <x-dashboard.input-label for="language" :value="__('Language')" />
                            <select id="language" name="language" class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                                <option value="en">English (US)</option>
                                <option value="id">Indonesian (Bahasa Indonesia)</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                            </select>
                        </div>

                        <!-- Model -->
                        <div class="mb-4">
                            <x-dashboard.input-label for="model" :value="__('AI Model')" />
                            <select id="model" name="model" class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                                @foreach (config('ai.models') as $provider => $models)
                                    <optgroup label="{{ ucfirst($provider) }}">
                                        @foreach ($models as $key => $name)
                                            <option value="{{ $key }}">{{ $name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <x-dashboard.primary-button type="submit" class="w-full justify-center" id="btn-submit">
                                <i class="ri-sparkling-fill mr-2"></i>
                                <span>Generate Article</span>
                            </x-dashboard.primary-button>
                        </div>
                    </form>
                </x-card>
            </div>

            <!-- History Section -->
            <div class="lg:col-span-2">
                <x-card>
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Generation History</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your previously generated content.</p>
                        </div>
                        <button type="button" onclick="refreshHistory()" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            <i class="ri-refresh-line mr-1"></i> Refresh
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Date</th>
                                    <th scope="col" class="px-4 py-3">Topic</th>
                                    <th scope="col" class="px-4 py-3">Model</th>
                                    <th scope="col" class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                                @include('pages.dashboard.posts.partials.ai-generator-table')
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $history->links() }}
                    </div>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Result Modal (Simple implementation) -->
    <div id="result-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
        <div class="w-full max-w-4xl rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800 h-[80vh] flex flex-col">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Generated Content</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto rounded border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 font-mono text-sm" id="modal-content">
                <!-- Content goes here -->
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="closeModal()" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Close</button>
                <button onclick="copyModalContent()" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Copy HTML</button>
            </div>
        </div>
    </div>

    @push('javascript')
        <script>
            // AJAX Removed - Standard Form Submission
            $(document).ready(function() {
                // Simple loading state on submit (allow default action)
                $('#ai-generate-form').on('submit', function() {
                    const form = $(this);
                    const btn = form.find('button[type="submit"]');
                    btn.html('<i class="ri-loader-4-line animate-spin mr-2"></i> Generating...');
                    btn.prop('disabled', true);
                    // No e.preventDefault() here, let it submit
                });
            });

            function refreshHistory() {
                const btn = $('button[onclick="refreshHistory()"]');
                const originalContent = btn.html();
                btn.html('<i class="ri-loader-4-line animate-spin mr-1"></i> Refreshing...');
                btn.prop('disabled', true);

                $.get("{{ route('admin.posts.ai-generator.index') }}", function(data) {
                        $('#history-table-body').html(data);
                    })
                    .always(function() {
                        btn.html(originalContent);
                        btn.prop('disabled', false);
                    });
            }

            function generateIdeas() {
                const category = $('#category').val();
                if (!category) {
                    alert('Please select a category first.');
                    return;
                }

                const btn = $('#btn-generate-ideas');
                const originalContent = btn.html();
                btn.html('<i class="ri-loader-4-line animate-spin mr-2"></i> Generating...');
                btn.prop('disabled', true);
                $('#ideas-container').addClass('hidden');

                $.ajax({
                    url: "{{ route('admin.posts.ai-generator.generate-ideas') }}",
                    type: 'POST',
                    data: {
                        category: category,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            const list = $('#ideas-list');
                            list.empty();
                            response.data.forEach(idea => {
                                const button = $('<button>', {
                                    type: 'button',
                                    class: 'text-left text-sm text-gray-700 dark:text-gray-300 hover:text-purple-700 dark:hover:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/40 p-2 rounded transition-colors',
                                    text: idea,
                                    click: function() {
                                        $('#topic').val(idea);
                                        // Optional: Visual feedback
                                        $('#ideas-list button').removeClass('bg-purple-200 dark:bg-purple-800');
                                        $(this).addClass('bg-purple-200 dark:bg-purple-800');
                                    }
                                });
                                list.append(button);
                            });
                            $('#ideas-container').removeClass('hidden');
                        } else {
                            alert('Failed to generate ideas: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while generating ideas.');
                    },
                    complete: function() {
                        btn.html(originalContent);
                        btn.prop('disabled', false);
                    }
                });
            }

            // Modal & Copy Utils
            function viewResult(id) {
                const content = $(`#result-${id}`).text(); // plain text to show HTML code
                // or .html() if we want to render it, but for copy we usually want code
                // Requirement: "generate article... purely in HTML format". The result IS HTML.
                // Displaying it: likely user wants to see the code to copy-paste into editor.
                $('#modal-content').text(content);
                $('#result-modal').removeClass('hidden');
            }

            function closeModal() {
                $('#result-modal').addClass('hidden');
            }

            function copyToClipboard(elementId) {
                const content = $(`#${elementId}`).text();
                navigator.clipboard.writeText(content).then(() => {
                    // Toast or visual feedback could go here
                    alert('Copied to clipboard!');
                });
            }

            function copyModalContent() {
                const content = $('#modal-content').text();
                navigator.clipboard.writeText(content).then(() => {
                    alert('Copied to clipboard!');
                });
            }
        </script>
    @endpush
</x-app-layout>
