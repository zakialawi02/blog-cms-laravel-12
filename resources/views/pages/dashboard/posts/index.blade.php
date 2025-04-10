@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-3 text-xl font-medium">
            <h1>{{ $data['title'] ?? '' }} <a class="hover:text-accent dark:hover:text-accent" href={{ route('article.index') }} target="_blank"><i class="ri-external-link-line"></i></a></h1>
        </div>
        <x-card>
            <div class="mb-0 flex flex-col items-center justify-between gap-4 px-2 align-middle md:mb-3 md:flex-row">
                <div class="order-2 inline-block w-full self-start md:order-1">
                    <div class="text-lg font-semibold">
                        <p>Filter</p>
                    </div>
                    <div class="flex flex-row gap-1 md:gap-2" id="filter-container">
                        <div class="w-full md:w-auto">
                            <label class="mb-1 block text-sm text-gray-900 dark:text-white" for="status">Status</label>
                            <select class="mb-3 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" id="status" name="status">
                                <option value="all">All</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                                <option value="trash">Trash</option>
                            </select>
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="mb-1 block text-sm text-gray-900 dark:text-white" for="category">Category</label>
                            <select class="mb-3 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" id="category" name="category">
                                <option value="all">All Category</option>
                                <option value="uncategorized">Uncategorized</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}">{{ $category->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (Auth::user()->role == 'superadmin')
                            <div class="w-full md:w-auto">
                                <label class="mb-1 block text-sm text-gray-900 dark:text-white" for="author">Author</label>
                                <select class="mb-3 block w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" id="author" name="author">
                                    <option value="all">All Authors</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->username }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="order-1 min-w-max self-end md:order-2 md:self-auto">
                    <x-dashboard.primary-button href="{{ route('admin.posts.create') }}">
                        <i class="ri-file-add-line"></i>
                        <span>New Post</span>
                    </x-dashboard.primary-button>
                </div>
            </div>

            <div class="table-container">
                <table class="display table" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Category</th>
                            <th style="min-width: 150px max-width: 200px" scope="col">Tags</th>
                            <th scope="col">Status</th>
                            <th scope="col">View</th>
                            <th scope="col">Author</th>
                            <th scope="col">Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ajax datatable -->
                    </tbody>
                </table>
            </div>
        </x-card>
    </section>

    @include('components.dependencies._datatables')
    @push('javascript')
        <script>
            $(document).ready(function() {
                let urlParams = new URLSearchParams(window.location.search);
                let statusParam = urlParams.get('status') || 'all';
                let authorParam = urlParams.get('author') || 'all';
                let categoryParam = urlParams.get('category') || 'all';
                let pageParam = parseInt(urlParams.get('page')) || 1; // Ambil halaman dari URL
                let limitParam = parseInt(urlParams.get('limit')) || 10;

                // Set nilai dropdown berdasarkan URL
                $('#status').val(statusParam);
                $('#author').val(authorParam);
                $('#category').val(categoryParam);

                let table = new DataTable('#myTable', {
                    responsive: true,
                    scrollX: true,
                    processing: true,
                    serverSide: true,
                    displayStart: (pageParam - 1) * limitParam, // Atur posisi awal paging
                    pageLength: limitParam,
                    ajax: {
                        url: "{{ url()->full() }}",
                        data: function(d) {
                            d.status = $('#status').val(); // Filter Status
                            d.author = $('#author').val(); // Filter Author
                            d.category = $('#category').val(); // Filter Category
                        },
                        beforeSend: function() {
                            dt_showLoader("#myTable");
                        },
                        complete: function() {
                            dt_hideLoader();
                        }
                    },
                    lengthMenu: [
                        [10, 15, 25, 50, -1],
                        [10, 15, 25, 50, "All"]
                    ],
                    language: {
                        paginate: {
                            previous: '<i class="ri-arrow-left-s-line"></i>',
                            next: '<i class="ri-arrow-right-s-line"></i>'
                        }
                    },
                    order: [
                        [7, 'desc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'title',
                            name: 'title',
                        },
                        {
                            data: 'category',
                            name: 'category'
                        },
                        {
                            data: 'tags',
                            name: 'tags'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'total_views',
                            name: 'total_views',
                            orderable: true,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return `<span><i class="ri-eye-line"></i> ${data}</span>`;
                            }
                        },
                        {
                            data: 'author',
                            name: 'author'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                });

                // Delete post
                $('body').on('click', '.deletePost', function(e) {
                    e.preventDefault();
                    const slug = $(this).data('slug');
                    const url = `{{ route('admin.posts.destroy', ':slug') }}`.replace(':slug', slug);
                    ZkPopAlert.show({
                        message: "Are you sure you want to delete this post?",
                        confirmText: "Yes, delete it",
                        cancelText: "No, cancel",
                        onConfirm: () => {
                            ajaxRequestPost(url);
                        }
                    });
                });

                // Permanent Delete post
                $('body').on('click', '.permanentlyDeletePost', function(e) {
                    e.preventDefault();
                    const slug = $(this).data('slug');
                    const url = `{{ route('admin.posts.destroy-permanent', ':slug') }}`.replace(':slug', slug);
                    ZkPopAlert.show({
                        message: "Are you sure you want to delete permanently this post?",
                        confirmText: "Yes, delete it",
                        cancelText: "No, cancel",
                        onConfirm: () => {
                            ajaxRequestPost(url);
                        }
                    });
                });

                // Restore post
                $('body').on('click', '.restorePost', function(e) {
                    e.preventDefault();
                    const slug = $(this).data('slug');
                    const url = `{{ route('admin.posts.restore', ':slug') }}`.replace(':slug', slug);
                    ZkPopAlert.show({
                        message: "Are you sure you want to restore this post?",
                        confirmText: "Yes, restore it",
                        cancelText: "No, cancel",
                        confirmClass: "bg-green-600 hover:bg-green-800 text-back-light text-sm px-4 py-2 text-gray-900 border border-green-500 rounded-lg focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100",
                        onConfirm: () => {
                            ajaxRequestPost(url, 'POST');
                        }
                    });
                });

                function ajaxRequestPost(url, type = 'DELETE') {
                    return $.ajax({
                        type: type,
                        url: url,
                        success: function(response) {
                            $('#myTable').DataTable().ajax.reload();
                            MyZkToast.success(response.message);
                        },
                        error: function(error) {
                            console.log(error);
                            MyZkToast.error(`${error.statusText} <br> ${error.responseJSON.message}`)
                        }
                    });
                }

                // Fungsi untuk memperbarui URL dengan parameter baru
                function updateURLParams() {
                    let status = $('#status').val();
                    let author = $('#author').val();
                    let category = $('#category').val();
                    let page = table.page() + 1; // Ambil halaman saat ini (DataTables mulai dari 0)
                    let limit = table.page.len(); // Ambil jumlah data per halaman
                    let url = new URL(window.location.href);
                    url.searchParams.set('status', status);
                    url.searchParams.set('author', author);
                    url.searchParams.set('category', category);
                    url.searchParams.set('page', page);
                    url.searchParams.set('limit', limit);

                    window.history.replaceState({}, '', url);
                }
                // Event listener untuk filter dropdown
                $('#status, #author, #category').on('change', function() {
                    updateURLParams();
                    table.ajax.reload(); // Reload DataTables
                });
                // Event listener untuk paging
                table.on('page.dt', function() {
                    updateURLParams();
                });
                // Event listener tambahan untuk perubahan limit dropdown DataTables
                $('.dt-length select').on('change', function() {
                    updateURLParams();
                });
            });
        </script>
    @endpush
</x-app-layout>
