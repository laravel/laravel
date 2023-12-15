<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'current_password' => ['required', 'string', 'current_password'],
    'password' => ['required', 'string', Password::defaults(), 'confirmed'],
]);

$updatePassword = function () {
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('current_password', 'password', 'password_confirmation');

        throw $e;
    }

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    $this->reset('current_password', 'password', 'password_confirmation');

    $this->dispatch('password-updated');
};

?>

<section>
    <div class="card card-body border-0 shadow mb-4 p-5">
        <h2 class="h5 mb-4">{{ __('Update Password') }}</h2>
        <p class="text-secondary">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
        <form wire:submit="updatePassword">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                    <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('current_password')" class="mt-2 text-danger" />
                </div>  
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <x-input-label for="update_password_password" :value="__('New Password')" />
                        <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />                  
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <x-primary-button class="btn">{{ __('Save') }}</x-primary-button>
    
                <x-action-message class="text-success" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            </div>
        </form>
    </div>
</section>
