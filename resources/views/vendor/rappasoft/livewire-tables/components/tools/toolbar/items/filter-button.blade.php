@aware(['component', 'tableName'])
@props(['filterGenericData'])

<div x-cloak x-show="!currentlyReorderingStatus" 
                @class([
                    'ml-0 ml-md-2 mb-3 mb-md-0' => $component->isBootstrap4(),
                    'ms-0 ms-md-2 mb-3 mb-md-0' => $component->isBootstrap5() && $component->searchIsEnabled(),
                    'mb-3 mb-md-0' => $component->isBootstrap5() && !$component->searchIsEnabled(),
                ])
>
    <div
        @if ($component->isFilterLayoutPopover())
            x-data="{ filterPopoverOpen: false }"
            x-on:keydown.escape.stop="if (!this.childElementOpen) { filterPopoverOpen = false }"
            x-on:mousedown.away="if (!this.childElementOpen) { filterPopoverOpen = false }"
        @endif
        @class([
            'btn-group d-block d-md-inline' => $component->isBootstrap(),
            'relative block md:inline-block text-left' => $component->isTailwind(),
        ])
    >
        <div>
            <button
                type="button"
                @class([
                    'btn dropdown-toggle d-block w-100 d-md-inline' => $component->isBootstrap(),
                    'inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' => $component->isTailwind(),
                ])
                @if ($component->isFilterLayoutPopover()) x-on:click="filterPopoverOpen = !filterPopoverOpen"
                    aria-haspopup="true"
                    x-bind:aria-expanded="filterPopoverOpen"
                    aria-expanded="true"
                @endif
                @if ($component->isFilterLayoutSlideDown()) x-on:click="filtersOpen = !filtersOpen" @endif
            >
                @lang('Filters')

                @if ($count = $component->getFilterBadgeCount())
                    <span @class([
                            'badge badge-info' => $component->isBootstrap(),
                            'ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-100 text-indigo-800 capitalize dark:bg-indigo-200 dark:text-indigo-900' => $component->isTailwind(),
                        ])>
                        {{ $count }}
                    </span>
                @endif

                @if($component->isTailwind())
                    <x-heroicon-o-funnel class="-mr-1 ml-2 h-5 w-5" />
                @else
                <span @class([
                    'caret' => $component->isBootstrap(),
                ])></span>
                @endif

            </button>
        </div>

        @if ($component->isFilterLayoutPopover())
            <x-livewire-tables::tools.toolbar.items.filter-popover :$filterGenericData />
        @endif

    </div>
</div>
