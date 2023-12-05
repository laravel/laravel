@php
    $filterKey = $filter->getKey();
    $currentMin = $minRange = $filter->getConfig('minRange');
    $currentMax = $maxRange = $filter->getConfig('maxRange');
@endphp
<div id="{{ $tableName }}-numberRange-{{ $filterKey }}" x-data="numberRangeFilter($wire,'{{ $filterKey }}', '{{ $tableName }}-numberRange-{{ $filterKey }}-wrapper', @js($filter->getConfigs()), '{{ $tableName }}-numberRange-{{ $filterKey }}')" x-on:mousedown.away.throttle.2000ms="updateWireable" x-on:touchstart.away.throttle.2000ms="updateWireable" x-on:mouseleave.throttle.2000ms="updateWireable">
    <x-livewire-tables::tools.filter-label :$filter :$filterLayout :$tableName :$isTailwind :$isBootstrap4 :$isBootstrap5 :$isBootstrap />


        <div @class([
            'mt-4 h-22 pt-8 pb-4 grid gap-10' => $isTailwind,
            'mt-4 h-22 w-100 pb-4 pt-2 grid gap-10' => $isBootstrap,
            ]) wire:ignore>
            <div 
                id="{{ $tableName }}-numberRange-{{ $filterKey }}-wrapper" data-ticks-position='bottom'
                @class([
                    'range-slider flat' => $isTailwind,
                    'range-slider flat w-100' => $isBootstrap,
                ])
                style='--min:{{ $minRange }};
                --max:{{ $maxRange }};
                --suffix:"{{ $filter->getConfig('suffix') }}";
                ' x-init="updateStyles">

                <input type="range" min="{{ $minRange }}" max="{{ $maxRange }}" value="{{ $currentMin }}"
                    id="{{ $tableName }}-numberRange-{{ $filterKey }}-min" x-model='filterMin' x-on:change="updateWire()" 
                    oninput="this.parentNode.style.setProperty('--value-a',this.value); this.parentNode.style.setProperty('--text-value-a', JSON.stringify(this.value))"
                    />
                <output></output>
                <input type="range" min="{{ $minRange }}" max="{{ $maxRange }}" value="{{ $currentMax }}"
                    id="{{ $tableName }}-numberRange-{{ $filterKey }}-max" x-model='filterMax' x-on:change="updateWire()"
                    oninput="this.parentNode.style.setProperty('--value-b',this.value); this.parentNode.style.setProperty('--text-value-b', JSON.stringify(this.value))"
                    />
                <output></output>
                <div class='range-slider__progress'></div>
            </div>
        </div>
</div>