@props(['method'])

@php
$type = match ($method) {
    'GET', 'OPTIONS', 'ANY' => 'default',
    'POST' => 'success',
    'PUT', 'PATCH' => 'primary',
    'DELETE' => 'error',
    default => 'default',
};
@endphp

<x-laravel-exceptions-renderer::badge type="{{ $type }}">
    <x-laravel-exceptions-renderer::icons.globe class="w-2.5 h-2.5" />
    {{ $method }}
</x-laravel-exceptions-renderer::badge>
