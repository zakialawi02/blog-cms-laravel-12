@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 pt-3 md:px-4">
        <x-card>
            <div class="mb-0 flex flex-col items-center justify-between gap-4 px-2 align-middle md:mb-3 md:flex-row">
                <div class="">

                </div>

                <div class="">
                    <x-dashboard.primary-button href="{{ route('admin.pages.create') }}">
                        <i class="ri-file-add-line"></i>
                        <span>New Page</span>
                    </x-dashboard.primary-button>
                </div>
            </div>

            <div class="table-container">
                <table class="display table" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Page</th>
                            <th scope="col">Description</th>
                            <th scope="col">Url</th>
                            <th scope="col">Created</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $page->title }}</td>
                                {{-- Added Tailwind CSS classes for max-width and word wrapping --}}
                                <td class="max-w-md break-words">{{ $page->description }}</td>
                                <td>
                                    <a class="hover:text-accent dark:hover:text-dark-accent" href="{{ route('page.show', $page->slug) . '?source=dashboard' }}" target="_blank"><i class="ri-external-link-line"></i>{{ route('page.show', $page->slug) }}</a>
                                </td>
                                <td>{{ $page->updated_at ? $page->updated_at->format('d M Y H:i') : '#' }}</td>
                                <td>
                                    <a class="btn bg-back-neutral zk-build-page" href="{{ route('admin.pages.builder', $page->id) }}"><i class="ri-pencil-ruler-2-line"></i></a>
                                    <a class="btn bg-back-secondary zk-edit-data" href="{{ route('admin.pages.edit', $page->id) }}"><i class="ri-settings-4-line"></i></a>
                                    <form class="zk-delete-data inline" action="{{ route('admin.pages.destroy', $page->id) }}" method="POST">
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
                        targets: 2,
                        className: 'max-w-[350px] overflow-hidden text-ellipsis whitespace-nowrap rtl:text-right'
                    },
                    {
                        orderable: false,
                        targets: 5
                    }
                ],
            });
        </script>
    @endpush
</x-app-layout>
