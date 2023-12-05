@aware(['component', 'tableName'])

@if ($component->isTailwind())
    <div>
        @if ($component->sortingPillsAreEnabled() && $component->hasSorts())
            <div class="mb-4 px-4 md:p-0" x-cloak x-show="!currentlyReorderingStatus">
                <small class="text-gray-700 dark:text-white">@lang('Applied Sorting'):</small>

                @foreach($component->getSorts() as $columnSelectName => $direction)
                    @php($column = $component->getColumnBySelectName($columnSelectName) ?? $component->getColumnBySlug($columnSelectName))

                    @continue(is_null($column))
                    @continue($column->isHidden())
                    @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))

                    <span
                        wire:key="{{ $tableName }}-sorting-pill-{{ $columnSelectName }}"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-100 text-indigo-800 capitalize dark:bg-indigo-200 dark:text-indigo-900"
                    >
                        {{ $column->getSortingPillTitle() }}: {{ $column->getSortingPillDirection($component, $direction) }}

                        <button
                            wire:click="clearSort('{{ $columnSelectName }}')"
                            type="button"
                            class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white"
                        >
                            <span class="sr-only">@lang('Remove sort option')</span>
                            <x-heroicon-m-x-mark class="h-3 w-3" />
                        </button>
                    </span>
                @endforeach

                <button
                    wire:click.prevent="clearSorts"
                    class="focus:outline-none active:outline-none"
                >
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-200 dark:text-gray-900">
                        @lang('Clear')
                    </span>
                </button>
            </div>
        @endif
    </div>
@elseif ($component->isBootstrap4())
    <div>
        @if ($component->sortingPillsAreEnabled() && $component->hasSorts())
            <div class="mb-3" x-cloak x-show="!currentlyReorderingStatus">
                <small>@lang('Applied Sorting'):</small>

                @foreach($component->getSorts() as $columnSelectName => $direction)
                    @php($column = $component->getColumnBySelectName($columnSelectName) ?? $component->getColumnBySlug($columnSelectName))

                    @continue(is_null($column))
                    @continue($column->isHidden())
                    @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))

                    <span
                        wire:key="{{ $tableName . '-sorting-pill-' . $columnSelectName }}"
                        class="badge badge-pill badge-info d-inline-flex align-items-center"
                    >
                        {{ $column->getSortingPillTitle() }}: {{ $column->getSortingPillDirection($component, $direction) }}

                        <a
                            href="#"
                            wire:click="clearSort('{{ $columnSelectName }}')"
                            class="text-white ml-2"
                        >
                            <span class="sr-only">@lang('Remove sort option')</span>
                            <x-heroicon-m-x-mark style="width:1em;height:1em" />
                        </a>
                    </span>
                @endforeach

                <a
                    href="#"
                    wire:click.prevent="clearSorts"
                    class="badge badge-pill badge-light"
                >
                    @lang('Clear')
                </a>
            </div>
        @endif
    </div>
@elseif ($component->isBootstrap5())
    <div>
        @if ($component->sortingPillsAreEnabled() && $component->hasSorts())
            <div class="mb-3" x-cloak x-show="!currentlyReorderingStatus">
                <small>@lang('Applied Sorting'):</small>

                @foreach($component->getSorts() as $columnSelectName => $direction)
                    @php($column = $component->getColumnBySelectName($columnSelectName) ?? $component->getColumnBySlug($columnSelectName))

                    @continue(is_null($column))
                    @continue($column->isHidden())
                    @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))

                    <span
                        wire:key="{{ $tableName }}-sorting-pill-{{ $columnSelectName }}"
                        class="badge rounded-pill bg-info d-inline-flex align-items-center"
                    >
                        {{ $column->getSortingPillTitle() }}: {{ $column->getSortingPillDirection($component, $direction) }}

                        <a
                            href="#"
                            wire:click="clearSort('{{ $columnSelectName }}')"
                            class="text-white ms-2"
                        >
                            <span class="visually-hidden">@lang('Remove sort option')</span>
                            <x-heroicon-m-x-mark style="width:1em;height:1em" />
                        </a>
                    </span>
                @endforeach

                <a
                    href="#"
                    wire:click.prevent="clearSorts"
                    class="badge rounded-pill bg-light text-dark text-decoration-none"
                >
                    @lang('Clear')
                </a>
            </div>
        @endif
    </div>
@endif
