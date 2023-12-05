@aware(['component', 'tableName'])
@props(['filterGenericData'])

@if ($component->hasConfigurableAreaFor('before-toolbar'))
    @include($component->getConfigurableAreaFor('before-toolbar'), $component->getParametersForConfigurableArea('before-toolbar'))
@endif

<div @class([
        'd-md-flex justify-content-between mb-3' => $component->isBootstrap(),
        'md:flex md:justify-between mb-4 px-4 md:p-0' => $component->isTailwind(),
    ])
>
    <div @class([
            'd-md-flex' => $component->isBootstrap(),
            'w-full mb-4 md:mb-0 md:w-2/4 md:flex space-y-4 md:space-y-0 md:space-x-2' => $component->isTailwind(),
        ])
    >
        <div x-cloak x-show="!currentlyReorderingStatus">
            @if ($component->hasConfigurableAreaFor('toolbar-left-start'))
                @include($component->getConfigurableAreaFor('toolbar-left-start'), $component->getParametersForConfigurableArea('toolbar-left-start'))
            @endif
        </div>
        
        @if ($component->reorderIsEnabled())
            <x-livewire-tables::tools.toolbar.items.reorder-buttons />
        @endif
        
        @if ($component->searchIsEnabled() && $component->searchVisibilityIsEnabled())
            <x-livewire-tables::tools.toolbar.items.search-field />
        @endif

        @if ($component->filtersAreEnabled() && $component->filtersVisibilityIsEnabled() && $component->hasVisibleFilters())
            <x-livewire-tables::tools.toolbar.items.filter-button :$filterGenericData />
        @endif

        @if ($component->hasConfigurableAreaFor('toolbar-left-end'))
            <div x-cloak x-show="!currentlyReorderingStatus">
                @include($component->getConfigurableAreaFor('toolbar-left-end'), $component->getParametersForConfigurableArea('toolbar-left-end'))
            </div>
        @endif
    </div>

    <div x-cloak x-show="!currentlyReorderingStatus"         
        @class([
            'd-md-flex' => $component->isBootstrap(),
            'md:flex md:items-center space-y-4 md:space-y-0 md:space-x-2' => $component->isTailwind(),
        ])
    >
        @if ($component->hasConfigurableAreaFor('toolbar-right-start'))
            @include($component->getConfigurableAreaFor('toolbar-right-start'), $component->getParametersForConfigurableArea('toolbar-right-start'))
        @endif

        @if ($component->showBulkActionsDropdownAlpine())
            <x-livewire-tables::tools.toolbar.items.bulk-actions />
        @endif
        
        @if ($component->columnSelectIsEnabled())
            <x-livewire-tables::tools.toolbar.items.column-select /> 
        @endif

        @if ($component->paginationIsEnabled() && $component->perPageVisibilityIsEnabled())
            <x-livewire-tables::tools.toolbar.items.pagination-dropdown /> 
        @endif

        @if ($component->hasConfigurableAreaFor('toolbar-right-end'))
            @include($component->getConfigurableAreaFor('toolbar-right-end'), $component->getParametersForConfigurableArea('toolbar-right-end'))
        @endif
    </div>
</div>
@if (
    $component->filtersAreEnabled() &&
    $component->filtersVisibilityIsEnabled() &&
    $component->hasVisibleFilters() &&
    $component->isFilterLayoutSlideDown()
)
    <x-livewire-tables::tools.toolbar.items.filter-slidedown :$filterGenericData />
@endif


@if ($component->hasConfigurableAreaFor('after-toolbar'))
    <div x-cloak x-show="!currentlyReorderingStatus" >
        @include($component->getConfigurableAreaFor('after-toolbar'), $component->getParametersForConfigurableArea('after-toolbar'))
    </div>
@endif
