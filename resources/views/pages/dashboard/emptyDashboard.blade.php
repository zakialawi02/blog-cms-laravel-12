@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        {{ __("You're logged in!") }}
    </section>
</x-app-layout>
