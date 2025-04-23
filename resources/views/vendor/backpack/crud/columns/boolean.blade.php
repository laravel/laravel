{{-- converts 1/true or 0/false to yes/no/lang --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    if (in_array($column['value'], [true, 1, '1'])) {
        $related_key = 1;
        if ( isset( $column['options'][1] ) ) {
            $column['text'] = $column['options'][1];
            $column['escaped'] = false;
        } else {
            $column['text'] = Lang::has('backpack::crud.yes') ? trans('backpack::crud.yes') : 'Yes';
        }
    } else {
        $related_key = 0;
        if ( isset( $column['options'][0] ) ) {
            $column['text'] = $column['options'][0];
            $column['escaped'] = false;
        } else {
            $column['text'] = Lang::has('backpack::crud.no') ? trans('backpack::crud.no') : 'No';
        }
    }

    $column['text'] = $column['prefix'].$column['text'].$column['suffix'];
@endphp

<span data-order="{{ $column['value'] }}">
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        @if($column['escaped'])
            {{ $column['text'] }}
        @else
            {!! $column['text'] !!}
        @endif
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>
