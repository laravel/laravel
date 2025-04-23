@php
    // defaults; backwards compatibility with Backpack 4.0 widgets
    $widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-lg-3';
@endphp

@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_start'))

<div class="{{ $widget['class'] ?? 'card' }}" @foreach($widget['attributes'] ?? [] as $key => $value) {{ $key }}="{{ $value }}" @endforeach>
    @if(isset($widget['key']))
        @livewire($widget['content'], $widget['parameters'] ?? [], key($widget['key']))
    @else
        @livewire($widget['content'], $widget['parameters'] ?? [])
    @endif
</div>

@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_end'))

@if($widget['livewireAssets'] ?? false)
    @pushOnce('after_styles')
        @livewireStyles
    @endPushOnce

    @pushOnce('after_scripts')
        @livewireScripts
    @endpushOnce
@endif

