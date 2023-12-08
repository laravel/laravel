@props(['submitName' => __('Save'), 'resetButton' => true])

<div class="row text-center">
    <div class="col-12">
        <div class="form-btns">
            <x-button wire:offline.attr="disabled" wire:loading.attr="disabled" type="submit">{{ $submitName }}</x-button>
            {{ $slot }}
            @if ($resetButton)
                <x-button wire:offline.attr="disabled" wire:loading.attr="disabled" type="button" wire:click="formReset" class="reset" color="secondary">{{ __('Reset') }}</x-button>
            @endif
        </div>
    </div>
</div>
