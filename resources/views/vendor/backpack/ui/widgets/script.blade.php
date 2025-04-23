@php
    $src = $widget['src'] ?? $widget['content'] ?? $widget['path'];
    $attributes = collect($widget)->except(['name', 'section', 'type', 'stack', 'src', 'content', 'path'])->toArray();
@endphp

@push($widget['stack'] ?? 'after_scripts')
    @basset($src, true, $attributes)
@endpush
