<aside class="w-65 dark:bg-back-dark-base-100 z-38 fixed inset-y-0 start-0 h-full -translate-x-full transform border-e border-gray-200 bg-white transition-all duration-300 lg:bottom-0 lg:end-auto lg:block lg:translate-x-0 dark:border-slate-700" id="sidebar-multi-level-sidebar" role="dialog" aria-label="Sidebar" tabindex="-1">
    <div class="relative flex h-full max-h-full flex-col">
        <div class="align-center flex items-center px-5 py-1.5">
            <!-- Logo -->
            <a class="focus:outline-hidden inline-block flex-none rounded-xl py-1 text-xl font-semibold focus:opacity-80" href="#" aria-label="Preline">
                <x-application-logo class="h-auto max-w-28" />
            </a>
            <!-- End Logo -->

            <div class="ms-2 lg:hidden">
                <button class="absolute end-2.5 top-2.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white" data-drawer-hide="sidebar-multi-level-sidebar"aria-controls="sidebar-multi-level-sidebar" type="button">
                    <i class="ri-close-large-line font-semibold"></i>
                    <span class="sr-only">Close menu</span>
                </button>
            </div>
        </div>

        <hr class="h-px border-0 bg-gray-200 dark:bg-slate-700">

        <!-- Content -->
        <div class="dark:[&::-webkit-scrollbar-thumb]:bg-back-neutral-500 dark:[&::-webkit-scrollbar-track]:bg-back-neutral-700 h-full overflow-y-auto [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar]:w-2">
            <nav class="flex w-full flex-col flex-wrap p-3">
                <ul class="flex flex-col space-y-1">
                    <x-dashboard.nav-item route="/" icon="ri-home-4-line" text="Home" />
                    <x-dashboard.nav-item route="admin.dashboard" icon="ri-dashboard-line" text="Dashboard" />
                    @if (Auth::user()->role !== 'user')
                        <x-dashboard.nav-dropdown icon="ri-article-line" text="Articles Posts" :items="[['route' => 'admin.posts.index', 'text' => 'Posts'], ['route' => 'admin.posts.create', 'text' => 'Create Post']]" />
                    @endif
                    @if (Auth::user()->role == 'superadmin')
                        <x-dashboard.nav-dropdown icon="ri-folder-reduce-line" text="Category" :items="[['route' => 'admin.categories.index', 'text' => 'Categories'], ['route' => 'admin.categories.create', 'text' => 'Add Category']]" />
                    @endif
                    @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                        <x-dashboard.nav-dropdown icon="ri-price-tag-3-line" text="Tags" :items="[['route' => 'admin.tags.index', 'text' => 'Tags List'], ['route' => 'admin.tags.create', 'text' => 'Add Tag']]" />
                    @endif
                    @if (Auth::user()->role !== 'user')
                        <x-dashboard.nav-dropdown icon="ri-bar-chart-box-line" text="Statistics Views" :items="[['route' => 'admin.posts.statsview', 'text' => 'Articles Views'], ['route' => 'admin.posts.statslocation', 'text' => 'By Country']]" />
                        <x-dashboard.nav-dropdown icon="ri-discuss-line" text="Comments" :items="[['route' => 'admin.comments.index', 'text' => 'All Comments'], ['route' => 'admin.mycomments.index', 'text' => 'My Comments']]" />
                    @endif
                    @if (Auth::user()->role == 'user')
                        <x-dashboard.nav-dropdown icon="ri-discuss-line" text="Comments" :items="[['route' => 'admin.mycomments.index', 'text' => 'My Comments']]" />
                    @endif
                    <div class="px-1 pt-3 text-sm font-bold text-gray-600 dark:text-gray-200">
                        <h5>Manage</h5>
                    </div>
                    <x-dashboard.nav-item route="#" icon="ri-notification-3-line" text="Notification" />
                    @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                        <x-dashboard.nav-item route="admin.newsletter.index" icon="ri-mail-line" text="Newsletters" />
                        <x-dashboard.nav-item route="admin.requestContributor.index" icon="ri-git-pull-request-line" text="Request Contributor" />
                    @endif
                    @if (Auth::user()->role == 'superadmin')
                        <x-dashboard.nav-item route="admin.users.index" icon="ri-user-line" text="User" />
                        <x-dashboard.nav-item route="admin.settings.web.index" icon="ri-settings-3-line" text="Web Setting" />
                        <x-dashboard.nav-item route="admin.info" icon="ri-information-line" text="System Info" />
                        <x-dashboard.nav-item route="docs" icon="ri-file-list-3-line" text="Route Docs" target="_blank" />
                    @endif

                </ul>
            </nav>
        </div>
        <!-- End Content -->
    </div>
</aside>
