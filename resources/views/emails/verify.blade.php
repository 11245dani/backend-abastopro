<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifica tu correo</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px;">
        <h2 style="color: #333;">¡Hola {{ $usuario->nombre }}!</h2>
        <p>Gracias por registrarte en Abastopro.</p>
        <p>Por favor, verifica tu correo electrónico haciendo clic en el siguiente botón:</p>
        <p>Token: {{ $usuario->verification_token }}</p>
        <a href="{{ url('/verificar/' . $usuario->verification_token) }}"
   style="display:inline-block; background-color:#28a745; color:#fff; padding:12px 24px; text-decoration:none; border-radius:5px;">
   Verificar cuenta
</a>

        <p style="margin-top: 20px;">Si no te registraste, puedes ignorar este mensaje.</p>
        <p style="color: #888;">Equipo Abastopro</p>
    </div>
</body>
</html>


