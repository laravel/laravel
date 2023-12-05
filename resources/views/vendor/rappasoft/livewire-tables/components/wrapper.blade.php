@props(['component', 'tableName'])

<div wire:key="{{ $tableName }}-wrapper" x-data="tableWrapper($wire, {{ $component->showBulkActionsDropdownAlpine() }})">
    <div {{ $attributes->merge($this->getComponentWrapperAttributes()) }}
        @if ($component->hasRefresh()) wire:poll{{ $component->getRefreshOptions() }} @endif
        @if ($component->isFilterLayoutSlideDown()) wire:ignore.self @endif>

        <div x-data="reorderFunction($wire, '{{ $component->getTableAttributes()['id'] }}', '{{ $component->getPrimaryKey() }}')">
            @include('livewire-tables::includes.debug')
            @include('livewire-tables::includes.offline')

            {{ $slot }}
        </div>
    </div>
</div>
