<!DOCTYPE html>
<html lang="es">

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Includes -->
    @yield('cssincludes')

    <!-- MANDATORY Here -->
    <script src="{{ asset('zentro/extra/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('zentro/extra/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Tabler style -->
    <link href="{{ asset('zentro/extra/tablericons/tabler-icons.min.css') }}" rel="stylesheet">


</head>

<body id="@yield('bodyid')" class="@yield('bodycss')">

    <x-auth-session-status class="mb-4" :status="session('status')" id="sessionStatus" style="visibility: hidden;" />

    @yield('content')

    @yield('jsincludes')

    <!-- Toastr & Swal Message-->

    <script>

        $(document).ready(function () {
            @yield('ondocumentready')

            @if($errors->any() || session('error'))
                Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                }).fire({
                    icon: "error",
                    title: "{!! session('error') ?: implode('<br>', $errors->all()) !!}",
                });
            @endif

            @if(session('success'))
                Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                }).fire({
                    icon: "success",
                    title: "{{ session('success') }}",
                });
            @endif

        const status = $('#sessionStatus').text().trim();
            if (status) {
                console.log('Status:', status);
                // Tu lógica aquí

                Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                }).fire({
                    icon: "success",
                    title: status
                });
            }

        });
    </script>

</body>

</html>