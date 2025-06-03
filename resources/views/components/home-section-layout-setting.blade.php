@props(['sectionKey', 'sectionData', 'setTotalToView' => true, 'itemKeyOptions' => []]) {{-- Tambahkan itemKeyOptions ke props --}}

<div class="space-y-3">
    <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="{{ $sectionKey }}_label">Display Label</label>
        <input class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="{{ $sectionKey }}_label" name="sections_config[{{ $sectionKey }}][label]" type="text" value="{{ old("sections_config.{$sectionKey}.label", $sectionData['label'] ?? '') }}">
        @error("sections_config.{$sectionKey}.label")
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="{{ $sectionKey }}_items">Content Items Key</label>
        {{-- Mengganti input teks dengan select dropdown --}}
        <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="{{ $sectionKey }}_items" name="sections_config[{{ $sectionKey }}][items]">
            @if (empty($itemKeyOptions))
                <option value="">-- No options available --</option>
            @else
                @foreach ($itemKeyOptions as $value => $label)
                    <option value="{{ $value }}" {{ old("sections_config.{$sectionKey}.items", $sectionData['items'] ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            @endif
        </select>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the type of content to display for this section.</p>
        @error("sections_config.{$sectionKey}.items")
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>

    @if ($setTotalToView)
        <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="{{ $sectionKey }}_total">Number of Items to Display</label>
            <input class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="{{ $sectionKey }}_total" name="sections_config[{{ $sectionKey }}][total]" type="number" value="{{ old("sections_config.{$sectionKey}.total", $sectionData['total'] ?? 0) }}" min="0">
            @error("sections_config.{$sectionKey}.total")
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>
    @endif

    <div class="form-group">
        <div class="flex items-center">
            <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:focus:ring-offset-slate-900" id="{{ $sectionKey }}_is_visible" name="sections_config[{{ $sectionKey }}][is_visible]" type="checkbox" value="1" {{ old("sections_config.{$sectionKey}.is_visible", $sectionData['is_visible'] ?? false) ? 'checked' : '' }}>
            <label class="ml-2 block text-sm text-gray-900 dark:text-gray-300" for="{{ $sectionKey }}_is_visible">Display This Section?</label>
        </div>
        @error("sections_config.{$sectionKey}.is_visible")
            <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
    </div>
</div>
