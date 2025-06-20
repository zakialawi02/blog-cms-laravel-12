@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card>
            <form class="my-form-input" id="form-category" action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <x-dashboard.input-label for="category" :value="__('Category Name')" />
                    <x-dashboard.text-input class="mt-1" id="category" name="category" type="text" :value="old('category')" placeholder="Category" required autofocus />
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('category')" />
                </div>

                <div class="mb-3">
                    <x-dashboard.input-label for="slug" :value="__('Category Slug / url')" />
                    <div class="relative">
                        <x-dashboard.text-input class="mt-1" id="slug" name="slug" type="text" :value="old('slug')" placeholder="Slug-category" readonly required />
                        <x-dashboard.secondary-button class="text-back-light ri-pencil-fill absolute bottom-0.5 end-2" id="edit-slug" type="button">
                        </x-dashboard.secondary-button>
                    </div>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('slug')" />
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

                $('#category').on('input', function(e) {
                    if (!isSlugEdited) {
                        const category = $("#category").val();
                        generateSlug(category);
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
