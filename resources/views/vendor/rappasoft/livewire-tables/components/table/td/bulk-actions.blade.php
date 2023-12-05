@aware(['component', 'tableName'])
@props(['row', 'rowIndex'])

@php($theme = $component->getTheme())

@if ($component->bulkActionsAreEnabled() && $component->hasBulkActions())
    <x-livewire-tables::table.td.plain wire:key="{{ $tableName }}-tbody-td-bulk-actions-td-{{ $row->{$this->getPrimaryKey()} }}" :displayMinimisedOnReorder="true"  >
        <div @class([
            'inline-flex rounded-md shadow-sm' => $theme === 'tailwind',
            'form-check' => $theme === 'bootstrap-5',
        ])>
            <input
                x-cloak x-show="!currentlyReorderingStatus"
                x-model="selectedItems"
                wire:key="{{ $tableName . 'selectedItems-'.$row->{$this->getPrimaryKey()} }}"
                wire:loading.attr.delay="disabled"
                value="{{ $row->{$this->getPrimaryKey()} }}"
                type="checkbox"
                @class([
                    'rounded border-gray-300 text-indigo-600 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:bg-gray-600' => $theme === 'tailwind',
                    'form-check-input' => $theme === 'bootstrap-5',
                ])
            />
        </div>
    </x-livewire-tables::table.td.plain>
@endif
