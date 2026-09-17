<div class="flex items-center gap-2">
    <label for="year-selector" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Year:</label>
    <select
        id="year-selector"
        wire:model.live="selectedYear"
        class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
    >
        @foreach ($this->years as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
