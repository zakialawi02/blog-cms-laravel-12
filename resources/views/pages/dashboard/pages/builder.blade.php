<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link type="image/png" href="{{ asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')) }}" rel="icon">

        <meta name="robots" content="noindex, nofollow">

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <!-- grapesjs -->
        <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
        <script src="https://unpkg.com/grapesjs"></script>
        <script src="https://unpkg.com/grapesjs-blocks-basic"></script>
        <script src="https://unpkg.com/grapesjs-blocks-flexbox"></script>
        <script src="https://unpkg.com/grapesjs-navbar"></script>
        <script src="https://unpkg.com/grapesjs-style-gradient"></script>
        <script src="https://unpkg.com/grapesjs-component-countdown"></script>
        <script src="https://unpkg.com/grapesjs-plugin-forms"></script>
        <script src="https://unpkg.com/grapesjs-style-filter"></script>
        <script src="https://unpkg.com/grapesjs-tabs"></script>
        <script src="https://unpkg.com/grapesjs-tooltip"></script>
        <script src="https://unpkg.com/grapesjs-custom-code"></script>
        <script src="https://unpkg.com/grapesjs-touch"></script>
        <script src="https://unpkg.com/grapesjs-parser-postcss"></script>
        <script src="https://unpkg.com/grapesjs-typed"></script>
        <script src="https://unpkg.com/grapesjs-style-bg"></script>
        <script src="https://unpkg.com/grapesjs-tui-image-editor"></script>
        <script src="https://unpkg.com/grapesjs-ui-suggest-classes"></script>
        <script src="https://unpkg.com/grapesjs-ga"></script>
        <script src="https://unpkg.com/grapesjs-component-twitch"></script>
        <script src="https://unpkg.com/grapesjs-user-blocks"></script>
        <script src="https://unpkg.com/grapesjs-chartjs-plugin"></script>
        <script src="https://unpkg.com/grapesjs-tailwindcss-plugin"></script>
        {{-- <script src="https://unpkg.com/grapesjs-tailwind"></script> --}}
        <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>

        <style>
            body,
            html {
                margin: 0;
                height: 100%;
            }

            .change-theme-button {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                margin: 5px;
            }

            .change-theme-button:focus {
                /* background-color: yellow; */
                outline: none;
                box-shadow: 0 0 0 2pt #c5c5c575;
            }

            .lc {
                display: flex;
                justify-content: center;
                align-items: center;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgb(232, 232, 232);
                z-index: 9999;
            }

            .spn {
                width: 50px;
                padding: 8px;
                aspect-ratio: 1;
                border-radius: 50%;
                background: #196cca;
                --_m:
                    conic-gradient(#0000 10%, #000),
                    linear-gradient(#000 0 0) content-box;
                -webkit-mask: var(--_m);
                mask: var(--_m);
                -webkit-mask-composite: source-out;
                mask-composite: subtract;
                animation: s3 1s infinite linear;
            }

            @keyframes s3 {
                to {
                    transform: rotate(1turn)
                }
            }
        </style>

        <!-- Scripts -->
        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <title>{{ $page->title }} • Page Builder | {{ $data['web_setting']['web_name'] ?? config('app.name') }}</title>
    </head>

    <body>
        <div id="unsupported-device" style="display: none; justify-content: center; align-items: center; text-align: center; height: 100vh; background-color: #f3f4f6;">
            <div>
                <h1 style="font-size: 24px; font-weight: bold; color: #1f2937;">Editor Not Supported</h1>
                <p style="font-size: 16px; color: #4b5563; margin-top: 10px;">For the best experience, please open this page on a desktop or tablet device with a wider screen.</p>
            </div>
        </div>

        <!-- Loader -->
        <div class="lc" id="lspn">
            <div class="spn"></div>
        </div>

        <div id="gjs">

        </div>
        <div id="blocks"></div>

        <style>
            /* Theming */

            /* Primary color for the background */
            .gjs-one-bg {
                background-color: #404040;
            }

            /* Secondary color for the text color */
            .gjs-two-color {
                color: rgba(255, 255, 255, 0.907);
            }

            /* Tertiary color for the background */
            .gjs-three-bg {
                background-color: #4b4b4b;
                color: white;
            }

            /* Quaternary color for the text color */
            .gjs-four-color,
            .gjs-four-color-h:hover {
                color: #616985;
            }
        </style>

        <script type="text/javascript">
            const escapeName = (name) => `${name}`.trim().replace(/([^a-z0-9\w-:/]+)/gi, '-');
            const projectId = '{{ $page->id }}';
            const loadProjectEndpoint = `{{ url('/dashboard/pages/${projectId}/load-project') }}`;
            const storeProjectEndpoint = `{{ url('/dashboard/pages/${projectId}/store-project') }}`;

            const username = `{{ Auth::user()->username }}`;
            var lp = `/storage/app/drive/${username}/img/`;
            var plp = 'https://via.placeholder.com/350x250/';
            var images = [
                plp + '78c5d6/fff',
                plp + '459ba8/fff',
                plp + '79c267/fff',
                plp + 'c5d647/fff',
                plp + 'f28c33/fff',
                plp + 'e868a2/fff',
                plp + 'cc4360/fff',
                lp + 'work-desk.jpg',
                lp + 'phone-app.png',
                lp + 'bg-gr-v.png'
            ];

            const editor = grapesjs.init({
                allowScripts: 1,
                container: '#gjs',
                height: '100%',
                fromElement: true,
                selectorManager: {
                    componentFirst: true,
                    escapeName,
                },
                showOffsets: true,
                assetManager: {
                    embedAsBase64: true,
                    assets: images,
                },
                storageManager: {
                    type: 'remote',
                    stepsBeforeSave: 1,
                    options: {
                        remote: {
                            urlLoad: loadProjectEndpoint,
                            urlStore: storeProjectEndpoint,
                            fetchOptions: opts => (opts.method === 'POST' ? {
                                method: 'PATCH'
                            } : {}),
                            onStore: data => ({
                                _token: '{{ csrf_token() }}',
                                id: projectId,
                                data
                            }),
                            onLoad: result => result.data,
                        }
                    }
                },
                plugins: [
                    'gjs-blocks-basic',
                    'grapesjs-plugin-forms',
                    'grapesjs-blocks-flexbox',
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
                    'grapesjs-user-blocks',
                    'grapesjs-ga',
                    'grapesjs-component-twitch',
                    'grapesjs-chartjs-plugin',
                    'grapesjs-tailwindcss-plugin',
                    // 'grapesjs-tailwind',
                    'grapesjs-preset-webpage',
                ],
                pluginsOpts: {
                    'gjs-blocks-basic': {
                        flexGrid: true
                    },
                    'grapesjs-tui-image-editor': {
                        config: {
                            includeUI: {
                                initMenu: 'filter',
                            },
                        },
                    },
                    'grapesjs-tabs': {
                        tabsBlock: {
                            category: 'Extra'
                        }
                    },
                    'grapesjs-typed': {
                        block: {
                            category: 'Extra',
                            content: {
                                type: 'typed',
                                'type-speed': 100,
                                strings: [
                                    'Text row one',
                                    'Text row two',
                                    'Text row three',
                                ],
                            }
                        }
                    },
                    'grapesjs-preset-webpage': {
                        modalImportTitle: 'Import Template',
                        modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                        modalImportContent: function(editor) {
                            return editor.getHtml() + '<style>' + editor.getCss() + '</style>'
                        },
                    },
                    'grapesjs-tailwindcss-plugin': {
                        // Options like autobuild, toolbarPanel, notificationCallback, buildButton, etc.
                    },
                },
                styleManager: {
                    sectors: [{
                            name: 'General',
                            properties: [{
                                    extend: 'float',
                                    type: 'radio',
                                    default: 'none',
                                    options: [{
                                            value: 'none',
                                            className: 'fa fa-times'
                                        },
                                        {
                                            value: 'left',
                                            className: 'fa fa-align-left'
                                        },
                                        {
                                            value: 'right',
                                            className: 'fa fa-align-right'
                                        }
                                    ],
                                },
                                'display',
                                {
                                    extend: 'position',
                                    type: 'select'
                                },
                                'top',
                                'right',
                                'left',
                                'bottom',
                            ],
                        }, {
                            name: 'Dimension',
                            open: false,
                            properties: [
                                'width',
                                {
                                    id: 'flex-width',
                                    type: 'integer',
                                    name: 'Width',
                                    units: ['px', '%'],
                                    property: 'flex-basis',
                                    toRequire: 1,
                                },
                                'height',
                                'max-width',
                                'min-height',
                                'margin',
                                'padding'
                            ],
                        }, {
                            name: 'Typography',
                            open: false,
                            properties: [
                                'font-family',
                                'font-size',
                                'font-weight',
                                'letter-spacing',
                                'color',
                                'line-height',
                                {
                                    extend: 'text-align',
                                    options: [{
                                            id: 'left',
                                            label: 'Left',
                                            className: 'fa fa-align-left'
                                        },
                                        {
                                            id: 'center',
                                            label: 'Center',
                                            className: 'fa fa-align-center'
                                        },
                                        {
                                            id: 'right',
                                            label: 'Right',
                                            className: 'fa fa-align-right'
                                        },
                                        {
                                            id: 'justify',
                                            label: 'Justify',
                                            className: 'fa fa-align-justify'
                                        }
                                    ],
                                },
                                {
                                    property: 'text-decoration',
                                    type: 'radio',
                                    default: 'none',
                                    options: [{
                                            id: 'none',
                                            label: 'None',
                                            className: 'fa fa-times'
                                        },
                                        {
                                            id: 'underline',
                                            label: 'underline',
                                            className: 'fa fa-underline'
                                        },
                                        {
                                            id: 'line-through',
                                            label: 'Line-through',
                                            className: 'fa fa-strikethrough'
                                        }
                                    ],
                                },
                                'text-shadow'
                            ],
                        }, {
                            name: 'Decorations',
                            open: false,
                            properties: [
                                'opacity',
                                'border-radius',
                                'border',
                                'box-shadow',
                                'background', // { id: 'background-bg', property: 'background', type: 'bg' }
                            ],
                        }, {
                            name: 'Extra',
                            open: false,
                            buildProps: [
                                'transition',
                                'perspective',
                                'transform'
                            ],
                        }, {
                            name: 'Flex',
                            open: false,
                            properties: [{
                                name: 'Flex Container',
                                property: 'display',
                                type: 'select',
                                defaults: 'block',
                                list: [{
                                        value: 'block',
                                        name: 'Disable'
                                    },
                                    {
                                        value: 'flex',
                                        name: 'Enable'
                                    }
                                ],
                            }, {
                                name: 'Flex Parent',
                                property: 'label-parent-flex',
                                type: 'integer',
                            }, {
                                name: 'Direction',
                                property: 'flex-direction',
                                type: 'radio',
                                defaults: 'row',
                                list: [{
                                    value: 'row',
                                    name: 'Row',
                                    className: 'icons-flex icon-dir-row',
                                    title: 'Row',
                                }, {
                                    value: 'row-reverse',
                                    name: 'Row reverse',
                                    className: 'icons-flex icon-dir-row-rev',
                                    title: 'Row reverse',
                                }, {
                                    value: 'column',
                                    name: 'Column',
                                    title: 'Column',
                                    className: 'icons-flex icon-dir-col',
                                }, {
                                    value: 'column-reverse',
                                    name: 'Column reverse',
                                    title: 'Column reverse',
                                    className: 'icons-flex icon-dir-col-rev',
                                }],
                            }, {
                                name: 'Justify',
                                property: 'justify-content',
                                type: 'radio',
                                defaults: 'flex-start',
                                list: [{
                                    value: 'flex-start',
                                    className: 'icons-flex icon-just-start',
                                    title: 'Start',
                                }, {
                                    value: 'flex-end',
                                    title: 'End',
                                    className: 'icons-flex icon-just-end',
                                }, {
                                    value: 'space-between',
                                    title: 'Space between',
                                    className: 'icons-flex icon-just-sp-bet',
                                }, {
                                    value: 'space-around',
                                    title: 'Space around',
                                    className: 'icons-flex icon-just-sp-ar',
                                }, {
                                    value: 'center',
                                    title: 'Center',
                                    className: 'icons-flex icon-just-sp-cent',
                                }],
                            }, {
                                name: 'Align',
                                property: 'align-items',
                                type: 'radio',
                                defaults: 'center',
                                list: [{
                                    value: 'flex-start',
                                    title: 'Start',
                                    className: 'icons-flex icon-al-start',
                                }, {
                                    value: 'flex-end',
                                    title: 'End',
                                    className: 'icons-flex icon-al-end',
                                }, {
                                    value: 'stretch',
                                    title: 'Stretch',
                                    className: 'icons-flex icon-al-str',
                                }, {
                                    value: 'center',
                                    title: 'Center',
                                    className: 'icons-flex icon-al-center',
                                }],
                            }, {
                                name: 'Flex Children',
                                property: 'label-parent-flex',
                                type: 'integer',
                            }, {
                                name: 'Order',
                                property: 'order',
                                type: 'integer',
                                defaults: 0,
                                min: 0
                            }, {
                                name: 'Flex',
                                property: 'flex',
                                type: 'composite',
                                properties: [{
                                    name: 'Grow',
                                    property: 'flex-grow',
                                    type: 'integer',
                                    defaults: 0,
                                    min: 0
                                }, {
                                    name: 'Shrink',
                                    property: 'flex-shrink',
                                    type: 'integer',
                                    defaults: 0,
                                    min: 0
                                }, {
                                    name: 'Basis',
                                    property: 'flex-basis',
                                    type: 'integer',
                                    units: ['px', '%', ''],
                                    unit: '',
                                    defaults: 'auto',
                                }],
                            }, {
                                name: 'Align',
                                property: 'align-self',
                                type: 'radio',
                                defaults: 'auto',
                                list: [{
                                    value: 'auto',
                                    name: 'Auto',
                                }, {
                                    value: 'flex-start',
                                    title: 'Start',
                                    className: 'icons-flex icon-al-start',
                                }, {
                                    value: 'flex-end',
                                    title: 'End',
                                    className: 'icons-flex icon-al-end',
                                }, {
                                    value: 'stretch',
                                    title: 'Stretch',
                                    className: 'icons-flex icon-al-str',
                                }, {
                                    value: 'center',
                                    title: 'Center',
                                    className: 'icons-flex icon-al-center',
                                }],
                            }]
                        },
                        // ...
                        {
                            id: 'extra',
                            name: 'Extra',
                            properties: [{
                                    extend: 'filter'
                                },
                                {
                                    extend: 'filter',
                                    property: 'backdrop-filter'
                                },
                            ],
                        }
                    ]
                },
            });

            // Data konfigurasi untuk semua komponen dan blok kustom Anda
            const customComponentsData = [{
                    typePrefix: 'hero',
                    id: 'zkpb-001',
                    label: 'Hero 01',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'hero',
                    id: 'zkpb-002',
                    label: 'Hero 02',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'hero',
                    id: 'zkpb-003',
                    label: 'Hero 03',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'hero',
                    id: 'zkpb-004',
                    label: 'Hero 04',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'hero',
                    id: 'zkpb-005',
                    label: 'Hero 05',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'hero',
                    id: 'zkpb-006',
                    label: 'Hero 06',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'hero',
                    id: 'zkpb-007',
                    label: 'Hero 07',
                    category: 'Hero Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-001',
                    label: 'Content 01',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-002',
                    label: 'Content 02',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-003',
                    label: 'Content 03',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-004',
                    label: 'Content 04',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-005',
                    label: 'Content 05',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-006',
                    label: 'Content 06',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-007',
                    label: 'Content 07',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-008',
                    label: 'Content 08',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-009',
                    label: 'Content 09',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'content',
                    id: 'zkpb-010',
                    label: 'Content 10',
                    category: 'Content Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'faq',
                    id: 'zkpb-001',
                    label: 'FaQ 01',
                    category: 'FAQ Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'faq',
                    id: 'zkpb-002',
                    label: 'FaQ 02',
                    category: 'FAQ Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'faq',
                    id: 'zkpb-003',
                    label: 'FaQ 03',
                    category: 'FAQ Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'faq',
                    id: 'zkpb-004',
                    label: 'FaQ 04',
                    category: 'FAQ Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'testimonial',
                    id: 'zkpb-001',
                    label: 'Testimonial 01',
                    category: 'Testimonial Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'testimonial',
                    id: 'zkpb-002',
                    label: 'Testimonial 02',
                    category: 'Testimonial Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'testimonial',
                    id: 'zkpb-003',
                    label: 'Testimonial 03',
                    category: 'Testimonial Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'testimonial',
                    id: 'zkpb-004',
                    label: 'Testimonial 04',
                    category: 'Testimonial Section',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'full-page',
                    id: 'zkpb-001',
                    label: 'Full Page 01',
                    category: 'Full Page',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'full-page',
                    id: 'zkpb-002',
                    label: 'Full Page 02',
                    category: 'Full Page',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'full-page',
                    id: 'zkpb-003',
                    label: 'Full Page 03',
                    category: 'Full Page',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
                {
                    typePrefix: 'full-page',
                    id: 'zkpb-004',
                    label: 'Full Page 04',
                    category: 'Full Page',
                    mediaThumb: `<svg style="width:70px;height:70px" viewBox="0 0 24 24"><path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" /></svg>`,
                },
            ];

            const bladeComponentsHtml = {
                'hero-zkpb-001': `{!! view('components.grapesjs.hero-zkpb-001')->render() !!}`,
                'hero-zkpb-002': `{!! view('components.grapesjs.hero-zkpb-002')->render() !!}`,
                'hero-zkpb-003': `{!! view('components.grapesjs.hero-zkpb-003')->render() !!}`,
                'hero-zkpb-004': `{!! view('components.grapesjs.hero-zkpb-004')->render() !!}`,
                'hero-zkpb-005': `{!! view('components.grapesjs.hero-zkpb-005')->render() !!}`,
                'hero-zkpb-006': `{!! view('components.grapesjs.hero-zkpb-006')->render() !!}`,
                'hero-zkpb-007': `{!! view('components.grapesjs.hero-zkpb-007')->render() !!}`,
                'content-zkpb-001': `{!! view('components.grapesjs.content-zkpb-001')->render() !!}`,
                'content-zkpb-002': `{!! view('components.grapesjs.content-zkpb-002')->render() !!}`,
                'content-zkpb-003': `{!! view('components.grapesjs.content-zkpb-003')->render() !!}`,
                'content-zkpb-004': `{!! view('components.grapesjs.content-zkpb-004')->render() !!}`,
                'content-zkpb-005': `{!! view('components.grapesjs.content-zkpb-005')->render() !!}`,
                'content-zkpb-006': `{!! view('components.grapesjs.content-zkpb-006')->render() !!}`,
                'content-zkpb-007': `{!! view('components.grapesjs.content-zkpb-007')->render() !!}`,
                'content-zkpb-008': `{!! view('components.grapesjs.content-zkpb-008')->render() !!}`,
                'content-zkpb-009': `{!! view('components.grapesjs.content-zkpb-009')->render() !!}`,
                'content-zkpb-010': `{!! view('components.grapesjs.content-zkpb-010')->render() !!}`,
                'faq-zkpb-001': `{!! view('components.grapesjs.faq-zkpb-001')->render() !!}`,
                'faq-zkpb-002': `{!! view('components.grapesjs.faq-zkpb-002')->render() !!}`,
                'faq-zkpb-003': `{!! view('components.grapesjs.faq-zkpb-003')->render() !!}`,
                'faq-zkpb-004': `{!! view('components.grapesjs.faq-zkpb-004')->render() !!}`,
                'testimonial-zkpb-001': `{!! view('components.grapesjs.testimonial-zkpb-001')->render() !!}`,
                'testimonial-zkpb-002': `{!! view('components.grapesjs.testimonial-zkpb-002')->render() !!}`,
                'testimonial-zkpb-003': `{!! view('components.grapesjs.testimonial-zkpb-003')->render() !!}`,
                'testimonial-zkpb-004': `{!! view('components.grapesjs.testimonial-zkpb-004')->render() !!}`,
                'full-page-zkpb-001': `{!! view('components.grapesjs.full-page-zkpb-001')->render() !!}`,
                'full-page-zkpb-002': `{!! view('components.grapesjs.full-page-zkpb-002')->render() !!}`,
                'full-page-zkpb-003': `{!! view('components.grapesjs.full-page-zkpb-003')->render() !!}`,
                'full-page-zkpb-004': `{!! view('components.grapesjs.full-page-zkpb-004')->render() !!}`,
            };

            // Iterasi melalui array untuk mendaftarkan semua komponen dan blok
            customComponentsData.forEach(config => {
                registerCustomComponent(editor, config);
            });

            // Fungsi untuk mendaftarkan komponen GrapesJS dan bloknya (sedikit dimodifikasi)
            function registerCustomComponent(editor, config) {
                const componentType = `${config.typePrefix}-${config.id}`;
                const blockId = `${config.typePrefix}-block-${config.id}`;
                console.log(componentType);

                // Ambil HTML yang sudah di-render dari objek global (window.bladeComponentsHtml)
                console.log(bladeComponentsHtml);
                const componentHtml = bladeComponentsHtml[componentType];

                if (!componentHtml) {
                    console.error(`Error: HTML for component type '${componentType}' not found in window.bladeComponentsHtml map.`);
                    return;
                }

                editor.DomComponents.addType(componentType, {
                    model: {
                        defaults: {
                            components: componentHtml, // Gunakan HTML yang sudah di-render
                        }
                    }
                });

                editor.BlockManager.add(blockId, {
                    label: config.label,
                    category: config.category,
                    media: config.mediaThumb,
                    content: {
                        type: componentType
                    },
                    activate: true,
                });
            }


            const pn = editor.Panels;
            const modal = editor.Modal;
            const cmdm = editor.Commands;

            // Menambahkan tombol ke panel atas
            pn.addButton('options', {
                id: 'back-button',
                className: 'fa fa-arrow-left',
                label: ' Back',
                command: 'navigate-back',
                attributes: {
                    title: 'Go back to pages list'
                },
            });

            // Mendefinisikan perintah untuk tombol yang ditambahkan
            const backButtonUrl = "{{ route('admin.pages.index') }}";
            cmdm.add('navigate-back', {
                run(editor, sender) {
                    sender.set('active', false); // Menonaktifkan tombol setelah diklik (opsional)
                    window.location.href = backButtonUrl;
                }
            });

            // Show borders by default
            pn.getButton('options', 'sw-visibility').set({
                command: 'core:component-outline',
                'active': true,
            });

            // Load and show settings and style manager
            var openTmBtn = pn.getButton('views', 'open-tm');
            openTmBtn && openTmBtn.set('active', 1);
            var openSm = pn.getButton('views', 'open-sm');
            openSm && openSm.set('active', 1);

            // Open block manager
            var openBlocksBtn = editor.Panels.getButton('views', 'open-blocks');
            openBlocksBtn && openBlocksBtn.set('active', 1);

            editor.on('load', () => {
                // 1. Kode untuk memuat stylesheet Vite ke kanvas (TETAP DIPERLUKAN)
                const viteCssLink = document.querySelector('link[rel="stylesheet"][href*="app.css"]');
                if (viteCssLink) {
                    const newLink = document.createElement('link');
                    newLink.rel = 'stylesheet';
                    newLink.href = viteCssLink.href;
                    editor.Canvas.getDocument().head.appendChild(newLink);
                }

                // 2. Terapkan class CSS ke body kanvas
                const wrapper = editor.getWrapper();
                wrapper.addClass('canvas-background'); // <-- Menggunakan addClass, bukan setStyle
            });
        </script>

        <script>
            $(document).ready(function() {
                setTimeout(() => {
                    $('#lspn').remove();
                }, 1000);
            });

            function handleScreenChange() {
                const editorContainer = document.getElementById('gjs');
                const unsupportedMessage = document.getElementById('unsupported-device');
                const loader = document.getElementById('lspn');

                if (window.innerWidth < 1024) {
                    // Layar terlalu kecil
                    if (loader) loader.style.display = 'none';
                    editorContainer.style.display = 'none';
                    unsupportedMessage.style.display = 'flex';
                } else {
                    // Layar cukup besar
                    unsupportedMessage.style.display = 'none';
                    editorContainer.style.display = 'block';

                    // Inisialisasi editor HANYA JIKA BELUM PERNAH DIBUAT
                    if (!editor) {
                        initializeEditor();
                    }
                }
            }

            handleScreenChange();

            // Tambahkan listener untuk memanggil fungsi setiap kali ukuran jendela berubah
            window.addEventListener('resize', handleScreenChange);
        </script>
    </body>

</html>
