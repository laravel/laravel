@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['showColorHex'] = $column['showColorHex'] ?? true;

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }
  
    $column['text'] = $column['value'] ?? $column['default'] ?? '-';
@endphp

<span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        @if(!empty($column['value']))
            @if($column['escaped'])
                <span title="{!! $column['text'] !!}" class="btn rounded-circle" style="font-size: 0.5rem; background-color: {!! $column['text'] !!}">&nbsp;</span>
                @if($column['showColorHex'])
                {!! $column['text'] !!}
                @endif
            @else
                <span title="{{ $column['text'] }}" class="btn rounded-circle" style="font-size: 0.5rem; background-color: {{ $column['text'] }}">&nbsp;</span>
                @if($column['showColorHex'])
                {{  $column['text'] }}
                @endif
            @endif
        @else
            {{$column['text']}}
        @endif
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>
