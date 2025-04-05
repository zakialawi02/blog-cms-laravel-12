@section('title', $data['title'] ?? '')
@section('meta_description', 'Stats of all posts on the zakialawi.my.id website')
@section('meta_author', 'zakialawi')

<x-app-layout>
    <section class="p-1 md:p-4">
        <x-card class="mb-3 px-2 text-xl font-medium">
            <h2><span class="font-bold">{{ __('Title') }}</span>: {{ $article->title }}</h2>
        </x-card>

        <div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
            <div class="">
                <x-card class="mb-3">
                    <div class="table-container overflow-x-auto">
                        <table class="display table" id="myTable2">
                            <tr>
                                <td>{{ __('Title') }}</td>
                                <td width:3px>:</td>
                                <td>{{ $article->title }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Url') }}</td>
                                <td width:3px>:</td>
                                <td><a class="text-back-primary dark:text-back-dark-primary hover:text-back-secondary dark:hover:text-back-secondary hover:underline" href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}" target="_blank">{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}</a></td>
                            </tr>
                            <tr>
                                <td>{{ __('Author') }}</td>
                                <td width:3px>:</td>
                                <td>{{ $article->user->username }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Status') }}</td>
                                <td width:3px>:</td>
                                <td><span class="badge bg-back-{{ $article->status === 'published' ? 'success' : 'secondary' }}">{{ $article->status }}</span></td>
                            </tr>
                            <tr>
                                <td>{{ __('Published at') }}</td>
                                <td width:3px>:</td>
                                <td>{{ $article?->published_at->diffForHumans() ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Created at') }}</td>
                                <td width:3px>:</td>
                                <td>{{ $article?->created_at->diffForHumans() ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Visitors') }}</td>
                                <td width:3px>:</td>
                                <td>{{ $article->total_views }}</td>
                            </tr>
                        </table>
                    </div>
                </x-card>
            </div>
            <div class="">
                <x-card class="mb-3">
                    <div id="stats-map"></div>
                    <pre id="csv"></pre>
                </x-card>
            </div>

            <x-card class="col-start-1 mb-3 md:col-start-2">
                <h4>{{ __('Visitors') }}</h4>
                <table class="table-hover table-striped table" id="myTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('Country') }}</th>
                            <th>{{ __('Country Code') }}</th>
                            <th>{{ __('Visitors') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($views as $view)
                            <tr>
                                <td>{{ $view->location }}</td>
                                <td>{{ $view->code }}</td>
                                <td>{{ $view->views }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    </section>

    @push('javascript')
        <script src="https://code.highcharts.com/maps/highmaps.js"></script>
        <script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/maps/modules/data.js"></script>
        <script src="https://code.highcharts.com/maps/modules/accessibility.js"></script>

        <script>
            const viewsData = @json($views);

            (async () => {

                const topology = await fetch(
                    'https://code.highcharts.com/mapdata/custom/world.topo.json'
                ).then(response => response.json());

                const mapData = viewsData.map(item => ({
                    "hc-key": item.code.toLowerCase(),
                    "value": item.views
                }));

                Highcharts.mapChart('stats-map', {
                    chart: {
                        map: topology
                    },

                    title: {
                        text: 'Visitors by Country',
                        align: 'left'
                    },

                    mapNavigation: {
                        enabled: true,
                        buttonOptions: {
                            verticalAlign: 'bottom'
                        }
                    },

                    colorAxis: {
                        min: 0,
                        type: 'linear',
                    },

                    tooltip: {
                        valueDecimals: 0,

                        valueSuffix: ' visitors'
                    },

                    series: [{
                        data: mapData,
                        name: 'Visitors',
                        allowPointSelect: true,
                        cursor: 'pointer',
                        states: {
                            select: {
                                color: '#a4edba',
                                borderColor: 'gray',
                            },
                            hover: {
                                color: '#BADA55',
                                enabled: true,
                                borderColor: 'gray',
                            },
                        },
                    }]
                });

            })();
        </script>
    @endpush
</x-app-layout>
