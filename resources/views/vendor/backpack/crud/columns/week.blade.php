@php
    $column['prefix'] = $column['prefix'] ?? 'Week ';
    $column['format'] = $column['format'] ?? 'W Y';

@endphp
@include('crud::columns.date')