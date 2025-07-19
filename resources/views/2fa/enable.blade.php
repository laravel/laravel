@extends('templates.NiceAdmin.layout')

@section('topbar')
    @include('templates.NiceAdmin.topbar')
@endsection

@section('mainstyle', 'margin-left:auto;')

@section('maincontent')
    <div class="pagetitle">
        <h1>Habilitar 2FA</h1>
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
                    <div class="card-header">Habilitar Autenticación en Dos Pasos</div>

                    <div class="card-body">
                        <p>Escanea este código QR con la aplicación Google Authenticator:</p>
                        <div class="text-center mb-4">
                            {!! $qrCodeUrl !!}
                        </div>
                        <p>O introduce manualmente el código: <strong>{{ $secret }}</strong> en esa aplicación.</p>
                        <p>Teclee el código de verificación generado por el Google Authenticator a continuación:</p>
                        <form method="POST" action="{{ route('2fa.enable') }}" class="row g-3">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="code" name="code" required autofocus
                                        placeholder="Código de verificación">
                                    <label for="code">Código de verificación</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Habilitar 2FA</button>
                            </div>
                        </form><!-- End floating Labels Form -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection