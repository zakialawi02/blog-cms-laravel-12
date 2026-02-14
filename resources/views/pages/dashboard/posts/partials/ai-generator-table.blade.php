@forelse ($history as $item)
    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600" id="row-{{ $item->id }}" data-status="{{ $item->status }}">
        <td class="px-4 py-3 text-nowrap">
            {{ $item->created_at->format('Y-m-d H:i') }}
        </td>
        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
            <div class="line-clamp-1 truncate font-medium">{{ $item->topic }}</div>
        </td>
        <td class="px-4 py-3">{{ $item->model }}</td>
        <td class="px-4 py-3 status-cell">
            @if ($item->status === 'completed')
                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                    <span class="mr-1 h-2 w-2 rounded-full bg-green-500"></span> Completed
                    <span class="ml-1 text-xs text-green-600 dark:text-green-400">({{ $item->created_at->diffForHumans($item->updated_at, true) }})</span>
                </span>
            @elseif ($item->status === 'failed')
                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300" title="{{ $item->error_message }}">
                    <span class="mr-1 h-2 w-2 rounded-full bg-red-500"></span> Failed
                </span>
            @else
                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                    <span class="mr-1 h-2 w-2 animate-pulse rounded-full bg-yellow-500"></span> {{ ucfirst($item->status) }}
                </span>
            @endif
            <!-- Hidden Result for Copy/View (kept in DOM for reference if needed, but UI hidden) -->
            @if ($item->status === 'completed')
                <div id="result-{{ $item->id }}" class="hidden">{!! $item->result !!}</div>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
            No generation history found. Start creating!
        </td>
    </tr>
@endforelse
