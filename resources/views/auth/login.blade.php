@extends('templates.NiceAdmin.layout')

@section('mainstyle', 'margin-left:auto;margin-top:auto;')

@section('layoutjsincludes')
    @include('web3::include.script.wallet_actions')
@endsection

@section('layoutcssincludes')
    @include('web3::include.css.styles')
@endsection

@section('maincontent')
    <!-- Icono QR flotante -->
    <div class="qr-flotante" data-bs-toggle="popover" data-bs-placement="left"
        data-bs-content="<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=Ejemplo'"
        data-bs-html="true">
        <a class="nav-link scrollto" href="#web3" onclick="window.web3Modal.openModal()">
            <i class="bi bi-qr-code" style="font-size: 1.5rem;"></i>
        </a>
    </div>

    <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            @include("templates.NiceAdmin.logo")
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">{{ trans("messages.auth.login.title") }}
                                    </h5>
                                    <p class="text-center small">{{ trans("messages.auth.login.hint") }}</p>
                                </div>

                                <form method="POST" action="{{ route('login') }}" class="row g-3 needs-validation">

                                    @csrf

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="email" name="email" type="email" :value="old('email')"
                                                autocomplete="username"
                                                placeholder="{{ trans("messages.form.field.email.label") }}"
                                                class="form-control" required autofocus>
                                            <label for="code">{{ trans("messages.form.field.email.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="password" name="password" type="password"
                                                autocomplete="current-password"
                                                placeholder="{{ trans("messages.form.field.password.label") }}"
                                                class="form-control" required>
                                            <label for="code">{{ trans("messages.form.field.password.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" value="true"
                                                id="remember_me">
                                            <label class="form-check-label"
                                                for="rememberMe">{{ trans("messages.form.field.rememberme.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100"
                                            type="submit">{{ trans("messages.form.field.login.label") }}</button>
                                    </div>

                                    <div class="col-12">

                                        @if (Route::has('password.request'))

                                            <p class="small mb-0">

                                                <a href="{{ route('password.request') }}">
                                                    {{ trans("messages.form.field.forgotpassword.label") }}
                                                </a>
                                            </p>
                                            <br />
                                        @endif
                                        <p class="small mb-0">{{ trans("messages.form.field.donthaveaccount.label") }} <a
                                                href="{{ route('register') }}">
                                                {{ trans("messages.form.field.createaccount.label") }}
                                            </a></p>
                                        <br />

                                        <p class="small mb-0"><a href="{{ route('auth.google') }}"><i
                                                    class="bi bi-google"></i>
                                                {{ trans("messages.form.field.loginwithgoogle.label") }}</a></p>
                                    </div>

                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>

    </div>
@endsection

<script>
    @section('ondocumentready')
        // INIT web3modal configurations:
        initializeWeb3Modal(function (account, size = 8, callback = false) {
            onWalletConnected(account, size, callback);
        }, onWalletDisconnected, function (account) {
            checkIsRegistered(account, function () {
                window.location.href = "{{ route('dashboard') }}";
            }, function () {
                register(account, "{{ request()->query('code') }}", function () {
                    window.location.href = "{{ route('dashboard') }}";
                });
            });
        });
    @endsection
</script>