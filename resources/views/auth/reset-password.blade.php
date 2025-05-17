<!-- resources/views/auth/reset-password.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h2>Restablecer tu contraseña</h2>

    @if (session('status'))
        <div style="color: green;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/reset-password') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label>Correo electrónico:</label><br>
       <input type="email" name="email" value="{{ old('email', $email ?? '') }}"  required><br><br>

        <label>Nueva contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirmar contraseña:</label><br>
        <input type="password" name="password_confirmation" required><br><br>

        <button type="submit">Restablecer contraseña</button>
    </form>
</body>
</html>