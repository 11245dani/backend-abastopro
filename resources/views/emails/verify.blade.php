<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo - AbastoPro</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #99D7A9 0%, #7BC891 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 640px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header {
            background: linear-gradient(135deg, #99D7A9 0%, #85C795 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .logo {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            width: 80px;
            height: 80px;
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        
        .brand-name {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }
        
        .tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 300;
            position: relative;
            z-index: 2;
        }
        
        .content {
            padding: 50px 40px;
            background: white;
        }
        
        .greeting {
            font-size: 32px;
            font-weight: 600;
            color: #2D3748;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #99D7A9, #6BA577);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .welcome-text {
            font-size: 18px;
            color: #4A5568;
            margin-bottom: 24px;
        }
        
        .description {
            font-size: 16px;
            color: #718096;
            margin-bottom: 40px;
            line-height: 1.7;
        }
        
        .cta-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #99D7A9 0%, #7BC891 100%);
            color: white;
            padding: 18px 36px;
            text-decoration: none;
            border-radius: 16px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 12px 40px rgba(153, 215, 169, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .verify-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .verify-button:hover::before {
            left: 100%;
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 50px rgba(153, 215, 169, 0.5);
        }
        
        .verify-button:active {
            transform: translateY(-1px);
        }
        
        .security-note {
            background: linear-gradient(135deg, #F7FAFC 0%, #EDF2F7 100%);
            border-left: 4px solid #99D7A9;
            padding: 20px;
            border-radius: 12px;
            margin: 30px 0;
        }
        
        .security-note p {
            color: #4A5568;
            font-size: 14px;
            margin: 0;
        }
        
        .footer {
            background: #F8F9FA;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid rgba(153, 215, 169, 0.1);
        }
        
        .team-signature {
            color: #718096;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .contact-info {
            color: #A0AEC0;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #99D7A9, transparent);
            margin: 30px 0;
            border-radius: 1px;
        }
        
        /* Responsive Design */
        @media (max-width: 640px) {
            body {
                padding: 10px;
            }
            
            .email-container {
                margin: 20px auto;
                border-radius: 16px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .content {
                padding: 30px 25px;
            }
            
            .footer {
                padding: 25px 25px;
            }
            
            .greeting {
                font-size: 26px;
            }
            
            .verify-button {
                padding: 16px 32px;
                font-size: 15px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .content {
                background: #1A202C;
            }
            
            .description, .welcome-text {
                color: #CBD5E0;
            }
            
            .greeting {
                color: #E2E8F0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with branding -->
        <div class="header">
            <div class="brand-name">AbastoPro</div>
            <div class="tagline">Tu plataforma de abastecimiento inteligente</div>
        </div>
        
        <!-- Main content -->
        <div class="content">
            <h1 class="greeting">¡Hola {{ $usuario->nombre }}! 👋</h1>
            
            <p class="welcome-text">
                ¡Bienvenido a AbastoPro! Nos emociona tenerte como parte de nuestra comunidad.
            </p>
            
            <p class="description">
                Para completar tu registro y comenzar a disfrutar de todas las funcionalidades de nuestra plataforma, 
                necesitamos verificar tu dirección de correo electrónico. Solo te tomará un momento.
            </p>
            
            <div class="cta-container">
                <a href="{{ url('/verificar/' . $usuario->verification_token) }}" class="verify-button">
                     Verificar mi cuenta
                </a>
            </div>
            
            <div class="divider"></div>
            
            <div class="security-note">
                <p>
                    <strong>🔒 Nota de seguridad:</strong> Este enlace es único y personal. 
                    Si no creaste una cuenta en AbastoPro, puedes ignorar este mensaje de forma segura.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="team-signature">
                Con cariño, <br>
                <strong>El equipo de AbastoPro</strong>
            </p>
            
            <div class="contact-info">
                ¿Necesitas ayuda? Contáctanos en soporte@abastopro.com<br>
                © 2025 AbastoPro. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>