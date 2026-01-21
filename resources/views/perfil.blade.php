<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
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
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .info {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .info:last-of-type {
            border-bottom: none;
        }
        
        .label {
            color: #7f8c8d;
            font-weight: 500;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .value {
            color: #2c3e50;
            margin-top: 6px;
            display: block;
            font-size: 15px;
        }
        
        .actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .link {
            display: inline-block;
        }
        
        a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #3498db;
            border-radius: 3px;
            font-size: 13px;
            transition: background-color 0.2s ease;
        }
        
        a:hover {
            background-color: #2980b9;
        }
        
        .logout-form {
            display: inline;
        }
        
        button {
            padding: 8px 16px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            transition: background-color 0.2s ease;
        }
        
        button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mi Perfil</h2>

        <div class="info">
            <span class="label">Nombre:</span>
            <span class="value">{{ Auth::user()->nombre }}</span>
        </div>

        <div class="info">
            <span class="label">Apellidos:</span>
            <span class="value">{{ Auth::user()->apellidos }}</span>
        </div>

        <div class="info">
            <span class="label">Correo:</span>
            <span class="value">{{ Auth::user()->email }}</span>
        </div>

        <div class="info">
            <span class="label">Rol:</span>
            <span class="value">{{ Auth::user()->role === 'admin' ? 'Administrador' : 'Usuario' }}</span>
        </div>

        <div class="actions">
            <div class="link"><a href="/">Inicio</a></div>
            <form method="POST" action="/logout" class="logout-form">
                <button type="submit">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
