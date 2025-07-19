@extends('templates.NiceAdmin.layout')

@section('mainstyle', 'margin-left:auto;margin-top:auto;')

@section('maincontent')
    <div class="container">

        <section
            class="section forgotpassword min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            @include("templates.NiceAdmin.logo")
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">
                                        {{ trans("messages.auth.forgotpassword.title") }}
                                    </h5>
                                    <p class="text-center small">{{ trans("messages.auth.forgotpassword.hint") }}</p>
                                </div>

                                <form id="form" method="POST" action="{{ route('password.email') }}"
                                    class="row g-3 needs-validation">

                                    @csrf

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="email" name="email" type="email" :value="old('email')"
                                                autocomplete="username"
                                                placeholder="{{ trans("messages.form.field.email.label") }}"
                                                class="form-control" required>
                                            <label for="code">{{ trans("messages.form.field.email.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100"
                                            type="submit">{{ trans("messages.auth.forgotpassword.button") }}</button>
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