@aware(['component', 'tableName'])
@if ($component->isTailwind())
<div class="@if ($component->getColumnSelectIsHiddenOnMobile()) hidden sm:block @elseif ($component->getColumnSelectIsHiddenOnTablet()) hidden md:block @endif mb-4 w-full md:w-auto md:mb-0 md:ml-2">
    <div
        x-data="{ open: false, childElementOpen: false }"
        @keydown.window.escape="if (!childElementOpen) { open = false }"
        x-on:click.away="if (!childElementOpen) { open = false }"
        class="inline-block relative w-full text-left md:w-auto"
        wire:key="{{ $tableName }}-column-select-button"
    >
        <div>
            <span class="rounded-md shadow-sm">
                <button
                    x-on:click="open = !open"
                    type="button"
                    class="inline-flex justify-center px-4 py-2 w-full text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600"
                    aria-haspopup="true"
                    x-bind:aria-expanded="open"
                    aria-expanded="true"
                >
                    @lang('Columns')

                    <x-heroicon-m-chevron-down class="-mr-1 ml-2 h-5 w-5" />
                </button>
            </span>
        </div>

        <div
            x-cloak x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-50 mt-2 w-full rounded-md divide-y divide-gray-100 ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right md:w-48 focus:outline-none"
        >
            <div class="bg-white rounded-md shadow-xs dark:bg-gray-700 dark:text-white">
                <div class="p-2" role="menu" aria-orientation="vertical"
                        aria-labelledby="column-select-menu"
                >
                    <div wire:key="{{ $tableName }}-columnSelect-selectAll-{{ rand(0,1000) }}">
                        <label
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-2 py-1 disabled:opacity-50 disabled:cursor-wait"
                        >
                            <input
                                class="text-indigo-600 transition duration-150 ease-in-out border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:bg-gray-600 disabled:opacity-50 disabled:cursor-wait"
                                wire:loading.attr="disabled" 
                                type="checkbox"
                                @checked($component->getSelectableSelectedColumns()->count() == $component->getSelectableColumns()->count())
                                @if($component->getSelectableSelectedColumns()->count() == $component->getSelectableColumns()->count())  wire:click="deselectAllColumns" @else wire:click="selectAllColumns" @endif
                            >
                            <span class="ml-2">{{ __('All Columns') }}</span>
                        </label>
                    </div>

                    @foreach ($component->getColumnsForColumnSelect() as $columnSlug => $columnTitle)
                        <div
                            wire:key="{{ $tableName }}-columnSelect-{{ $loop->index }}"
                        >
                            <label
                                wire:loading.attr="disabled"
                                wire:target="selectedColumns"
                                class="inline-flex items-center px-2 py-1 disabled:opacity-50 disabled:cursor-wait"
                            >
                                <input
                                    class="text-indigo-600 rounded border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:bg-gray-600 disabled:opacity-50 disabled:cursor-wait"
                                    wire:model.live="selectedColumns" wire:target="selectedColumns"
                                    wire:loading.attr="disabled" type="checkbox"
                                    value="{{ $columnSlug }}" />
                                <span class="ml-2">{{ $columnTitle }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@elseif ($component->isBootstrap())
<div
    @class([
        'd-none d-sm mb-3 mb-md-0 pl-0 pl-md-2' => $component->getColumnSelectIsHiddenOnMobile() && $component->isBootstrap4(),
        'd-none d-md-block mb-3 mb-md-0 pl-0 pl-md-2' => $component->getColumnSelectIsHiddenOnTablet() && $component->isBootstrap4(),
        'd-none d-sm-block mb-3 mb-md-0 md-0 ms-md-2' => $component->getColumnSelectIsHiddenOnMobile() && $component->isBootstrap5(),
        'd-none d-md-block mb-3 mb-md-0 md-0 ms-md-2' => $component->getColumnSelectIsHiddenOnTablet() && $component->isBootstrap5(),
    ])
>
    <div
        x-data="{ open: false, childElementOpen: false }"
        x-on:keydown.escape.stop="if (!childElementOpen) { open = false }"
        x-on:mousedown.away="if (!childElementOpen) { open = false }"
        @class([
            'dropdown d-block d-md-inline' => $component->isBootstrap(),
        ])
        wire:key="{{ $tableName }}-column-select-button"
    >
        <button
            x-on:click="open = !open"
            @class([
                'btn dropdown-toggle d-block w-100 d-md-inline' => $component->isBootstrap(),
            ])
            type="button" id="{{ $tableName }}-columnSelect" aria-haspopup="true"
            x-bind:aria-expanded="open"
        >
            @lang('Columns')
        </button>

        <div
            x-bind:class="{ 'show': open }"
            @class([
                'dropdown-menu dropdown-menu-right w-100 mt-0 mt-md-3' => $component->isBootstrap4(),
                'dropdown-menu dropdown-menu-end w-100' => $component->isBootstrap5(),
            ])
            aria-labelledby="columnSelect-{{ $tableName }}"
        >
            @if($component->isBootstrap4())
                <div wire:key="{{ $tableName }}-columnSelect-selectAll-{{ rand(0,1000) }}">
                    <label wire:loading.attr="disabled" class="px-2 mb-1">
                        <input
                            wire:loading.attr="disabled"
                            type="checkbox"
                            @if($component->getSelectableSelectedColumns()->count() == $component->getSelectableColumns()->count()) checked wire:click="deselectAllColumns" @else unchecked wire:click="selectAllColumns" @endif
                        />

                        <span class="ml-2">{{ __('All Columns') }}</span>
                    </label>
                </div>
            @elseif($component->isBootstrap5())
                <div class="form-check ms-2" wire:key="{{ $tableName }}-columnSelect-selectAll-{{ rand(0,1000) }}">
                    <input
                        wire:loading.attr="disabled"
                        type="checkbox"
                        class="form-check-input"
                        @if($component->getSelectableSelectedColumns()->count() == $component->getSelectableColumns()->count()) checked wire:click="deselectAllColumns" @else unchecked wire:click="selectAllColumns" @endif
                    />

                    <label wire:loading.attr="disabled" class="form-check-label">
                        {{ __('All Columns') }}
                    </label>
                </div>
            @endif

            @foreach ($component->getColumnsForColumnSelect() as $columnSlug => $columnTitle)
                <div
                    wire:key="{{ $tableName }}-columnSelect-{{ $loop->index }}"
                    @class([
                        'form-check ms-2' => $component->isBootstrap5(),
                    ])
                >
                    @if ($component->isBootstrap4())
                        <label
                            wire:loading.attr="disabled"
                            wire:target="selectedColumns"
                            class="px-2 {{ $loop->last ? 'mb-0' : 'mb-1' }}"
                        >
                            <input
                                wire:model.live="selectedColumns"
                                wire:target="selectedColumns"
                                wire:loading.attr="disabled" type="checkbox"
                                value="{{ $columnSlug }}"
                            />
                            <span class="ml-2">
                                {{ $columnTitle }}
                            </span>
                        </label>
                    @elseif($component->isBootstrap5())
                        <input
                            wire:model.live="selectedColumns"
                            wire:target="selectedColumns"
                            wire:loading.attr="disabled"
                            type="checkbox"
                            class="form-check-input"
                            value="{{ $columnSlug }}"
                        />
                        <label
                            wire:loading.attr="disabled"
                            wire:target="selectedColumns"
                            class="{{ $loop->last ? 'mb-0' : 'mb-1' }} form-check-label"
                        >
                            {{ $columnTitle }}
                        </label>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

@endif
