<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state(['password' => '']);

rules(['password' => ['required', 'string', 'current_password']]);

$deleteUser = function (Logout $logout) {
    $this->validate();

    tap(Auth::user(), $logout(...))->delete();

    $this->redirect('/', navigate: true);
};

?>

<section>
    <div class="card card-body border-0 shadow mb-4 p-5">
        <h2 class="h5 mb-4">{{ __('Delete Account') }}</h2>
        <p class="text-secondary">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
        <x-danger-button class="btn btn-danger"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Delete Account') }}</x-danger-button>

        <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
            <form wire:submit="deleteUser" class="p-6">
    
                <h2 class="text-secondary">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>
    
                <p class="text-secondary">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
    
                <div class="mt-3">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
    
                    <x-text-input
                        wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        class="form-control"
                        placeholder="{{ __('Password') }}"
                    />
    
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
    
                <div class="mt-3 d-flex justify-content-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
    
                    <x-danger-button class="btn btn-danger">
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
        
    </div>
</section>
