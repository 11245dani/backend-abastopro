<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifica tu nuevo correo</title>
</head>
<body>
    <p>Hola {{ $nombre }},</p>

    <p>Has solicitado actualizar tu dirección de correo electrónico. Para continuar usando la plataforma, debes confirmar tu nuevo correo.</p>

    <p>
        <a href="{{ $url }}">Haz clic aquí para verificar tu nuevo correo</a>
    </p>

    <p>Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>

    <p>Gracias,<br>El equipo de Abastopro</p>
</body>
</html>
