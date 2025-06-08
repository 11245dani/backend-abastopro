<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contraseña Restablecida</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .message-container {
            background-color: #fff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #99D7A9;
            margin-bottom: 1rem;
        }

        p {
            color: #555;
            margin-bottom: 2rem;
        }

        .btn-login {
            display: inline-block;
            background-color: #99D7A9;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color:rgb(158, 211, 171);
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>¡Contraseña actualizada!</h2>
        <p>Tu contraseña ha sido restablecida correctamente. Ya puedes iniciar sesión con tu nueva contraseña.</p>
        <a href="/login" class="btn-login">Ir a iniciar sesión</a>
    </div>
</body>
</html>