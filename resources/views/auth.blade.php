<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neurocogniciones - Acceso</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head> 
<body>
    <div class="container">
        <section class="hero-section">
            <div class="hero-overlay">
                <h1>Neurocogniciones</h1>
                <p>Inicia sesion o registrate para comenzar a aprender</p>
                </div>
        </section>

        <section class="form-section">
            <div id="login-form-container" class="form-container active">
                <h2>Iniciar Sesión</h2>
                <form action="/login" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn-submit">Ingresar</button>
                </form>
                <a class="btn-toggle" id="show-register">¿No tienes cuenta? Regístrate aquí.</a>
            </div>

            <div id="register-form-container" class="form-container">
                <h2>Crear Cuenta</h2>
                <form action="/register" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Nombre completo" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
                    </div>
                    <button type="submit" class="btn-submit">Registrarse</button>
                </form>
                <a class="btn-toggle" id="show-login">¿Ya tienes cuenta? Inicia sesión.</a>
            </div>
        </section>
    </div>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>