<div {{ $attributes->merge(['class' => 'mb-3'])->filter(fn ($value, $key) => !in_array($key, ['for', 'label'])) }}>
    <label for="{{ $for }}" class="form-label">{{ $label }}</label>
    {{ $slot }}
    </div>
