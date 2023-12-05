@props(['disabled' => false, 'checked' => false])

<!-- <div class="checkbox"> -->
    <input {{ $disabled ? 'disabled' : '' }}  {{ $checked ? 'checked' : '' }} type="checkbox" {!! $attributes->merge(['class' => '']) !!} />
    <label class="fw-normal me-30" for="{{ $attributes->get('id') }}">{{ $slot }}</label>
<!-- </div> -->
