@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card>
            <form class="my-form-input" id="form-tag" action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <x-input-label for="tag_name" :value="__('Tag Name')" />
                    <x-text-input class="mt-1" id="tag_name" name="tag_name" type="text" :value="old('tag_name')" placeholder="tag name" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('tag_name')" />
                </div>

                <div class="mb-3">
                    <x-input-label for="slug" :value="__('Tag Slug / url')" />
                    <div class="relative">
                        <x-text-input class="mt-1" id="slug" name="slug" type="text" :value="old('slug')" placeholder="Slug-tag" readonly required />
                        <x-dashboard.secondary-button class="text-back-light ri-pencil-fill absolute bottom-0.5 end-2" id="edit-slug" type="button">
                        </x-dashboard.secondary-button>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                </div>


                <div class="my-3">
                    <x-dashboard.primary-button type="submit">
                        Save
                    </x-dashboard.primary-button>
                </div>
            </form>
        </x-card>
    </section>

    @push('javascript')
        <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

        <script>
            $(document).ready(function() {
                let isSlugEdited = false;
                $("#edit-slug").click(function(e) {
                    const slug = document.getElementById("slug");
                    slug.readOnly = !slug.readOnly;
                    $("#edit-slug").toggleClass("ri-pencil-fill");
                    $("#edit-slug").toggleClass("ri-close-fill");
                    isSlugEdited = true;
                })

                $('#tag_name').on('input', function(e) {
                    if (!isSlugEdited) {
                        const tag_name = $("#tag_name").val();
                        generateSlug(tag_name);
                    }
                });

                function generateSlug(value) {
                    var slug = _.kebabCase(value);
                    $('#slug').val(slug);
                }
            });
        </script>
    @endpush
</x-app-layout>
