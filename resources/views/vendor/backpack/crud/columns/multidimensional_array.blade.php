{{-- enumerate the values in an array --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $list = [];

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    // if the isn't using attribute casting, decode it
    if (is_string($column['value'])) {
        $column['value'] = json_decode($column['value']);
    }

    if (is_array($column['value']) && count($column['value'])) {
        foreach ($column['value'] as $item) {
            if (isset($item->{$column['visible_key']})) {
                $list[$column['visible_key']][] = $item->{$column['visible_key']};
            } elseif (is_array($item) && isset($item[$column['visible_key']])) {
                $list[$column['visible_key']][] = $item[$column['visible_key']];
            }
        }
    }

    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
@endphp

<span>
    @if(!empty($list))
        {{ $column['prefix'] }}
        @foreach($list[$column['visible_key']] as $key => $text)
            @php
                $column['text'] = $text;
                $related_key = $key;
            @endphp

            <span class="d-inline-flex">
                @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
                    @if($column['escaped'])
                        {{ $column['text'] }}
                    @else
                        {!! $column['text'] !!}
                    @endif
                @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
            
                @if(!$loop->last), @endif
            </span>
        @endforeach
        {{ $column['suffix'] }}
    @else
        {{ $column['default'] ?? '-' }}
    @endif
</span>
