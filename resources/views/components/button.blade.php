@props(['type' => 'button', 'color' => 'primary'])

<button {{ $attributes->merge(['type' => $type, 'class' => sprintf("btn btn-%s", $color)]) }}>
    {{ $slot }}
</button>
