@extends('templates.NiceAdmin.layout')

@section('mainstyle', 'margin-left:auto;margin-top:auto;')

@section('maincontent')
    <div class="pagetitle">
        <h1>Verificación 2FA</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">2FA</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-header">Verificación en Dos Pasos</div>

                    <div class="card-body">
                        <p>Por favor, introduce el código de verificación de tu aplicación Google Authenticator:</p>

                        <form method="POST" action="{{ route('2fa.verify') }}" class="row g-3">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="code" name="code" required autofocus
                                        placeholder="Código de verificación">
                                    <label for="code">Código de verificación</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Verificar</button>
                            </div>
                        </form><!-- End floating Labels Form -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection