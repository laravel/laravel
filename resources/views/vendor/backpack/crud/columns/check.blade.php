{{-- checkbox with loose false/null/0 checking --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    $column['icon'] = $column['value'] != false
        ? ($column['icons']['checked'] ?? 'la-check-circle')
        : ($column['icons']['unchecked'] ?? 'la-circle');

    $column['text'] = $column['value'] != false
        ? ($column['labels']['checked'] ?? trans('backpack::crud.yes'))
        : ($column['labels']['unchecked'] ?? trans('backpack::crud.no'));

    $column['text'] = $column['prefix'].$column['text'].$column['suffix'];
@endphp

<span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
    <i class="la {{ $column['icon'] }}"></i>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>

<span class="sr-only">
    @if($column['escaped'])
        {{ $column['text'] }}
    @else
        {!! $column['text'] !!}
    @endif
</span>
