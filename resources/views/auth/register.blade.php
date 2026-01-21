<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .container {
            max-width: 380px;
            margin: 40px auto;
            background: white;
            padding: 35px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
        }
        
        input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 3px;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }
        
        input:focus {
            outline: none;
            border-color: #3498db;
        }
        
        button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            margin-top: 8px;
            transition: background-color 0.2s ease;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .errors {
            color: #e74c3c;
            margin-bottom: 18px;
            padding: 12px;
            background-color: #fadbd8;
            border-left: 3px solid #e74c3c;
            border-radius: 2px;
            font-size: 13px;
        }
        
        .errors ul {
            margin: 0;
            padding-left: 18px;
        }
        
        .errors li {
            margin-bottom: 5px;
        }
        
        .link {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
        }
        
        .link a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .link a:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registrarse</h2>

        @if($errors->any())
            <div class="errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/register">
            <div class="form-group">
                <label for="name">Usuario</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <div class="form-group">
                <label for="role">Rol</label>
                <select id="role" name="role" required style="width: 100%; padding: 9px 12px; border: 1px solid #bdc3c7; border-radius: 3px; font-size: 14px;">
                    <option value="user" selected>Usuario Regular</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <button type="submit">Registrarse</button>
        </form>

        <div class="link">
            <a href="/login">Iniciar Sesión</a>
        </div>
    </div>
</body>
</html>
