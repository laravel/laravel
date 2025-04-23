@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['wrapper']['element'] = $column['wrapper']['element'] ?? $column['element'] ?? 'a';
    $column['wrapper']['target'] = $column['wrapper']['target'] ?? $column['target'] ?? '_blank';
    $column['wrapper']['href'] = $column['value'];
    $rel = $column['wrapper']['rel'] ?? $column['rel'] ?? null;

    if($rel !== false)  {
        $column['wrapper']['rel'] = $rel ?? 'noreferrer';
    }
@endphp
@include('crud::columns.text')