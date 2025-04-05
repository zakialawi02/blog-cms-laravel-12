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
                            <th>Article</th>
                            <th>Comment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $myComment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $myComment->article->title }}</td>
                                <td>{!! $myComment->content !!}</td>
                                <td>
                                    <a class="btn bg-back-primary" type="button" href="{{ route('article.show', ['year' => $myComment->article->published_at->format('Y'), 'slug' => $myComment->article->slug]) . '?source=comments&commentId=zkcomment_0212' . $myComment->id }}" target="_blank"><i class="ri-eye-fill"></i></a>
                                    <form class="inline" action="{{ route('admin.comment.destroy', $myComment->id) }}" method="POST">
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
                    targets: 3
                }],
            });
        </script>
    @endpush
</x-app-layout>
