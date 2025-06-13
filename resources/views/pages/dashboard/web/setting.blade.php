@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card class="mb-3">
            <form method="POST" action="{{ route('admin.settings.web.update') }}" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="align-center mb-0 flex items-end justify-end">
                    <div class="space-x-0.5 md:space-x-1.5">
                        <x-dashboard.primary-button class="text-back-light" type="submit">
                            <i class="ri-save-3-line"></i>
                            <span>{{ __('Save') }}</span>
                        </x-dashboard.primary-button>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="mb-3">
                        <x-dashboard.input-label class="block text-lg font-semibold" for="app_logo" value="{{ __('App Logo') }}" />
                        <span class="text-sm text-gray-500">Logo size must be less than 700 Kb and png format</span>
                        <div class="mt-2 flex items-center gap-x-3">
                            <div class="space-y-4">
                                <x-application-logo class="max-h-20" id="app_logo_preview" />
                            </div>
                            <div class="mt-4 flex text-sm text-gray-600">
                                <x-dashboard.input-label class="block cursor-pointer text-lg font-semibold" for="app_logo">
                                    <span class="bg-back-neutral text-back-light shadow-xs hover:bg-back-neutral/70 focus:outline-hidden rounded-md border border-gray-300 px-2.5 py-1.5 text-center text-sm font-medium transition duration-150 ease-in-out focus:ring-1 focus:ring-indigo-500 focus:ring-offset-1 disabled:opacity-40">
                                        {{ __('Change') }}
                                    </span>
                                    <input class="sr-only" id="app_logo" name="app_logo" type="file" accept="image/png" />
                                </x-dashboard.input-label>
                            </div>
                        </div>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('app_logo')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label class="block text-lg font-semibold" for="favicon" value="{{ __('Favicon') }}" />
                        <span class="text-sm text-gray-500">File size must be ratio 1:1, less than 512x512 pixels, transparent background, and png format</span>
                        <div class="mt-2 flex items-center gap-x-3">
                            <div class="space-y-4">
                                <img class="max-h-12 w-12 max-w-12 rounded-md object-cover" id="favicon_preview" src="{{ asset('assets/app_logo/' . $data['web_setting']['favicon'] ?? 'favicon.png') }}" alt="Favicon">
                            </div>
                            <div class="mt-4 flex text-sm text-gray-600">
                                <x-dashboard.input-label class="block cursor-pointer text-lg font-semibold" for="favicon">
                                    <span class="bg-back-neutral text-back-light shadow-xs hover:bg-back-neutral/70 focus:outline-hidden rounded-md border border-gray-300 px-2.5 py-1.5 text-center text-sm font-medium transition duration-150 ease-in-out focus:ring-1 focus:ring-indigo-500 focus:ring-offset-1 disabled:opacity-40">
                                        {{ __('Change') }}
                                    </span>
                                    <input class="sr-only" id="favicon" name="favicon" type="file" accept="image/png" />
                                </x-dashboard.input-label>
                            </div>
                        </div>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('favicon')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="web_name" value="{{ __('Web Name') }}"></x-dashboard.input-label>
                        <span class="text-sm text-gray-500">Main Title Website, Show in header</span>
                        <x-dashboard.text-input id="web_name" name="web_name" type="text" value="{{ old('web_name', $data['web_setting']['web_name']) }}" placeholder="Web Name" required></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('web_name')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="web_name_variant" value="{{ __('Display Web Name') }}"></x-dashboard.input-label>
                        <span class="text-sm text-gray-500">Display Web Name and Tagline position</span>
                        <div class="mt-3">
                            <fieldset class="inline-flex space-x-2">
                                <div>
                                    <label class="has-checked:border-blue-600 has-checked:ring-1 has-checked:ring-blue-600 flex min-h-20 min-w-44 items-center justify-between gap-4 rounded border border-gray-300 bg-white p-3 text-sm font-medium shadow-sm transition-colors hover:bg-gray-50" for="vars1">
                                        <div class="flex items-center gap-2">
                                            <img class="max-w-22 h-auto" src={{ asset('assets/app_logo/app_logo.png') }} alt="Logo Application">
                                            <p class="text-gray-900">Web Name</p>
                                        </div>
                                        <input class="size-5 border-gray-300" id="vars1" name="web_name_variant" type="radio" value="vars1" {{ $data['web_setting']['web_name_variant'] == '1' ? 'checked' : '' }} />
                                    </label>
                                </div>
                                <div>
                                    <label class="has-checked:border-blue-600 has-checked:ring-1 has-checked:ring-blue-600 flex min-h-20 min-w-44 items-center justify-between gap-4 rounded border border-gray-300 bg-white p-3 text-sm font-medium shadow-sm transition-colors hover:bg-gray-50" for="vars2">
                                        <div>
                                            <img class="max-w-22 h-auto" src={{ asset('assets/app_logo/app_logo.png') }} alt="Logo Application">
                                        </div>
                                        <input class="size-5 border-gray-300" id="vars2" name="web_name_variant" type="radio" value="vars2" {{ $data['web_setting']['web_name_variant'] == '2' ? 'checked' : '' }} />
                                    </label>
                                </div>
                                <div>
                                    <label class="has-checked:border-blue-600 has-checked:ring-1 has-checked:ring-blue-600 flex min-h-20 min-w-44 items-center justify-between gap-4 rounded border border-gray-300 bg-white p-3 text-sm font-medium shadow-sm transition-colors hover:bg-gray-50" for="vars3">
                                        <div>
                                            <img class="max-w-22 h-auto" src={{ asset('assets/app_logo/app_logo.png') }} alt="Logo Application">
                                            <p class="text-gray-900">Tagline</p>
                                        </div>
                                        <input class="size-5 border-gray-300" id="vars3" name="web_name_variant" type="radio" value="vars3" {{ $data['web_setting']['web_name_variant'] == '3' ? 'checked' : '' }} />
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('web_name_variant')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="tagline" value="{{ __('Tagline') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="tagline" type="text" value="{{ old('tagline', $data['web_setting']['tagline']) }}" placeholder="tagline/slogan"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('tagline')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="description" value="{{ __('Description') }}"></x-dashboard.input-label>
                        <x-dashboard.textarea-input name="description" rows="3" placeholder="write your description of website">{{ old('description', $data['web_setting']['description']) }}</x-dashboard.textarea-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="keywords" value="{{ __('Keywords') }}"></x-dashboard.input-label>
                        <x-dashboard.textarea-input name="keywords" rows="3" placeholder="keyword1, keyword2, keyword3,">{{ old('keywords', $data['web_setting']['keywords']) }}</x-dashboard.textarea-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('keywords')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="email" value="{{ __('Email') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="email" type="email" value="{{ old('email', $data['web_setting']['email']) }}" placeholder="mail@example.com"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_fb" value="{{ __('Facebook Url') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_fb" type="url" value="{{ old('link_fb', $data['web_setting']['link_fb']) }}" placeholder="https://www.facebook.com/username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_fb')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_ig" value="{{ __('Instagram Url') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_ig" type="url" value="{{ old('link_ig', $data['web_setting']['link_ig']) }}" placeholder="https://www.instagram.com/username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_ig')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_tiktok" value="{{ __('Tiktok Url') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_tiktok" type="url" value="{{ old('link_tiktok', $data['web_setting']['link_tiktok']) }}" placeholder="https://www.tiktok.com/@username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_tiktok')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_youtube" value="{{ __('Youtube Url') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_youtube" type="url" value="{{ old('link_youtube', $data['web_setting']['link_youtube']) }}" placeholder="https://www.youtube.com/channel/username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_youtube')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_twitter" value="{{ __('Twitter Url / X') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_twitter" type="url" value="{{ old('link_twitter', $data['web_setting']['link_twitter']) }}" placeholder="https://twitter.com/username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_twitter')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_linkedin" value="{{ __('Linkedin Url') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_linkedin" type="url" value="{{ old('link_linkedin', $data['web_setting']['link_linkedin']) }}" placeholder="https://www.linkedin.com/in/username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_linkedin')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="link_github" value="{{ __('Github Url') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="link_github" type="url" value="{{ old('link_github', $data['web_setting']['link_github']) }}" placeholder="https://github.com/username"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('link_github')" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="can_join_contributor" value="{{ __('Open For Contributor') }}"></x-dashboard.input-label>
                        <label class="inline-flex cursor-pointer items-center">
                            <input class="peer sr-only" name="can_join_contributor" type="checkbox" {{ $data['web_setting']['can_join_contributor'] == 1 ? 'checked' : '' }}>
                            <div class="peer-checked:bg-back-primary peer-focus:ring-back-primary/80 dark:peer-checked:bg-back-dark-primary dark:peer-focus:ring-back-primary peer relative h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 rtl:peer-checked:after:-translate-x-full dark:border-gray-600 dark:bg-gray-700"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                        </label>
                    </div>

                    <div class="mb-3">
                        <x-dashboard.input-label for="google_adsense" value="{{ __('Google Adsense') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="google_adsense" type="text" value="{{ old('google_adsense', $data['web_setting']['google_adsense'] ?? '') }}" placeholder="Adsense Publisher ID eg: ca-pub-123456789"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('google_adsense')" />
                    </div>
                    <div class="mb-3">
                        <x-dashboard.input-label for="google_analytics" value="{{ __('Google Analytics') }}"></x-dashboard.input-label>
                        <x-dashboard.text-input name="google_analytics" type="text" value="{{ old('google_analytics', $data['web_setting']['google_analytics'] ?? '') }}" placeholder="Google Analytics ID eg: UA-123456789-1"></x-dashboard.text-input>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('google_analytics')" />
                    </div>
                </div>
            </form>
        </x-card>
    </section>

    @push('javascript')
        <script>
            $(document).ready(function() {
                function previewImage(input, imgId) {
                    if (input.files && input.files[0]) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById(imgId).src = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                document.getElementById('app_logo').addEventListener('change', function() {
                    previewImage(this, 'app_logo_preview');
                });

                document.getElementById('favicon').addEventListener('change', function() {
                    previewImage(this, 'favicon_preview');
                });
            });
        </script>
    @endpush
</x-app-layout>
