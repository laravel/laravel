<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
    <div class="container-fluid px-0">
      <div class="d-flex justify-content-end w-100" id="navbarSupportedContent">
            <a class="nav-link dropdown-toggle pt-1 px-0" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <div class="media d-flex align-items-center">
                <img class="avatar rounded-circle" alt="Image placeholder" src="{{ asset('/assets/images/team/profile-picture-1.jpg') }}">
                <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                  <span
                    class="mb-0 fw-bold text-secondary">{{  auth()->user()->name ? auth()->user()->name : 'User Name'}}</span>
                </div>
              </div>
            </a>
            <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
              <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}" wire:navigate>
                <svg class="dropdown-icon text-secondary mx-2" fill="currentColor" viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                    clip-rule="evenodd"></path>
                </svg>
                {{ __('My Profile') }}
              </a>
              <div role="separator" class="dropdown-divider my-1"></div>
              <a wire:click="logout" class="dropdown-item d-flex align-items-center">
                <!-- Authentication -->
                <svg class="dropdown-icon text-danger me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                {{ __('Log Out') }}  
              </a>
            </div>
      </div>
    </div>
  </nav>
