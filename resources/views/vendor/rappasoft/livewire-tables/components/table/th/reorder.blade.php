@aware(['component', 'tableName'])

<x-livewire-tables::table.th.plain x-cloak x-show="currentlyReorderingStatus" wire:key="{{ $tableName }}-thead-reorder" :displayMinimisedOnReorder="false">
    <div x-cloak x-show="currentlyReorderingStatus"></div>
</x-livewire-tables::table.th.plain>
