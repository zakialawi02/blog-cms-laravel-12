@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card>
            <div class="table-container">
                <table class="display table" id="myTable">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Is Subscribed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($newsletters as $row)
                            <tr>
                                <td style="max-width: 480px; white-space: normal; word-break: break-word;">{{ $row->email }}</td>
                                <td>{{ $row->created_at->format('d M Y H:i:s') }}</td>
                                <td>{{ $row->is_subscribed ? 'Yes' : 'No' }}</td>
                                <td>
                                    <form class="zk-delete-data inline" action="{{ route('admin.newsletter.destroy', $row->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn bg-back-error" type="submit"><i class="ri-delete-bin-6-line"></i></button>
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
                autoWidth: false,
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }],
            });
        </script>
    @endpush
</x-app-layout>
