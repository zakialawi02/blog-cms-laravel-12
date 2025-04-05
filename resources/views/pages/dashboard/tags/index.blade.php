@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card>
            <div class="mb-3 flex items-center justify-end px-2 align-middle">
                <x-dashboard.primary-button href="{{ route('admin.tags.create') }}">
                    <i class="ri-folder-add-line"></i>
                    <span>New Tag</span>
                </x-dashboard.primary-button>
            </div>

            <div class="table-container">
                <table class="display table" id="myTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tag Name</th>
                            <th>Slug</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tags as $tag)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $tag->tag_name }}</td>
                                <td>{{ $tag->slug }}</td>
                                <td>{{ $tag->created_at->diffForHumans() }}</td>
                                <td>
                                    <a class="btn bg-back-secondary zk-edit-data" href="{{ route('admin.tags.edit', $tag->slug) }}"><i class="ri-pencil-line"></i></a>
                                    <form class="zk-delete-data inline" action="{{ route('admin.tags.destroy', $tag->slug) }}" method="POST">
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
