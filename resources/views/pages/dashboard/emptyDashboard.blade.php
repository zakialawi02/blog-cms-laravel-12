@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        {{ __("You're logged in!") }}
    </section>

    <section class="p-1 md:px-4">
        <x-card>
            <div class="mb-3">
                <h4 class="mb-0 text-2xl">Coming Soon</h4>
            </div>
            <p>Coming Soon new features</p>
        </x-card>
    </section>
</x-app-layout>
