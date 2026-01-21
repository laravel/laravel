<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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
            max-width: 700px;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th {
            background-color: #ecf0f1;
            color: #2c3e50;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            border-bottom: 1px solid #bdc3c7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
            color: #2c3e50;
            font-size: 14px;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #3498db;
            border-radius: 3px;
            font-size: 13px;
            display: inline-block;
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
        
        p {
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Panel de Administración - Usuarios</h2>

        @if($users->isEmpty())
            <p>No hay usuarios registrados.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->nombre }}</td>
                            <td>{{ $user->apellidos }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role === 'admin' ? 'Administrador' : 'Usuario' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="actions">
            <a href="/">Inicio</a>
            <a href="/perfil">Mi Perfil</a>
            <form method="POST" action="/logout" class="logout-form">
                <button type="submit">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
