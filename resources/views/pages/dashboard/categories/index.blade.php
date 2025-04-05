@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card>
            <div class="mb-3 flex items-center justify-end px-2 align-middle">
                <x-dashboard.primary-button href="{{ route('admin.categories.create') }}">
                    <i class="ri-folder-add-line"></i>
                    <span>New Category</span>
                </x-dashboard.primary-button>
            </div>

            <div class="table-container">
                <table class="display table" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $category->category }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->created_at->diffForHumans() }}</td>
                                <td>
                                    <a class="btn bg-back-secondary zk-edit-data" href="{{ route('admin.categories.edit', $category->slug) }}"><i class="ri-pencil-line"></i></a>
                                    <form class="zk-delete-data inline" action="{{ route('admin.categories.destroy', $category->slug) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn bg-back-error zk-delete-data" type="submit"><i class="ri-delete-bin-6-line"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </section>

    @include('components.dependencies._datatables')

    @push('javascript')
        <script>
            let table = new DataTable('#myTable', {
                responsive: true,
                scrollX: true,
                columnDefs: [{
                        orderable: false,
                        targets: 0
                    },
                    {
                        orderable: false,
                        targets: 4
                    }
                ],
            });
        </script>
    @endpush
</x-app-layout>
