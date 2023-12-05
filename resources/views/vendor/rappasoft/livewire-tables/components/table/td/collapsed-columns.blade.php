@aware(['component', 'tableName'])
@props(['rowIndex', 'hidden' => false])

@if ($component->collapsingColumnsAreEnabled() && $component->hasCollapsedColumns())
    @if ($component->isTailwind())
        <td x-data="{open:false}" wire:key="{{ $tableName }}-collapsingIcon-{{ $rowIndex }}-{{ md5(now()) }}"
            {{
                $attributes
                    ->merge(['class' => 'p-3 table-cell text-center '])
                    ->class(['sm:hidden' => !$component->shouldCollapseAlways() && !$component->shouldCollapseOnTablet()])
                    ->class(['md:hidden' => !$component->shouldCollapseAlways() && !$component->shouldCollapseOnTablet() && $component->shouldCollapseOnMobile()])
                    ->class(['lg:hidden' => !$component->shouldCollapseAlways() && ($component->shouldCollapseOnTablet() || $component->shouldCollapseOnMobile())])
            }}
            :class="currentlyReorderingStatus ? 'laravel-livewire-tables-reorderingMinimised' : ''"
        >
            @if (! $hidden)
                <button
                    x-cloak x-show="!currentlyReorderingStatus"
                    x-on:click.prevent="$dispatch('toggle-row-content', {'row': {{ $rowIndex }}}); open = !open"
                >
                    <x-heroicon-o-plus-circle x-cloak x-show="!open" class="text-green-600 h-6 w-6" />
                    <x-heroicon-o-minus-circle x-cloak x-show="open" class="text-yellow-600 h-6 w-6" />
                </button>
            @endif
        </td>
    @elseif ($component->isBootstrap())
        <td x-data="{open:false}" wire:key="{{ $tableName }}-collapsingIcon-{{ $rowIndex }}-{{ md5(now()) }}" 
            {{
                $attributes
                    ->class(['d-sm-none' => !$component->shouldCollapseAlways() && !$component->shouldCollapseOnTablet()])
                    ->class(['d-md-none' => !$component->shouldCollapseAlways() && !$component->shouldCollapseOnTablet() && $component->shouldCollapseOnMobile()])
                    ->class(['d-lg-none' => !$component->shouldCollapseAlways() && ($component->shouldCollapseOnTablet() || $component->shouldCollapseOnMobile())])
            }}
            :class="currentlyReorderingStatus ? 'laravel-livewire-tables-reorderingMinimised' : ''"
        >
            @if (! $hidden)
                <button
                    x-cloak x-show="!currentlyReorderingStatus"
                    x-on:click.prevent="$dispatch('toggle-row-content', {'row': {{ $rowIndex }}});open = !open"
                    class="p-0"
                    style="background:none;border:none;"
                >
                    <x-heroicon-o-plus-circle x-cloak x-show="!open" class="text-success" style="width:1.4em;height:1.4em;" />
                    <x-heroicon-o-minus-circle x-cloak x-show="open" class="text-warning" style="width:1.4em;height:1.4em;" />
                </button>
            @endif
        </td>
    @endif
@endif
