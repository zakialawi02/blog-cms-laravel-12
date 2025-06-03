@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 pt-3 md:px-4">
        <x-card>
            <form class="my-form-input" id="form-page" action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <x-dashboard.input-label for="title" value="{{ __('Title') }}"></x-dashboard.input-label>
                    <x-dashboard.text-input class="mt-1 block w-full" id="title" name="title" type="text" value="{{ old('title', $page->title) }}" placeholder="Enter title" required autofocus />
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('title')" />
                </div>
                <div class="mb-3">
                    <x-dashboard.input-label for="description" value="{{ __('Description') }}"></x-dashboard.input-label>
                    <x-dashboard.textarea-input class="mt-1 block w-full" id="description" name="description" type="text" rows="5" required autofocus>{{ old('description', $page->description) }}</x-dashboard.textarea-input>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('description')" />
                </div>
                <div class="mb-3">
                    <x-dashboard.input-label for="slug" value="{{ __('Slug/Url page') }}"></x-dashboard.input-label>
                    <div class="relative mt-2.5">
                        <div class="absolute inset-y-0 left-0 flex items-center">
                            <div class="bg-backend-neutral z-10 rounded-l-md border border-r-0 border-gray-300 px-3.5 py-2 shadow-sm">https://domain.com/p/</div>
                        </div>
                        <x-dashboard.text-input class="px-3.5 py-2 pl-48 focus:ring-inset sm:text-sm sm:leading-6" id="slug" name="slug" type="text" value="{{ old('slug', $page->slug) }}" placeholder="enter-slug-here" required="" maxlength="255">
                        </x-dashboard.text-input>
                    </div>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('slug')" />
                </div>
                <div class="mb-3">
                    <x-dashboard.input-label for="template_id" value="{{ __('Template') }}"></x-dashboard.input-label>
                    <div class="flex items-center gap-x-3">
                        <input class="focus:ring-back-primary dark:focus:ring-back-dark-primary focus:border-back-primary dark:focus:border-back-dark-primary h-4 w-4" id="fullWidth" name="template_id" type="radio" value="1" @if ($page->isFullWidth == 1) checked @endif>
                        <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300" for="fullWidth">Full Width</label>
                    </div>
                    <div class="flex items-center gap-x-3">
                        <input class="focus:ring-back-primary dark:focus:ring-back-dark-primary focus:border-back-primary dark:focus:border-back-dark-primary h-4 w-4" id="canvas" name="template_id" type="radio" value="0" @if ($page->isFullWidth == 0) checked @endif>
                        <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300" for="canvas">Canvas</label>
                    </div>
                </div>

                <x-dashboard.primary-button type="submit">
                    save
                </x-dashboard.primary-button>

            </form>
        </x-card>
    </section>

</x-app-layout>
