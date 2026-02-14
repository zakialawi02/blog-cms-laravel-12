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

                    <form id="ai-generate-form" action="#" method="POST">
                        @csrf
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
                                <option value="gemini-3-flash-preview">Gemini 3 Flash Preview</option>
                                <option value="deepseek-v3-2-251201">DeepSeek V3</option>
                                <option value="glm-4-7-251222">GLM-4</option>
                                <option value="kimi-k2-250905">Kimi K2</option>
                                <option value="kimi-k2-thinking-251104">Kimi K2 Thinking</option>
                                <option value="seed-1-8-251228">Seed 1.8</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <x-dashboard.primary-button type="submit" class="w-full justify-center">
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
                        <button type="button" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            Clear History
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
                                    <th scope="col" class="px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Mock Data Row 1 -->
                                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3 text-nowrap">
                                        {{ now()->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <div class="line-clamp-1 truncate font-medium">Laravel 12 New Features</div>
                                    </td>
                                    <td class="px-4 py-3">Gemini Flash</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                            <span class="mr-1 h-2 w-2 rounded-full bg-green-500"></span>
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-nowrap">
                                        <button class="mr-2 rounded p-1 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors" title="View">
                                            <i class="ri-eye-line text-lg"></i>
                                        </button>
                                        <button class="mr-2 rounded p-1 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="Use as Draft">
                                            <i class="ri-file-edit-line text-lg"></i>
                                        </button>
                                        <button class="rounded p-1 text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors" title="Delete">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Mock Data Row 2 -->
                                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3 text-nowrap">
                                        {{ now()->subHour()->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <div class="line-clamp-1 truncate font-medium">Sustainable Energy Trends 2025</div>
                                    </td>
                                    <td class="px-4 py-3">GPT-4o</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            <span class="mr-1 h-2 w-2 animate-pulse rounded-full bg-yellow-500"></span>
                                            Processing
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-nowrap">
                                        <button class="rounded p-1 text-gray-400 cursor-not-allowed" disabled>
                                            <i class="ri-eye-off-line text-lg"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Mock Data Row 3 -->
                                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3 text-nowrap">
                                        {{ now()->subDays(1)->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        <div class="line-clamp-1 truncate font-medium">How to bake a cake</div>
                                    </td>
                                    <td class="px-4 py-3">Gemini Pro</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <span class="mr-1 h-2 w-2 rounded-full bg-red-500"></span>
                                            Failed
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-nowrap">
                                        <button class="mr-2 rounded p-1 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors" title="Retry">
                                            <i class="ri-refresh-line text-lg"></i>
                                        </button>
                                        <button class="rounded p-1 text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors" title="Delete">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Mock -->
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Showing <span class="font-semibold text-gray-900 dark:text-white">1-3</span> of <span class="font-semibold text-gray-900 dark:text-white">12</span></span>
                        <div class="inline-flex gap-2">
                            <button class="rounded px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700" disabled>Previous</button>
                            <button class="rounded px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">Next</button>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </section>

    @push('javascript')
        <script>
            $(document).ready(function() {
                $('#ai-generate-form').on('submit', function(e) {
                    e.preventDefault();
                    // Simple Alert for demonstration since it is frontend only
                    const topic = $('#topic').val();
                    const model = $('#model option:selected').text();

                    // Show loading state on button
                    const btn = $(this).find('button[type="submit"]');
                    const originalContent = btn.html();
                    btn.html('<i class="ri-loader-4-line animate-spin mr-2"></i> Generating...');
                    btn.prop('disabled', true);

                    // Simulate API call
                    setTimeout(() => {
                        alert(`[MOCK] Generating article about "${topic}" using ${model}...`);
                        btn.html(originalContent);
                        btn.prop('disabled', false);
                        // In a real app, this would refresh the history table or redirect
                    }, 1500);
                });
            });
        </script>
    @endpush
</x-app-layout>
