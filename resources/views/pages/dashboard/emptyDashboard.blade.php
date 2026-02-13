@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="px-1 py-4 md:px-4 md:py-8">
        <x-card class="mx-auto max-w-3xl overflow-hidden border-0 bg-gradient-to-br from-slate-500/10 via-zinc-500/10 to-sky-500/10 p-6 md:p-8">
            <div class="flex flex-col items-center text-center">
                <div class="mb-4 rounded-2xl bg-white/70 p-4 shadow-sm dark:bg-black/20">
                    <i class="ri-dashboard-3-line text-4xl text-back-primary dark:text-back-dark-primary"></i>
                </div>

                <p class="text-sm uppercase tracking-[0.2em] text-back-muted dark:text-back-dark-muted">Dashboard</p>
                <h1 class="mt-2 text-2xl font-semibold md:text-3xl">Your workspace is being prepared</h1>
                <p class="mt-3 max-w-xl text-base text-back-muted dark:text-back-dark-muted">
                    Dashboard access for your current account role is not available yet. If this seems incorrect, please
                    contact the administrator.
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <x-dashboard.light-button href="{{ url('/') }}" :size="'small'">
                        <i class="ri-arrow-left-line"></i>
                        Back to Homepage
                    </x-dashboard.light-button>
                    <x-dashboard.primary-button href="{{ route('admin.profile.edit') }}" :size="'small'">
                        <i class="ri-user-settings-line"></i>
                        Open Profile
                    </x-dashboard.primary-button>
                </div>
            </div>
        </x-card>
    </section>
</x-app-layout>
