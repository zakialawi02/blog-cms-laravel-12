@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">

        <form id="post-form" action="{{ route('admin.posts.update', $post->slug) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="align-center mb-3 flex items-center justify-between gap-3">
                <div class="">
                    <x-dashboard.neutral-button type="button" onclick="window.history.length > 1 ? history.back() : window.location='{{ route('admin.posts.index') }}'">
                        <i class="ri-arrow-left-line"></i>
                        <span>Back</span>
                    </x-dashboard.neutral-button>
                </div>

                <div class="flex flex-wrap justify-end gap-1 space-x-0.5 md:space-x-1.5">
                    <x-dashboard.primary-button name="action" value="publish" type="submit">
                        <i class="ri-save-3-line"></i>
                        <span>Update and {{ Auth::user()->role === 'writer' ? 'Save' : 'Publish' }}</span>
                    </x-dashboard.primary-button>
                    <x-dashboard.secondary-button name="action" value="draft" type="submit">
                        <i class="ri-draft-line"></i>
                        <span>Save As Draft</span>
                    </x-dashboard.secondary-button>
                </div>
            </div>


            <div class="mb-3 border-b border-gray-200 dark:border-gray-700">
                <ul class="-mb-px flex flex-wrap text-center text-sm font-medium" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block rounded-t-lg border-b-2 p-4" id="content-styled-tab" data-tabs-target="#styled-content" type="button" role="tab" aria-controls="content" aria-selected="{{ $errors->get('title') || $errors->get('slug') ? 'true' : 'false' }}">content</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300" id="metadata-styled-tab" data-tabs-target="#styled-metadata" type="button" role="tab" aria-controls="metadata" aria-selected="{{ $errors->get('meta_title') || $errors->get('meta_desc') || $errors->get('meta_keywords') ? 'true' : 'false' }}">metadata</button>
                    </li>
                </ul>
            </div>

            <!-- Tab content -->
            <div class="mb-3" id="default-styled-tab-content">
                <div class="hidden" id="styled-content" role="tabpanel" aria-labelledby="content-tab">
                    <div class="grid grid-cols-1 gap-2 lg:grid-cols-3 lg:gap-3">
                        <div class="lg:col-span-2">
                            <x-card>
                                <div class="mb-3">
                                    <x-dashboard.input-label for="title" :value="__('Title')" />
                                    <x-dashboard.text-input class="mt-1" id="title" name="title" type="text" value="{{ old('title', $post->title) }}" placeholder="title" required autofocus />
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('title')" />
                                </div>

                                <div class="mb-3">
                                    <x-dashboard.input-label for="category_id" :value="__('Category')" />
                                    <select class="focus:ring-back-primary focus:border-back-primary dark:focus:border-back-dark-primary block w-full rounded-lg border border-gray-300 bg-gray-50 px-2.5 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-500" id="category_id" name="category_id">
                                        <option value="">-- Select Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>{{ $category->category }}</option>
                                        @endforeach
                                    </select>
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('category_id')" />
                                </div>

                                <div class="mb-3">
                                    <x-dashboard.input-label for="excerpt" :value="__('Excerpt/Summary/Intro')" />
                                    <x-dashboard.textarea-input id="excerpt" name="excerpt" rows="4" placeholder="Write your excerpt here...">{{ old('excerpt', $post->excerpt) }}</x-dashboard.textarea-input>
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('excerpt')" />
                                </div>

                                <div class="mb-3">
                                    <x-dashboard.input-label for="tags" :value="__('Tags')" />
                                    <x-dashboard.text-input class="tagify--custom-dropdown w-full" id="tags" name="tags" placeholder="Type to add tags. Use comma to separate tags and new tag" />
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('tags')" />
                                </div>
                            </x-card>
                        </div>
                        <div class="">
                            <x-card>
                                <div class="mb-3">
                                    <x-dashboard.input-label for="slug" :value="__('Slug / url post')" />
                                    <div class="relative">
                                        <x-dashboard.text-input class="mt-1" id="slug" name="slug" type="text" value="{{ old('slug', $post->slug) }}" placeholder="slug-url" readonly required />
                                        <x-dashboard.secondary-button class="text-back-light ri-pencil-fill absolute bottom-0.5 end-2" id="edit-slug" type="button">
                                        </x-dashboard.secondary-button>
                                    </div>
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('slug')" />
                                </div>

                                <div class="mb-3">
                                    <x-dashboard.input-label for="slug" :value="__('Publish At')" />
                                    <span class="text-back-muted text-sm">*by default immediately</span>
                                    <x-dashboard.text-input class="" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at) }}" placeholder="Click for choose publish datetime" />
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('published_at')" />
                                </div>

                                <div class="mb-3">
                                    <x-dashboard.input-label for="user_id" :value="__('Author')" />
                                    <select class="focus:ring-back-primary focus:border-back-primary dark:focus:border-back-dark-primary block w-full rounded-lg border border-gray-300 bg-gray-50 px-2.5 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-500" id="user_id" name="user_id">
                                        <option value="">-- Select Author --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $post->user_id) == $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                                        @endforeach
                                    </select>
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('user_id')" />
                                </div>

                                <div class="mb-3">
                                    <x-dashboard.input-label for="cover" :value="__('Featured Image')" />
                                    <div class="mb-5 flex w-full items-center justify-center">
                                        <label class="dropzone flex h-24 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600" id="dropzone">
                                            <div class="flex flex-col items-center justify-center pb-6 pt-5">
                                                <svg class="mb-4 h-8 w-8 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, JPEG or WEBP (MAX. 2MB)</p>
                                            </div>
                                            <input class="cover hidden" id="dropzone-file" name="cover" type="file" />
                                        </label>
                                    </div>
                                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('cover')" />
                                    <div class="preview-cover mt-2">
                                        <img src="{{ $post->cover }}" alt="Featured Image" style="width: 300px; height: 200px; object-fit: cover">
                                    </div>
                                </div>
                            </x-card>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata Tab -->
            <div class="hidden" id="styled-metadata" role="tabpanel" aria-labelledby="metadata-tab">
                <x-card>
                    <div class="mb-3">
                        <x-dashboard.input-label for="meta_title" :value="__('Meta Title')" />
                        <span class="text-back-muted text-sm dark:text-gray-400">Use meta title for customing title on browser tab and SEO</span>
                        <x-dashboard.text-input class="mt-1" id="meta_title" name="meta_title" type="text" :value="old('meta_title', $post->meta_title)" placeholder="SEO meta title" />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('meta_title')" />
                    </div>
                    <div class="mb-3">
                        <x-dashboard.input-label for="meta_desc" :value="__('Meta Description')" />
                        <span class="text-back-muted text-sm dark:text-gray-400">Use summary for custom description SEO</span>
                        <x-dashboard.textarea-input id="meta_desc" name="meta_desc" rows="4" placeholder="Write your meta description here...">{{ old('meta_desc', $post->meta_desc) }}</x-dashboard.textarea-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('meta_desc')" />
                    </div>
                    <div class="mb-3">
                        <x-dashboard.input-label for="meta_keywords" :value="__('Meta Keywords')" />
                        <span class="text-back-muted text-sm dark:text-gray-400">Use comma to separate.</span>
                        <x-dashboard.textarea-input id="meta_keywords" name="meta_keywords" rows="4" placeholder="Enter meta keywords. Eg: keyword1, keyword2, keyword abc, keyword xyz">{{ old('meta_keywords', $post->meta_keywords) }}</x-dashboard.textarea-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('meta_keywords')" />
                    </div>
                </x-card>
            </div>

            <x-card class="my-6">

                <x-dashboard.input-label for="cover" :value="__('Content')" />
                <x-dashboard.input-error class="mt-2" :messages="$errors->get('cover')" />
                <div class="main-container w-full">
                    <div class="editor-container editor-container_classic-editor editor-container_include-style editor-container_include-block-toolbar editor-container_include-word-count editor-container_include-fullscreen" id="editor-container">
                        <div class="editor-container__editor">
                            <textarea class="w-full rounded-lg border-gray-300 bg-gray-50" id="post-content" name="content" placeholder="Enter the Description" rows="5">{{ old('content', $post->content ?? '') }}</textarea>
                        </div>
                        <div id="editor-content" style="display: none;">
                            {!! old('content', $post->content ?? '') !!}
                        </div>
                        <div class="editor_container__word-count" id="editor-word-count"></div>
                    </div>
                </div>

            </x-card>

            <div class="space-x-0.5 md:space-x-1.5">
                <x-dashboard.primary-button name="action" value="publish" type="submit">
                    <i class="ri-save-3-line"></i>
                    <span>Save and Publish</span>
                </x-dashboard.primary-button>
                <x-dashboard.secondary-button name="action" value="draft" type="submit">
                    <i class="ri-draft-line"></i>
                    <span>Save As Draft</span>
                </x-dashboard.secondary-button>
            </div>
        </form>

    </section>

    <x-dashboard.ai-panel />

    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
        <link type="text/css" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" />
    @endpush

    @push('javascript')
        @vite('resources/js/ckeditor.js')
        <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>

        <script>
            $(document).ready(function() {
                const date = $("#published_at").flatpickr({
                    altInput: true,
                    altFormat: "F j, Y H:i",
                    dateFormat: "Y-m-d H:i",
                    enableTime: true,
                    allowInput: false,
                    altInputClass: "cursor-pointer focus:ring-back-primary focus:border-back-primary block w-full rounded-lg border border-gray-300 bg-gray-50 px-2.5 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-back-dark-primary dark:focus:ring-blue-500"
                });

                let isSlugEdited = true;
                $("#edit-slug").click(function(e) {
                    const slug = document.getElementById("slug");
                    slug.readOnly = !slug.readOnly;
                    $("#edit-slug").toggleClass("ri-pencil-fill");
                    $("#edit-slug").toggleClass("ri-close-fill");
                })

                const debouncedGenerateSlug = _.debounce(function(slug) {
                    generateSlug(slug);
                }, 500);
                $("#title").on("input", function() {
                    if (!isSlugEdited) {
                        const title = $("#title").val();
                        debouncedGenerateSlug(title);
                    }
                });

                function generateSlug(data) {
                    $.ajax({
                        type: "post",
                        url: `{{ route('admin.posts.generateSlug') }}`,
                        data: {
                            data,
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function(response) {
                            $("#slug").val(response.slug);
                        },
                        error: function(error) {

                        }
                    });
                }

                const existingTagsDB = @json($post->tags->map(fn($tag) => ['id' => $tag->id, 'value' => $tag->tag_name]));
                const whitelistTags = @json($tags->map(fn($tag) => ['id' => $tag->id, 'value' => $tag->tag_name]));

                let tagsInput = document.querySelector('input[name="tags"]');
                let tagify = new Tagify(tagsInput, {
                    whitelist: whitelistTags,
                    enforceWhitelist: false, // Biarkan pengguna memasukkan tag baru
                    maxTags: 10, // Batas jumlah tag
                    dropdown: {
                        maxItems: 20, // Maksimal item yang ditampilkan di dropdown
                        classname: "tags-look",
                        enabled: 0, // Munculkan suggestion saat mengetik
                        closeOnSelect: false
                    },
                    transformTag: function(tagData) {
                        // Cek apakah tag sudah ada
                        var existingTag = tagify.value.find(t => t.value.toLowerCase() === tagData.value.toLowerCase());
                        if (existingTag) {
                            return false;
                        }
                        // Jika tag baru tidak ada dalam whitelist, buat id baru
                        if (!whitelistTags.find(t => t.value.toLowerCase() === tagData.value.toLowerCase())) {
                            tagData.id = whitelistTags.length + 1; // ID otomatis meningkat
                            whitelistTags.push(tagData); // Tambahkan ke whitelist
                        }
                    }
                });
                tagify.on("add", function(e) {
                    console.log("Tag ditambahkan:", e.detail.data);
                });
                tagify.addTags(existingTagsDB);

                const dropzone = $("#dropzone");
                const fileInput = $("#dropzone-file");

                // Fungsi untuk menampilkan preview gambar
                function showPreview(file) {
                    if (file && /^image\//i.test(file.type)) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = $("<img>").attr("src", e.target.result)
                                .css({
                                    width: "300px",
                                    maxHeight: "200px",
                                    objectFit: "cover",
                                    objectPosition: "center"
                                });

                            $(".preview-cover").html(img);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert("Invalid file type. Please select an image.");
                    }
                }

                // Event saat file dipilih melalui input
                fileInput.change(function(event) {
                    const file = event.target.files[0];
                    showPreview(file);
                });

                // Event saat file di-drag ke dalam dropzone
                dropzone.on("dragover", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).addClass("border-blue-500 bg-gray-200 dark:bg-gray-600");
                });

                // Event saat file keluar dari dropzone
                dropzone.on("dragleave", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).removeClass("border-blue-500 bg-gray-200 dark:bg-gray-600");
                });

                // Event saat file di-drop ke dalam dropzone
                dropzone.on("drop", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).removeClass("border-blue-500 bg-gray-200 dark:bg-gray-600");

                    const files = event.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.prop("files", files);
                        showPreview(files[0]);
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                let formChanged = false;
                document.getElementById('post-form').addEventListener('change', () => {
                    if (!formChanged) {
                        formChanged = true;
                    }
                });
                window.addEventListener('beforeunload', function(e) {
                    if (!formChanged) return undefined;
                    // Cancel the event as per the standard.
                    e.preventDefault();
                    // Chrome requires returnValue to be set.
                    e.returnValue = '';
                    return 'Are you sure you want to leave? Changes you made may not be saved.';
                });

                document.getElementById('post-form').addEventListener('submit', function(event) {
                    formChanged = false;
                });
            });
        </script>
    @endpush
</x-app-layout>
