<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;

state([
    'name' => fn () => auth()->user()->name,
    'email' => fn () => auth()->user()->email
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
    ]);

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $path = session('url.intended', RouteServiceProvider::HOME);

        $this->redirect($path);

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>

<section>
    <div class="card card-body border-0 shadow mb-4 p-5">
        <h2 class="h5 mb-4">{{ __('Profile Information') }}</h2>
        <form wire:submit="updateProfileInformation">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input wire:model="name" id="name" name="name" type="text" class="form-control" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>  
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input wire:model="email" id="email" name="email" type="email" class="form-control" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />

                            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}
                    
                        <button wire:click.prevent="sendVerification" class="btn btn-link text-sm text-gray-600">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif                    
                </div>
            @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <x-primary-button class="btn">{{ __('Save') }}</x-primary-button>

                <x-action-message class="text-success" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            </div>
        </form>
    </div>
</section>
