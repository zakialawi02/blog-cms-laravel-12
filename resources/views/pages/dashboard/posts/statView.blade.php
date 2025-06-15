@section('title', $data['title'] ?? '')
@section('meta_description', 'Stats of all posts on the zakialawi.my.id website')
@section('meta_author', 'zakialawi')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-4 px-1 text-2xl font-medium">
            <h2>{{ $data['title'] ?? '' }}</h2>
        </div>

        <x-card class="mb-3" id="coloum-chart">
            <div class="flex h-full w-full items-center justify-center">
                <div class="mx-auto" role="status">
                    <svg class="h-8 w-8 animate-spin fill-blue-600 text-gray-200 dark:text-gray-600" aria-hidden="true" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </x-card>

        <x-card class="mb-3">
            <div class="mb-2 border-b border-gray-300 dark:border-gray-500">
                <ul class="-mb-px flex flex-wrap text-center font-medium" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-recent" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block rounded-t-lg border-b-2 p-4" id="recent-styled-tab" data-tabs-target="#styled-recent" type="button" role="tab" aria-controls="recent" aria-selected="false">
                            <i class="ri-time-line"></i>
                            <span>{{ __('Recent') }}</span>
                        </button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300" id="popular-styled-tab" data-tabs-target="#styled-popular" type="button" role="tab" aria-controls="popular" aria-selected="false">
                            <i class="ri-fire-line"></i>
                            <span>{{ __('Popular') }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="mb-3" id="default-styled-tab-recent">
                <!-- Panel 1 -->
                <div class="hidden" id="styled-recent" role="tabpanel" aria-labelledby="recent-tab">
                    <div class="table-container">
                        <table class="display table" id="myTable2">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Date Time') }}</th>
                                    <th scope="col">{{ __('Article') }}</th>
                                    <th scope="col">{{ __('OS') }}</th>
                                    <th scope="col">{{ __('Ip') }}</th>
                                    <th scope="col">{{ __('Location') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- server side --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panel 2 -->
                <div class="hidden" id="styled-popular" role="tabpanel" aria-labelledby="popular-tab">
                    <div class="table-container">
                        <table class="display table" id="myTable">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Article') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col">{{ __('Published') }}</th>
                                    <th scope="col">{{ __('Visitors') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- server side --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </x-card>


    </section>

    @include('components.dependencies._datatables')

    @push('javascript')
        <script src="https://code.highcharts.com/stock/highstock.js"></script>
        <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/stock/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/stock/modules/accessibility.js"></script>

        <script>
            (async () => {

                const data = await fetch(
                    '{{ route('admin.posts.statslast6months') }}'
                ).then(response => response.json());
                const mapData = data.map(item => [item.timestamp * 1000, item.view_count]);

                // create the chart
                Highcharts.stockChart('coloum-chart', {
                    chart: {
                        alignTicks: false
                    },

                    rangeSelector: {
                        selected: 0
                    },

                    title: {
                        text: 'Visitor Statistics'
                    },

                    series: [{
                        type: 'column',
                        name: 'Visitor Statistics',
                        data: mapData,
                        dataGrouping: {
                            units: [
                                [
                                    'day',
                                    [1]
                                ],
                                [
                                    'month',
                                    [1, 2, 3]
                                ],
                            ]
                        }
                    }]
                });
            })();

            let table = new DataTable('#myTable', {
                searching: false,
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                ajax: {
                    url: "{{ url()->current() }}?type=popular",
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
                    [3, 'desc']
                ],
                columns: [{
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'published_at',
                        name: 'published_at'
                    },
                    {
                        data: 'total_views',
                        name: 'total_views'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            })

            let table2 = new DataTable('#myTable2', {
                searching: false,
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                ajax: {
                    url: "{{ url()->current() }}?type=recent",
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
                    [1, 'asc']
                ],
                columns: [{
                        data: 'viewed_at',
                        name: 'viewed_at'
                    },
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, row) {
                            let url = "{{ route('admin.posts.statsdetail', ':slug') }}";
                            url = url.replace(':slug', data.slug);
                            return `<a href="${url}" class="text-back-secondary hover:underline hover:text-back-secondary/70 dark:text-back-light dark:hover:text-back-light/70">${data.title}</a>`;
                        }
                    },
                    {
                        data: 'operating_system',
                        name: 'operating_system'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                ],
            })
        </script>
    @endpush
</x-app-layout>
