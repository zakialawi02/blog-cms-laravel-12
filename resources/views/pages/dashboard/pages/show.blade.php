@section('title', $data['title'] ?? '')
@section('meta_description', '')


<x-app-front-pages-layout>
    @push('css')
        @if ($page->isFullWidth == 1)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    @endpush

    @if ($page->isFullWidth == 1)
        <!-- NAVBAR -->
        <x-headerNav />
    @endif

    <main class="@if ($page->isFullWidth == 1) w-full p-6 md:p-10 @endif">

        <!-- Loader -->
        <div class="lc" id="lspn">
            <div class="spn"></div>
        </div>

        {{-- @dd($page) --}}

        <div id="gjs"></div>
        <div id="gjscss"></div>

        @if ($page->isFullWidth == 1)
            <!-- Footer -->
            <x-footer />
        @endif
    </main>

    @push('javascript')
        <script>
            const escapeName = (name) => `${name}`.trim().replace(/([^a-z0-9\w-:/]+)/gi, '-');
            const projectId = '{{ $page->id }}';
            const loadProjectEndpoint = `{{ url('/dashboard/pages/${projectId}/load-project') }}`;
            const storeProjectEndpoint = `{{ url('/dashboard/pages/${projectId}/store-project') }}`;
        </script>
        <script>
            $.ajax({
                type: "get",
                url: loadProjectEndpoint,
                dataType: "json",
                success: function(response) {
                    const projectData = response.data;
                    // console.log(projectData);

                    const editor = grapesjs.init({
                        headless: true,
                        plugins: [
                            'gjs-blocks-basic',
                            'grapesjs-plugin-forms',
                            'grapesjs-component-countdown',
                            'grapesjs-tabs',
                            'grapesjs-custom-code',
                            'grapesjs-touch',
                            'grapesjs-navbar',
                            'grapesjs-style-gradient',
                            'grapesjs-parser-postcss',
                            'grapesjs-tooltip',
                            'grapesjs-tui-image-editor',
                            'grapesjs-typed',
                            'grapesjs-style-bg',
                            'grapesjs-ui-suggest-classes',
                            'grapesjs-style-filter',
                            'grapesjs-tailwind',
                            'grapesjs-preset-webpage',
                        ],
                    })
                    editor.loadProjectData(projectData);
                    const html = editor.getHtml();
                    const css = editor.getCss();

                    // console.log('html:', html);
                    // console.log('css:', css);
                    $("#gjs").append(html);
                    const style = document.createElement('style');
                    style.type = 'text/css';
                    style.innerHTML = css;
                    document.getElementsByTagName('head')[0].appendChild(style);
                    // $("#gjscss").append(css);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#lspn').remove();
            });
        </script>
    @endpush
</x-app-front-pages-layout>
