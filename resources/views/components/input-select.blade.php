@props(['disabled' => false, 'options' => [], 'value' => null])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-select']) !!}>
    {{ $slot }}
    @foreach($options as $optionId => $optionName)
        <option value="{{ $optionId }}" {{ $optionId === $value ? 'selected' : '' }}> {{ $optionName }} </option>
    @endforeach
</select>