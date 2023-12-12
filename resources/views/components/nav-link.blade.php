@props(['active'])

@php
$classes = ($active ?? false)
            ? 'active'
            : '';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
