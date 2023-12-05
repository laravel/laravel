@aware(['component', 'tableName'])
@props(['rows', 'filterGenericData'])

<x-livewire-tables::table.tr.plain
    :customAttributes="$this->getFooterTrAttributes($rows)"
    wire:key="{{ $tableName .'-footer' }}"
>
    {{-- TODO: Remove --}}
    <x-livewire-tables::table.td.plain x-cloak x-show="currentlyReorderingStatus" wire:key="{{ $tableName . '-footer-hidden-test' }}" />

    @if ($this->collapsingColumnsAreEnabled() && $this->hasCollapsedColumns())
        <x-livewire-tables::table.td.collapsed-columns :displayMinimisedOnReorder="true" rowIndex="-1" :hidden="true" wire:key="{{ $tableName.'-footer-collapse' }}" />
    @endif

    {{-- TODO: Remove --}}
    <x-livewire-tables::table.td.plain wire:key="{{ $tableName . '-footer-hidden-tes2t' }}" />

    @foreach($this->getColumns() as $colIndex => $column)
        @continue($column->isHidden())
        @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))
        @continue($column->isReorderColumn() && !$this->getCurrentlyReorderingStatus() && $this->getHideReorderColumnUnlessReorderingStatus())
        <x-livewire-tables::table.td.plain :displayMinimisedOnReorder="true"  wire:key="{{ $tableName .'-footer-shown-'.$colIndex }}" :column="$column" :customAttributes="$this->getFooterTdAttributes($column, $rows, $colIndex)">
            {{ $column->getFooterContents($rows, $filterGenericData) }}
        </x-livewire-tables::table.td.plain>
    @endforeach
</x-livewire-tables::table.tr.plain>
