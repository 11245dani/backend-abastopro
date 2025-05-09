<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablece tu contraseña</title>
</head>
<body>
    <h2>Hola</h2>

    <p>Recibiste este correo porque solicitaste cambiar tu contraseña en Abastopro.</p>

    <p>Haz clic en el siguiente enlace para restablecerla:</p>

    <p>
        <a href="{{ $url }}" style="display: inline-block; background-color: #38bdf8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Restablecer contraseña
        </a>
    </p>

    <p>Este enlace expirará en 60 minutos.</p>

    <p>Si no solicitaste este cambio, ignora este mensaje.</p>

    <hr>
    <p style="font-size: 12px; color: #777;">Abastopro</p>
</body>
</html>
