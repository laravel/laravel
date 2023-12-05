@props(['submitName' => __('Save'), 'resetButton' => true])

<div class="row text-center">
    <div class="col-12">
        <div class="form-btns">
            <x-button type="submit">{{ $submitName }}</x-button>
            {{ $slot }}
            @if ($resetButton)
                <x-button type="reset" class="reset" color="secondary">{{ __('Reset') }}</x-button>
            @endif
        </div>
    </div>
</div>
