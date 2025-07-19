@extends('templates.NiceAdmin.layout')

@section('mainstyle', 'margin-left:auto;margin-top:auto;')

@section('maincontent')
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
                                    <h5 class="card-title text-center pb-0 fs-4">{{ trans("messages.auth.register.title") }}
                                    </h5>
                                    <p class="text-center small">{{ trans("messages.auth.register.hint") }}</p>
                                </div>

                                <form id="form" method="POST" action="{{ route('register') }}"
                                    class="row g-3 needs-validation">

                                    @csrf

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="name" name="name"
                                                placeholder="{{ trans("messages.form.field.name.label") }}"
                                                class="form-control" required autofocus>
                                            <label for="code">{{ trans("messages.form.field.name.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="email" name="email" type="email" :value="old('email')"
                                                autocomplete="username"
                                                placeholder="{{ trans("messages.form.field.email.label") }}"
                                                class="form-control" required>
                                            <label for="code">{{ trans("messages.form.field.email.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="password" name="password" type="password"
                                                autocomplete="current-password"
                                                placeholder="{{ trans("messages.form.field.password.label") }}"
                                                class="form-control" required>
                                            <label for="password">{{ trans("messages.form.field.password.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3"> <!-- Añadido margen superior -->
                                        <div class="form-floating">
                                            <input id="password_confirmation" name="password_confirmation" type="password"
                                                autocomplete="new-password"
                                                placeholder="{{ trans('messages.form.field.passwordconfirmation.label') }}"
                                                class="form-control" required>
                                            <label
                                                for="password_confirmation">{{ trans('messages.form.field.passwordconfirmation.label') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100"
                                            type="submit">{{ trans("messages.form.field.register.label") }}</button>
                                    </div>

                                    <div class="col-12">
                                        <p class="small mb-0">{{ trans("messages.form.field.alreadyhaveaccount.label") }} <a
                                                href="{{ route('login') }}">
                                                {{ trans("messages.form.field.loginaccount.label") }}
                                            </a></p>
                                        <br />

                                        <p class="small mb-0"><a href="{{ route('auth.google') }}"><i
                                                    class="bi bi-google"></i> Acceder usando mi cuenta Google</a></p>
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
        $('#form').on('keyup', '#password, #password_confirmation', function () {
            validatePasswords();
        });

        // Validación al enviar el formulario
        $('#form').submit(function (e) {
            if (!validatePasswords()) {
                e.preventDefault(); // Evita el envío si no coinciden
            }
        });

        function validatePasswords() {
            const password = $('#password').val();
            const passwordConfirm = $('#password_confirmation').val();
            const errorDiv = $('#password_error');

            if (password !== passwordConfirm && passwordConfirm.length > 0) {
                $('#password_confirmation').addClass('is-invalid');
                errorDiv.show();
                return false;
            } else {
                $('#password_confirmation').removeClass('is-invalid');
                errorDiv.hide();
                return true;
            }
        }
    @endsection
</script>