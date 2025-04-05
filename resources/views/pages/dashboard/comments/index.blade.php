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
                            <th>Comment</th>
                            <th>User</th>
                            <th>Article</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $comment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $comment->content }}</td>
                                <td>{{ $comment->user->username }} {{ $comment->user->id == auth()->user()->id ? '(You)' : '' }}</td>
                                <td>{{ $comment->article->title }}</td>
                                <td>{{ $comment->created_at->diffForHumans() }}</td>
                                <td>
                                    <a class="btn bg-back-primary" type="button" href="{{ route('article.show', ['year' => $comment->article->published_at->format('Y'), 'slug' => $comment->article->slug]) . '?source=comments&commentId=zkcomment_0212' . $comment->id }}" target="_blank"><i class="ri-eye-fill"></i></a>
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
                    targets: 5
                }],
            });
        </script>
    @endpush
</x-app-layout>
