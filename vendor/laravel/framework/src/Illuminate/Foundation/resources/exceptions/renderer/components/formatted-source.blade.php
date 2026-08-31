@props(['frame'])

@php
    $class = $frame->class();
    $operator = $frame->operator();
    $callable = $frame->callable();

    if ($class && $operator) {
        $source = $class.$operator.$callable.'('.implode(', ', $frame->args()).')';
    } elseif ($callable !== 'throw') {
        $source = $callable.'('.implode(', ', $frame->args()).')';
    } else {
        $source = $frame->source();
    }
@endphp

<x-laravel-exceptions-renderer::syntax-highlight
    :code="$source"
    language="php"
    truncate
    class="text-xs min-w-0"
    data-tippy-content="{{ $source }}"
/>
