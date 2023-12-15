<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirect(
        session('url.intended', RouteServiceProvider::HOME),
        navigate: true
    );
};

?>
<main>
    <!-- Section -->
    <section class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
        <div class="container">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <p class="text-center">
                <a href="/" class="d-flex align-items-center justify-content-center">
                    <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ __('Back to homepage') }}
                </a>
            </p>
            <div class="row justify-content-center form-bg-image">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                        <div class="text-center text-md-center mb-4 mt-md-0">
                            <h1 class="mb-0 h3">Welcome back</h1>
                        </div>
                        <form wire:submit="login" class="mt-4">
                            <!-- Email Address -->
                            <div class="form-group mb-4">
                                <x-input-label for="email" :value="__('Email')" />

                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z">
                                            </path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                        </svg>
                                    </span>
                                    <x-text-input wire:model="form.email" id="email" class="form-control" type="email" name="email" autofocus autocomplete="username" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                            </div>
                            <!-- Password -->
                            <div class="form-group mb-4">
                                <x-input-label for="password" :value="__('Password')" />
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon2">
                                        <svg class="icon icon-xs text-secondary" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <x-text-input wire:model="form.password" id="password" class="form-control"
                                    type="password"
                                    name="password"
                                    autocomplete="current-password" />
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                            </div>

                            <!-- Remember Me -->
                            <div class="form-group mb-4">
                                <div class="form-check">
                                    <input wire:model="form.remember" class="form-check-input" type="checkbox" value="" id="remember">
                                    <label class="form-check-label mb-0" for="remember">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>
                            </div> 
                                <x-primary-button class="d-grid">
                                    {{ __('Login') }}
                                </x-primary-button>                         
                        </form> 
                        <div class="d-flex justify-content-center align-items-center mt-4">
                            <span class="fw-normal">
                                {{ __('Not registered?') }}
                                <a href="{{ route('register') }}" class="fw-bold">Create account</a>
                            </span>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" wire:navigate>
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>