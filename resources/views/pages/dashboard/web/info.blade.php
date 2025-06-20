@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="section">
            <h1>Environment Variables</h1>
            <div class="table-container overflow-x-auto">
                <table class="display table" id="myTable">
                    @foreach ($data as $key => $value)
                        <tr>
                            {{-- Jika error terjadi di sini, artinya $key adalah sebuah array --}}
                            <th>{{ strtoupper($key) }}</th>

                            {{-- Bagian ini seharusnya sudah aman dari solusi sebelumnya --}}
                            <td>
                                @if (is_array($value))
                                    {{-- json_encode aman untuk semua jenis array, termasuk multi-dimensi --}}
                                    {{-- Penggunaan <pre> agar format JSON lebih mudah dibaca --}}
                                    <pre style="margin: 0; background: transparent; padding: 0; border: none;"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="section">
            <h1>PHP Configuration</h1>
            <div class="table-container overflow-x-auto">
                <table class="display table" id="myTable2">
                    @foreach ($phpInfo as $key => $value)
                        <tr>
                            <th>{{ $key }}</th>
                            <td>
                                @if (is_array($value))
                                    <ul>
                                        @foreach ($value as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </section>

    @push('css')
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 40px;
            }

            th,
            td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
                color: #252525 !important;
                width: 300px;
            }

            .section {
                margin-bottom: 50px;
            }
        </style>
    @endpush
</x-app-layout>
