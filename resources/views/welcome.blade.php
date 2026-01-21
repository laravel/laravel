<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenida</title>
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
        margin: 60px auto;
        background: white;
        padding: 40px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        text-align: center;
    }
    
    h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 26px;
        font-weight: 600;
    }
    
    p {
        color: #7f8c8d;
        margin-bottom: 30px;
        font-size: 15px;
        line-height: 1.5;
    }
    
    .buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .btn-primary, .btn-secondary {
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 3px;
        font-weight: 500;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-block;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #2980b9;
    }
    
    .btn-secondary {
        background-color: #95a5a6;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #7f8c8d;
    }
    
    .auth-msg {
        padding: 20px;
        background-color: #ecf0f1;
        border-left: 3px solid #3498db;
        border-radius: 2px;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    
    .auth-msg p {
        margin-bottom: 15px;
    }
    
    .auth-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
    }
    
    .auth-buttons a {
        padding: 8px 16px;
        background-color: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 3px;
        display: inline-block;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    
    .auth-buttons a:hover {
        background-color: #2980b9;
    }
    
    .auth-buttons button {
        padding: 8px 16px;
        background-color: #e74c3c;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    
    .auth-buttons button:hover {
        background-color: #c0392b;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Sistema de Usuarios</h1>
    
    @auth
        <div class="auth-msg">
            <p>¡Hola {{ Auth::user()->nombre }}! Ya estás logueado.</p>
            <div class="auth-buttons">
                @if(Auth::user()->role === 'admin')
                    <a href="/admin/users">Panel Admin</a>
                @else
                    <a href="/perfil">Mi Perfil</a>
                @endif
                <form method="POST" action="/logout" style="display: inline;">
                    <button type="submit" style="padding: 8px 16px; background-color: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    @else
        <p>Bienvenido al sistema de gestión de usuarios</p>
        <div class="buttons">
            <a href="/login" class="btn-primary">Iniciar Sesión</a>
            <a href="/register" class="btn-secondary">Registrarse</a>
        </div>
    @endauth
</div>
</body>
</html>
