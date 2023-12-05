@aware(['component', 'tableName'])
@props(['row', 'rowIndex'])

@if ($component->collapsingColumnsAreEnabled() && $component->hasCollapsedColumns())
    @php
        $colspan = $component->getColspanCount();
        $columns = collect();

        if($component->shouldCollapseAlways())
        {
            $columns->push($component->getCollapsedAlwaysColumns());
        }
        if ($component->shouldCollapseOnMobile() && $component->shouldCollapseOnTablet()) {
            $columns->push($component->getCollapsedMobileColumns());
            $columns->push($component->getCollapsedTabletColumns());
        } elseif ($component->shouldCollapseOnTablet() && ! $component->shouldCollapseOnMobile()) {
            $columns->push($component->getCollapsedTabletColumns());
        } elseif ($component->shouldCollapseOnMobile() && ! $component->shouldCollapseOnTablet()) {
            $columns->push($component->getCollapsedMobileColumns());
        }

        $columns = $columns->collapse();
    @endphp

    <tr
        x-data
        @toggle-row-content.window="$event.detail.row === {{ $rowIndex }} ? $el.classList.toggle('{{ $component->isBootstrap() ? 'd-none' : 'hidden' }}') : null"

        wire:key="{{ $tableName }}-row-{{ $row->{$this->getPrimaryKey()} }}-collapsed-contents"
        wire:loading.class.delay="opacity-50 dark:bg-gray-900 dark:opacity-60"

        @class([
            'hidden bg-white dark:bg-gray-700 dark:text-white' => $component->isTailwind(),
            'd-none' => $component->isBootstrap()
        ])
    >
        <td
            @class([
                'pt-4 pb-2 px-4' => $component->isTailwind(),
                'pt-3 p-2' => $component->isBootstrap(),
            ])
            colspan="{{ $colspan }}"
        >
            <div>
                @foreach($columns as $colIndex => $column)
                    @continue($column->isHidden())
                    @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))

                    <p wire:key="{{ $tableName }}-row-{{ $row->{$this->getPrimaryKey()} }}-collapsed-contents-{{ $colIndex }}"
                    
                        @class([
                            'block mb-2' => $component->isTailwind() && $column->shouldCollapseAlways(),
                            'block mb-2 sm:hidden' => $component->isTailwind() && !$column->shouldCollapseAlways() && !$column->shouldCollapseOnTablet() && !$column->shouldCollapseOnMobile(),
                            'block mb-2 md:hidden' => $component->isTailwind() && !$column->shouldCollapseAlways() && !$column->shouldCollapseOnTablet() && $column->shouldCollapseOnMobile(),
                            'block mb-2 lg:hidden' => $component->isTailwind() && !$column->shouldCollapseAlways() && ($column->shouldCollapseOnTablet() || $column->shouldCollapseOnMobile()),
                            
                            'd-block mb-2' => $component->isBootstrap() && $column->shouldCollapseAlways(),
                            'd-block mb-2 d-sm-none' => $component->isBootstrap() && !$column->shouldCollapseAlways() && !$column->shouldCollapseOnTablet() && !$column->shouldCollapseOnMobile(),
                            'd-block mb-2 d-md-none' => $component->isBootstrap() && !$column->shouldCollapseAlways() && !$column->shouldCollapseOnTablet() && $column->shouldCollapseOnMobile(),
                            'd-block mb-2 d-lg-none' => $component->isBootstrap() && !$column->shouldCollapseAlways() && ($column->shouldCollapseOnTablet() || $column->shouldCollapseOnMobile()),

                        ])
                    >
                        <strong>{{ $column->getTitle() }}</strong>: {{ $column->renderContents($row) }}
                    </p>
                @endforeach
            </div>
        </td>
    </tr>
@endif
