@props([
    'padding' => 'py-0',
    'allowOverflow' => false,
    'background' => 'bg-white',
    'shadow' => false,
    'maxWidth' => 4,
    'title' => null,
    'borders' => true,
    'isolated' => false,
    'addedClass' => ''
])

@php
    $widthClass = 'max-w-' . $maxWidth . 'xl';
    $borderClass = $borders && !$isolated ? 'border border-gray-200' : '';
    $shadowClass = $shadow && !$isolated ? 'shadow-sm' : '';
    $backgroundClass = $isolated ? 'bg-shopify-gray-active' : $background;
    $innerPadding = $isolated ? '' : 'p-4 sm:p-6';
@endphp

<div class="{{ $padding }}">
    <div class="{{ $widthClass }} mx-auto sm:px-6 lg:px-8">
        @if ($title && !$isolated)
            <h3 class="font-bold mb-4 pl-4 mt-8">{{ $title }}</h3>
        @elseif (isset($panelTitle) && !$isolated)
            <div class="font-bold mb-4 pl-4 mt-6">
                <div class="flex items-center space-x-2">
                    {{ $panelTitle }}
                </div>
            </div>
        @endif
        <div class="{{ $backgroundClass }} {{ $borderClass }} {{ $shadowClass }} rounded-xl {{ $allowOverflow ? 'overflow-hidden' : '' }}">
            <div {{ $attributes->class([$innerPadding, 'rounded-xl', $addedClass]) }}>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>