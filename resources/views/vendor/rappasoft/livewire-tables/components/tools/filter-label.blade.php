@aware(['component', 'tableName'])
@props(['filter', 'filterLayout' => 'popover', 'tableName' => 'table', 'isTailwind' => false, 'isBootstrap' => false, 'isBootstrap4' => false, 'isBootstrap5' => false])

@php
    $customLabelAttributes = $filter->getFilterLabelAttributes();
@endphp

@if($filter->hasCustomFilterLabel() && !$filter->hasCustomPosition())
    @include($filter->getCustomFilterLabel(),['filter' => $filter, 'filterLayout' => $filterLayout, 'tableName' => $tableName, 'isTailwind' => $isTailwind, 'isBootstrap' => $isBootstrap, 'isBootstrap4' => $isBootstrap4, 'isBootstrap5' => $isBootstrap5, 'customLabelAttributes' => $customLabelAttributes])
@elseif(!$filter->hasCustomPosition())
    <label for="{{ $tableName }}-filter-{{ $filter->getKey() }}"

        {{
            $attributes->merge($customLabelAttributes)
                ->class(['block text-sm font-medium leading-5 text-gray-700 dark:text-white' => $isTailwind && ($customLabelAttributes['default'] ?? true)])
                ->class(['d-block' => $isBootstrap && $filterLayout == 'slide-down' && ($customLabelAttributes['default'] ?? true)])
                ->class(['mb-2' => $isBootstrap && $filterLayout == 'popover' && ($customLabelAttributes['default'] ?? true)])
                ->except('default')
        }}

    >
        {{ $filter->getName() }}
    </label>

@endif
