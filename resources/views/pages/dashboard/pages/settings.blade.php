@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <form method="POST" action="{{ route('admin.pages.layout.update') }}">
        @csrf
        @method('PUT')

        <section class="p-1 md:p-4">
            <div class="mb-2 px-1 text-2xl font-medium">
                <h2>{{ $data['title'] ?? 'Homepage Layout Settings' }}</h2>
            </div>

            <div class="container mb-4 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold">Layouts Configuration</h4>
                </div>
                <div>
                    <x-dashboard.primary-button type="submit">
                        Save Changes
                    </x-dashboard.primary-button>
                </div>
            </div>

            <div class="border-warning mb-4 rounded-lg border-l-4 bg-yellow-50 p-4" role="alert">
                <span class="font-medium">Warning:</span> Be cautious when adding scripts to layout widgets, as they can break the application.
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700" role="alert">
                    <span class="font-medium">Oops! There were some errors:</span>
                    <ul class="ml-4 mt-1.5 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Featured Section --}}
            <div class="container mb-3 space-y-3">
                <x-card>
                    <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                        <div>
                            <p class="font-semibold">Featured Section</p>
                            <p class="text-xs">placement: home & blog post</p>
                            <p class="text-xs">
                                Label: {{ $layouts['home_feature_section']['label'] }} |
                                Status: {{ $layouts['home_feature_section']['is_visible'] ? 'Visible' : 'Hidden' }}
                            </p>
                        </div>
                        <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_feature_section_form', this)">
                            <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                        </div>
                    </div>
                    <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_feature_section_form">
                        <x-home-section-layout-setting :sectionKey="'home_feature_section'" :sectionData="$layouts['home_feature_section']" :itemKeyOptions="$itemKeyOptions" :setTotalToView="false" />
                    </div>
                </x-card>
                {{-- ADS --}}
                <x-card>
                    <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                        <div>
                            <p class="font-semibold">Ads Section (max: 728px x 90px)</p>
                            <p class="text-xs">placement: home & blog post</p>
                            <p class="text-xs">
                                Label: {{ $layouts['ads_featured']['label'] }} |
                                Status: {{ $layouts['ads_featured']['is_visible'] ? 'Visible' : 'Hidden' }}
                            </p>
                        </div>
                        <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('ads_featured_form', this)">
                            <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                        </div>
                    </div>
                    <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="ads_featured_form">
                        <x-home-section-layout-setting :sectionKey="'ads_featured'" :sectionData="$layouts['ads_featured']" :itemKeyOptions="$itemKeyOptions" :setTotalToView="false" :is_ads="true" />
                    </div>
                </x-card>
            </div>

            {{-- Main Content Sections & Sidebar --}}
            <div class="container grid grid-cols-1 gap-1 md:gap-4 lg:grid-cols-4">
                <div class="lg:col-span-3">
                    {{-- Section 1 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Section 1</p>
                                <p class="text-xs">placement: home</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_section_1']['label'] }} |
                                    Items: {{ $layouts['home_section_1']['items'] }} |
                                    Status: {{ $layouts['home_section_1']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_section_1_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_section_1_form">
                            <x-home-section-layout-setting :sectionKey="'home_section_1'" :sectionData="$layouts['home_section_1']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Section 2 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Section 2</p>
                                <p class="text-xs">placement: home</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_section_2']['label'] }} |
                                    Items: {{ $layouts['home_section_2']['items'] }} |
                                    Status: {{ $layouts['home_section_2']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_section_2_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_section_2_form">
                            <x-home-section-layout-setting :sectionKey="'home_section_2'" :sectionData="$layouts['home_section_2']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Section 3 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Section 3</p>
                                <p class="text-xs">placement: home</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_section_3']['label'] }} |
                                    Items: {{ $layouts['home_section_3']['items'] }} |
                                    Status: {{ $layouts['home_section_3']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_section_3_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_section_3_form">
                            <x-home-section-layout-setting :sectionKey="'home_section_3'" :sectionData="$layouts['home_section_3']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Section 4 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Section 4</p>
                                <p class="text-xs">placement: home</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_section_4']['label'] }} |
                                    Items: {{ $layouts['home_section_4']['items'] }} |
                                    Status: {{ $layouts['home_section_4']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_section_4_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_section_4_form">
                            <x-home-section-layout-setting :sectionKey="'home_section_4'" :sectionData="$layouts['home_section_4']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Section 5 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Section 5 (ads (max: 728px x 90px))</p>
                                <p class="text-xs">placement: home & after article</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_section_5']['label'] }} |
                                    Items: {{ $layouts['home_section_5']['items'] }} |
                                    Status: {{ $layouts['home_section_5']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_section_5_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_section_5_form">
                            <x-home-section-layout-setting :sectionKey="'home_section_5'" :sectionData="$layouts['home_section_5']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>
                </div>

                {{-- Sidebar Sections --}}
                <div class="text-dark dark:text-dark-light">
                    {{-- Sidebar 1 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Sidebar 1</p>
                                <p class="text-xs">placement: home & single post</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_sidebar_1']['label'] }} |
                                    Items: {{ $layouts['home_sidebar_1']['items'] }} |
                                    Status: {{ $layouts['home_sidebar_1']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_sidebar_1_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_sidebar_1_form">
                            <x-home-section-layout-setting :sectionKey="'home_sidebar_1'" :sectionData="$layouts['home_sidebar_1']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Ads Sidebar 1 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Ads Sidebar 1 (max: 300px x 250px)</p>
                                <p class="text-xs">placement: home & single post</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['ads_sidebar_1']['label'] }} |
                                    Items: {{ $layouts['ads_sidebar_1']['items'] }} |
                                    Status: {{ $layouts['ads_sidebar_1']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('ads_sidebar_1_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="ads_sidebar_1_form">
                            <x-home-section-layout-setting :sectionKey="'ads_sidebar_1'" :sectionData="$layouts['ads_sidebar_1']" :itemKeyOptions="$itemKeyOptions" :setTotalToView="false" :is_ads="true" />
                        </div>
                    </x-card>

                    {{-- Sidebar 2 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Sidebar 2</p>
                                <p class="text-xs">placement: home & single post</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_sidebar_2']['label'] }} |
                                    Items: {{ $layouts['home_sidebar_2']['items'] }} |
                                    Status: {{ $layouts['home_sidebar_2']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_sidebar_2_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_sidebar_2_form">
                            <x-home-section-layout-setting :sectionKey="'home_sidebar_2'" :sectionData="$layouts['home_sidebar_2']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Sidebar 3 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Sidebar 3</p>
                                <p class="text-xs">placement: home & single post</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_sidebar_3']['label'] }} |
                                    Items: {{ $layouts['home_sidebar_3']['items'] }} |
                                    Status: {{ $layouts['home_sidebar_3']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_sidebar_3_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_sidebar_3_form">
                            <x-home-section-layout-setting :sectionKey="'home_sidebar_3'" :sectionData="$layouts['home_sidebar_3']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Sidebar 4 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Sidebar 4</p>
                                <p class="text-xs">placement: home & single post</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['home_sidebar_4']['label'] }} |
                                    Items: {{ $layouts['home_sidebar_4']['items'] }} |
                                    Status: {{ $layouts['home_sidebar_4']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_sidebar_4_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_sidebar_4_form">
                            <x-home-section-layout-setting :sectionKey="'home_sidebar_4'" :sectionData="$layouts['home_sidebar_4']" :itemKeyOptions="$itemKeyOptions" />
                        </div>
                    </x-card>

                    {{-- Ads Sidebar 2 --}}
                    <x-card class="mb-3">
                        <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                            <div>
                                <p class="font-semibold">Ads Sidebar 2 (max: 300px x 250px)</p>
                                <p class="text-xs">placement: home & single post</p>
                                <p class="text-xs">
                                    Label: {{ $layouts['ads_sidebar_2']['label'] }} |
                                    Items: {{ $layouts['ads_sidebar_2']['items'] }} |
                                    Status: {{ $layouts['ads_sidebar_2']['is_visible'] ? 'Visible' : 'Hidden' }}
                                </p>
                            </div>
                            <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('ads_sidebar_2_form', this)">
                                <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                            </div>
                        </div>
                        <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="ads_sidebar_2_form">
                            <x-home-section-layout-setting :sectionKey="'ads_sidebar_2'" :sectionData="$layouts['ads_sidebar_2']" :itemKeyOptions="$itemKeyOptions" :setTotalToView="false" :is_ads="true" />
                        </div>
                    </x-card>
                </div>
            </div>

            {{-- Bottom Section --}}
            <div class="container my-2 space-y-2.5">
                {{-- ADS --}}
                <x-card>
                    <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                        <div>
                            <p class="font-semibold">Ads Bottom 1 (max: 728px x 90px)</p>
                            <p class="text-xs">placement: home & after comment</p>
                            <p class="text-xs">
                                Label: {{ $layouts['ads_bottom_1']['label'] }} |
                                Items: {{ $layouts['ads_bottom_1']['items'] }} |
                                Status: {{ $layouts['ads_bottom_1']['is_visible'] ? 'Visible' : 'Hidden' }}
                            </p>
                        </div>
                        <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('ads_bottom_1_form', this)">
                            <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                        </div>
                    </div>
                    <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="ads_bottom_1_form">
                        <x-home-section-layout-setting :sectionKey="'ads_bottom_1'" :sectionData="$layouts['ads_bottom_1']" :itemKeyOptions="$itemKeyOptions" :setTotalToView="false" :is_ads="true" />
                    </div>
                </x-card>
                {{-- Bottom Section --}}
                <x-card>
                    <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                        <div>
                            <p class="font-semibold">Bottom Section 1</p>
                            <p class="text-xs">
                                Label: {{ $layouts['home_bottom_section_1']['label'] }} |
                                Items: {{ $layouts['home_bottom_section_1']['items'] }} |
                                Status: {{ $layouts['home_bottom_section_1']['is_visible'] ? 'Visible' : 'Hidden' }}
                            </p>
                        </div>
                        <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('home_bottom_section_1_form', this)">
                            <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                        </div>
                    </div>
                    <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="home_bottom_section_1_form">
                        <x-home-section-layout-setting :sectionKey="'home_bottom_section_1'" :sectionData="$layouts['home_bottom_section_1']" :itemKeyOptions="$itemKeyOptions" />
                    </div>
                </x-card>
                {{-- ADS --}}
                <x-card>
                    <div class="mb-2 flex items-center justify-between rounded-md bg-slate-200 p-2 dark:bg-slate-700">
                        <div>
                            <p class="font-semibold">Ads Bottom 2 (max: 728px x 90px)</p>
                            <p class="text-xs">
                                Label: {{ $layouts['ads_bottom_2']['label'] }} |
                                Items: {{ $layouts['ads_bottom_2']['items'] }} |
                                Status: {{ $layouts['ads_bottom_2']['is_visible'] ? 'Visible' : 'Hidden' }}
                            </p>
                        </div>
                        <div class="hover:text-info cursor-pointer" onclick="toggleEditForm('ads_bottom_2_form', this)">
                            <i class="ri-pencil-line text-sm"></i> <span class="edit-text text-[11px]">Edit</span>
                        </div>
                    </div>
                    <div class="hidden border-t border-slate-300 p-4 dark:border-slate-600" id="ads_bottom_2_form">
                        <x-home-section-layout-setting :sectionKey="'ads_bottom_2'" :sectionData="$layouts['ads_bottom_2']" :itemKeyOptions="$itemKeyOptions" :setTotalToView="false" :is_ads="true" />
                    </div>
                </x-card>
            </div>
        </section>
    </form>

    @push('javascript')
        {{-- Or @push('scripts') if that's your stack name --}}
        <script>
            function toggleEditForm(formId, buttonElement) {
                const formElement = document.getElementById(formId);
                const editTextElement = buttonElement.querySelector('.edit-text');
                if (formElement) {
                    if (formElement.classList.contains('hidden')) {
                        formElement.classList.remove('hidden');
                        if (editTextElement) editTextElement.textContent = 'Cancel';
                    } else {
                        formElement.classList.add('hidden');
                        if (editTextElement) editTextElement.textContent = 'Edit';
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
