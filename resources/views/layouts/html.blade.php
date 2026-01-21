@php
    use Nwidart\Modules\Facades\Module;
    use Illuminate\Support\Facades\File;
@endphp

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
    <script src="{{ asset('zentro/extra/toastr/toastr.min.js') }}"></script>
    <!-- Zentro Web3Modal -->
    <script src="{{ asset('zentro/extra/web3.min.js') }}"></script>
    <!-- Zentro Utilities -->
    <script src="{{ asset('zentro/zentro.js') }}"></script>
    <!-- <script src="{{ asset('zentro/walletconnect.js') }}" type="module"></script> -->
    <!-- Loaging GLOBAL config -->
    <script type="text/javascript">
        var config = @json(config('metadata.cripto'));
    </script>

    <!-- Tabler style -->
    <link href="{{ asset('zentro/extra/tablericons/tabler-icons.min.css') }}" rel="stylesheet">

    <!-- Inclusion dinamica de recursos de los Modulos Activos -->
    @foreach(Module::allEnabled() as $module)
        @php
            $moduleName = $module->getName();
            $moduleLower = strtolower($moduleName);
            // Ruta física a la carpeta include
            $path = "Modules/{$moduleName}/Resources/views/include";
            $includePath = base_path($path);
        @endphp

        @if(File::isDirectory($includePath))
            @php
                // Obtenemos todos los archivos .blade.php de forma recursiva
                $files = File::allFiles($includePath);
            @endphp

            @foreach($files as $file)
                @php
                    // 1. Obtenemos la ruta relativa: "css/estilos.blade.php"
                    $relativePath = $file->getRelativePathname();
                    echo "<!-- {$path}/{$relativePath} -->\n";

                    // 2. Quitamos la extensión ".blade.php"
                    $viewPartial = str_replace('.blade.php', '', $relativePath);

                    // 3. Convertimos barras en puntos para Laravel: "css.estilos"
                    $viewPartial = str_replace(DIRECTORY_SEPARATOR, '.', $viewPartial);

                    // 4. Construimos el nombre completo: "gutotradebot::include.css.estilos"
                    $fullViewName = "{$moduleLower}::include.{$viewPartial}";
                @endphp
                @includeIf($fullViewName)
            @endforeach
        @endif
    @endforeach

</head>

<body id="@yield('bodyid')" class="@yield('bodycss')">

    <x-auth-session-status class="mb-4" :status="session('status')" id="sessionStatus" style="visibility: hidden;" />

    @yield('content')

    @yield('jsincludes')

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