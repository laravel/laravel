@extends('templates.NiceAdmin.layout')

@section('mainstyle', 'margin-left:auto;margin-top:auto;')

@section('maincontent')
    <div class="container">

        <section class="section 2fa min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
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
                                        {{ trans("messages.auth.2fa.title") }}
                                    </h5>
                                    <p class="text-center small">{{ trans("messages.auth.2fa.hint") }}</p>
                                </div>

                                <form id="form" method="POST" action="{{ route('2fa.disable') }}"
                                    class="row g-3 needs-validation">

                                    @csrf

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input id="code" name="code" type="code" :value="old('code')"
                                                autocomplete="username"
                                                placeholder="{{ trans("messages.form.field.2fa.label") }}"
                                                class="form-control" required>
                                            <label for="code">{{ trans("messages.form.field.2fa.label") }}</label>
                                        </div>
                                    </div>

                                    <div class="col-8">
                                        <button class="btn btn-danger w-100"
                                            type="submit">{{ trans("messages.auth.2fa.button") }}</button>
                                    </div>

                                    <div class="col-4">
                                        <a href="{{ route('dashboard') }}"
                                            class="btn btn-secondary">{{ trans("messages.form.action.cancel.label") }}</a>
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