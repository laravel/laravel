@php
    $filterKey = $filter->getKey();
@endphp

<div x-cloak id="{{ $tableName }}-dateRangeFilter-{{ $filterKey }}" x-data="flatpickrFilter($wire, '{{ $filterKey }}', @js($filter->getConfigs()), $refs.dateRangeInput, '{{ App::currentLocale() }}')" >
    <x-livewire-tables::tools.filter-label :$filter :$filterLayout :$tableName :$isTailwind :$isBootstrap4 :$isBootstrap5 :$isBootstrap />
    <div
        @class([
            'w-full rounded-md shadow-sm text-left ' => $isTailwind,
            'd-inline-block w-100 mb-3 mb-md-0 input-group' => $isBootstrap,
        ])
    >
        <input
            type="text"
            x-ref="dateRangeInput"
            x-on:click="init"
            value="{{ $filter->getDateString(isset($this->appliedFilters[$filterKey]) ? $this->appliedFilters[$filterKey] : '') }}"
            wire:key="{{ $filter->generateWireKey($tableName, 'dateRange') }}"
            id="{{ $tableName }}-filter-dateRange-{{ $filterKey }}"
            @class([
                'w-full inline-block align-middle transition duration-150 ease-in-out border-gray-300 rounded-md shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white dark:border-gray-600' => $isTailwind,
                'd-inline-block w-100 form-control' => $isBootstrap,
            ])
            @if($filter->hasConfig('placeholder')) placeholder="{{ $filter->getConfig('placeholder') }}" @endif
        />     
    </div>
</div>
