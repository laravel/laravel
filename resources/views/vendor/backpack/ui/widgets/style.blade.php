@php
    $widget['rel'] = $widget['rel'] ?? 'stylesheet';

    $href = asset($widget['href'] ?? $widget['content'] ?? $widget['path']);
    $attributes = collect($widget)->except(['name', 'section', 'type', 'stack', 'href', 'content', 'path'])->toArray();
@endphp

@push($widget['stack'] ?? 'after_styles')
    @basset($href, true, $attributes, 'style')
@endpush
