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
        @php
            // Determine the current item type, prioritizing old form input over saved data.
            // This ensures that if validation fails, the form shows the user's last selection.
$currentItemType = old("sections_config.{$sectionKey}.items", $sectionData['items'] ?? '');
$isJsScript = $currentItemType === 'js-script';
// Get the value for the 'total' field, which could be a number or JS code.
$totalValue = old("sections_config.{$sectionKey}.total", $sectionData['total'] ?? null);
        @endphp
        <div class="form-group">
            {{-- The label text will change dynamically --}}
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" id="{{ $sectionKey }}_total_label" for="{{ $isJsScript ? $sectionKey . '_total_textarea' : $sectionKey . '_total_input' }}">
                {{ $isJsScript ? 'JavaScript Code' : 'Number of Items to Display' }}
            </label>

            {{-- Input for Number of Items (hidden if js-script is selected) --}}
            <input class="@if ($isJsScript) hidden @endif mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="{{ $sectionKey }}_total_input" name="sections_config[{{ $sectionKey }}][total]" type="number" value="{{ $totalValue ?? 0 }}" min="0" @if ($isJsScript) disabled @endif>

            {{-- Textarea for JS Code (hidden unless js-script is selected) --}}
            <textarea class="@if (!$isJsScript) hidden @endif mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" id="{{ $sectionKey }}_total_textarea" name="sections_config[{{ $sectionKey }}][total]" rows="6" placeholder="Enter your JavaScript code here..." @if (!$isJsScript) disabled @endif>{{ $totalValue ?? '' }}</textarea>

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

@push('javascript')
    {{-- JAVASCRIPT: To handle the dynamic switching on the client-side --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sectionKey = '{{ $sectionKey }}';
            const selectElement = document.getElementById(`${sectionKey}_items`);
            if (!selectElement) return;

            // Get references to the elements that will be toggled
            const numberInput = document.getElementById(`${sectionKey}_total_input`);
            const textarea = document.getElementById(`${sectionKey}_total_textarea`);
            const label = document.getElementById(`${sectionKey}_total_label`);

            // Function to toggle between the number input and the textarea
            const toggleInputs = (selectedValue) => {
                if (selectedValue === 'js-script') {
                    // Change to JS Code Textarea
                    label.textContent = 'JavaScript Code';
                    label.htmlFor = `${sectionKey}_total_textarea`; // Update label's target
                    numberInput.classList.add('hidden');
                    numberInput.disabled = true; // Disabled inputs are not submitted with the form
                    textarea.classList.remove('hidden');
                    textarea.disabled = false;
                } else {
                    // Change to Number of Items Input
                    label.textContent = 'Number of Items to Display';
                    label.htmlFor = `${sectionKey}_total_input`; // Update label's target
                    numberInput.classList.remove('hidden');
                    numberInput.disabled = false;
                    textarea.classList.add('hidden');
                    textarea.disabled = true;
                }
            };

            // Add an event listener to the select dropdown
            selectElement.addEventListener('change', (event) => {
                toggleInputs(event.target.value);
            });
        });
    </script>
@endpush
