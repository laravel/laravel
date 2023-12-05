<form wire:submit.prevent="submitForm">
    <div class="row">
        <div class="col-lg-6">
            <x-input-form-group class="mb-4" inputName="name">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" wire:model.live.debounce.500ms="name" wire:dirty.class="border-yellow" type="text" autofocus autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </x-input-form-group>
        </div>
        <div class="col-lg-6">
            <x-input-form-group class="mb-4" inputName="short_code">
                <x-input-label for="short_code" :value="__('Short code')" />
                <x-text-input id="short_code" wire:model.live.debounce.500ms="short_code" type="text" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('short_code')" />
            </x-input-form-group>
        </div>
    </div>

    <x-form-action></x-form-action>
</form>
