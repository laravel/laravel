@aware(['component', 'tableName'])
<div
    x-data="{ open: false, childElementOpen: false, isTailwind: @js($component->isTailwind()), isBootstrap: @js($component->isBootstrap()) }"
    x-cloak x-show="(selectedItems.length > 0 || alwaysShowBulkActions)"
    @class([
        'mb-3 mb-md-0' => $component->isBootstrap(),
        'w-full md:w-auto mb-4 md:mb-0' => $component->isTailwind(),
    ])
>
    <div @class([
            'dropdown d-block d-md-inline' => $component->isBootstrap(),
            'relative inline-block text-left z-10 w-full md:w-auto' => $component->isTailwind(),
        ])
    >
        <button
            @class([
                'btn dropdown-toggle d-block d-md-inline' => $component->isBootstrap(),
                'inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' => $component->isTailwind(),
            ])
            type="button"
            id="{{ $tableName }}-bulkActionsDropdown" 
            
                        
            @if($component->isTailwind())
                        x-on:click="open = !open"
                        @else
                        data-toggle="dropdown" data-bs-toggle="dropdown"
                        @endif
            aria-haspopup="true" aria-expanded="false">

            @lang('Bulk Actions')
            @if($component->isTailwind())
                <x-heroicon-m-chevron-down class="-mr-1 ml-2 h-5 w-5" />
            @endif
        </button>
        
        @if($component->isTailwind())
            <div
                x-on:click.away="if (!childElementOpen) { open = false }"
                @keydown.window.escape="if (!childElementOpen) { open = false }"
                x-cloak x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="origin-top-right absolute right-0 mt-2 w-full md:w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50"
            >
                <div class="rounded-md bg-white shadow-xs dark:bg-gray-700 dark:text-white">
                    <div class="py-1" role="menu" aria-orientation="vertical">
                        @foreach ($component->getBulkActions() as $action => $title)
                            <button
                                wire:click="{{ $action }}"
                                @if($component->hasConfirmationMessage($action))
                                    wire:confirm="{{ $component->getBulkActionConfirmMessage($action) }}"
                                @endif
                                wire:key="{{ $tableName }}-bulk-action-{{ $action }}"
                                type="button"
                                class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900 flex items-center space-x-2 dark:text-white dark:hover:bg-gray-600"
                                role="menuitem"
                            >
                                <span>{{ $title }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div
                @class([
                    'dropdown-menu dropdown-menu-right w-100' => $component->isBootstrap4(),
                    'dropdown-menu dropdown-menu-end w-100' => $component->isBootstrap5(),
                ])
                aria-labelledby="{{ $tableName }}-bulkActionsDropdown"
            >
                @foreach ($component->getBulkActions() as $action => $title)
                    <a
                        href="#"
                        @if($component->hasConfirmationMessage($action))
                            wire:confirm="{{ $component->getBulkActionConfirmMessage($action) }}"
                        @endif
                        wire:click="{{ $action }}"
                        wire:key="{{ $tableName }}-bulk-action-{{ $action }}"
                        @class([
                            'dropdown-item' => $component->isBootstrap(),
                        ])
                    >
                        {{ $title }}
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</div>
