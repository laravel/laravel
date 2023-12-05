@aware(['component', 'tableName'])

@if ($component->collapsingColumnsAreEnabled() && $component->hasCollapsedColumns())
    @if ($component->isTailwind())
        <th
            scope="col"
            {{
                $attributes
                    ->merge(['class' => 'table-cell dark:bg-gray-800 laravel-livewire-tables-reorderingMinimised'])
                    ->class(['sm:hidden' => !$component->shouldCollapseOnTablet() && !$component->shouldCollapseAlways()])
                    ->class(['md:hidden' => !$component->shouldCollapseOnMobile() && !$component->shouldCollapseOnTablet() && !$component->shouldCollapseAlways()])
                    ->class(['lg:hidden' => !$component->shouldCollapseAlways()])
            }}
            :class="{ 'laravel-livewire-tables-reorderingMinimised': ! currentlyReorderingStatus }"
        ></th>
    @elseif ($component->isBootstrap())
        <th
            scope="col"
            {{
                $attributes
                    ->merge(['class' => 'd-table-cell laravel-livewire-tables-reorderingMinimised'])
                    ->class(['d-sm-none' => !$component->shouldCollapseOnTablet() && !$component->shouldCollapseAlways()])
                    ->class(['d-md-none' => !$component->shouldCollapseOnMobile() && !$component->shouldCollapseOnTablet() && !$component->shouldCollapseAlways()])
                    ->class(['d-lg-none' => !$component->shouldCollapseAlways()])
            }}                    
            :class="{ 'laravel-livewire-tables-reorderingMinimised': ! currentlyReorderingStatus }"
        ></th>
    @endif
@endif
