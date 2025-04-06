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
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Code</th>
                            <th>Requested At</th>
                            <th>Valid</th>
                            <th>Is Confirmed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($query as $query)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $query->user->username }}</td>
                                <td>{{ $query->user->email }}</td>
                                <td>{{ $query->code }}</td>
                                <td>{{ $query->created_at->format('d M Y H:i:s') }}</td>
                                <td>{{ $query->valid_code_until->format('d M Y H:i:s') }}</td>
                                <td>{{ $query->is_confirmed ? 'Yes' : 'No' }}</td>
                                <td>
                                    <form class="inline" action="{{ route('admin.requestsContributors') }}?resend={{ $query->user_id }}" method="POST">
                                        @csrf
                                        <button class="btn bg-back-neutral dark:bg-back-dark-neutral" type="submit" title="Send Email Again"><i class="ri-send-plane-2-line"></i></button>
                                    </form>
                                    <form class="zk-delete-data inline" action="{{ route('admin.requestContributor.destroy', $query->id) }}" method="POST">
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
                columnDefs: [{
                    orderable: false,
                    targets: 7
                }],
            });
        </script>
    @endpush
</x-app-layout>
