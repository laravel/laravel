@php
    $maxValue = $column['attributes']['max'] ?? 100;
    $minValue = $column['attributes']['min'] ?? 0;

    $column['showMaxValue'] = $column['showMaxValue'] ?? true;
    $column['showValue'] = $column['showValue'] ?? $column['showMaxValue'];
    $column['progressColor'] = $column['progressColor'] ?? 'bg-success';
    $column['striped'] = $column['striped'] ?? false;

    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }
@endphp

@if (!empty($column['value']))
<div class="progress">
    <div 
        class="progress-bar {{ $column['progressColor']}} @if($column['striped']) progress-bar-striped @endif" 
        role="progressbar" 
        style="width: {{ ($column['value']/$maxValue)*100 }}%" 
        aria-valuenow="{{ $column['value'] }}" 
        aria-valuemin="{{ $minValue }}" 
        aria-valuemax="{{ $maxValue }}"
        >
        @if($column['showValue']){{ $column['value'] }}&nbsp; @if($column['showMaxValue']) / {{$maxValue}} @endif
         @endif
    </div>
</div>
@else
    <span>{{ $column['default'] ?? '-' }}</span>
@endif
