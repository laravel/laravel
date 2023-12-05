@props(['messages'])

@if ($messages)
    <div class="help-block">
        <ul role="alert" {{ $attributes->merge(['class' => 'text-sm']) }}>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
