<div>
    <form wire:submit.prevent="submitForm">
        <div class="row">
            <div class="col">
                <x-input-form-group class="mb-4" inputName="name">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" wire:model="name" wire:dirty.class="border-yellow" type="text" autofocus autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </x-input-form-group>
            </div>
            <div class="col">
                <x-input-form-group class="mb-4" inputName="short_code">
                    <x-input-label for="short_code" :value="__('Short code')" />
                    <x-text-input id="short_code" wire:model="short_code" type="text" autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('short_code')" />
                </x-input-form-group>
            </div>
            <div class="col">
                <x-input-form-group class="mb-4" inputName="date">
                    <x-input-label for="date" :value="__('Date')" />
                    <div wire:ignore>
                        <x-text-input id="date" wire:model="date" type="text" autocomplete="off" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('date')" />
                </x-input-form-group>
            </div>
            <div class="col">
                <x-input-form-group class="mb-4" inputName="color">
                    <x-input-label for="color" :value="__('Color')" />
                    <div wire:ignore>
                        <x-input-select id="color" class="select2" wire:model="color" :options="['R' => 'Red', 'G' => 'Green', 'B' => 'Blue']">
                            <option></option>
                        </x-input-select>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('color')" />
                </x-input-form-group>
            </div>
        </div>

        <x-form-action></x-form-action>
    </form>
</div>

@assets
    {{--We are using the CDN for just example--}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" type="text/css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" type="text/css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" defer></script>
@endassets

@script
    <script>
        $(document).ready(function() {
            initScripts();
        });

        /**
         * Initialise the form scripts
         */
        function initScripts() {
            const $datePicker = $('#date');

            $datePicker.datepicker({
                format: 'yyyy-mm-dd',
            });

            const $select2 = $('#color');

            $select2.select2({
                placeholder: "Select Color",
                allowClear: true
            });

            // We should set the wire model property
            // Reason: the DOM will not update when livewire initialised
            $select2.on('change', function (e) {
                @this.set('color', e.target.value);
            });

            // We should set the wire model property
            // Reason: the DOM will not update when livewire initialised
            $datePicker.on('change', function (e) {
                @this.set('date', e.target.value);
            });
        }
    </script>
@endscript
