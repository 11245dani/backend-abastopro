<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación Completada - AbastoPro</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #99D7A9 0%, #7BC891 50%, #85C795 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated background elements */
        .bg-decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s infinite ease-in-out;
        }
        
        .bg-decoration:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }
        
        .bg-decoration:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation-delay: 2s;
        }
        
        .bg-decoration:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 20%;
            left: 10%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) scale(1);
                opacity: 0.1;
            }
            50% { 
                transform: translateY(-30px) scale(1.1);
                opacity: 0.2;
            }
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .status-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #99D7A9, #7BC891);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            box-shadow: 0 15px 40px rgba(153, 215, 169, 0.4);
            animation: pulse 2s infinite;
            position: relative;
        }
        
        .status-icon::before {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            border: 2px solid rgba(153, 215, 169, 0.3);
            border-radius: 50%;
            animation: ripple 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }
        
        .message {
            font-size: 28px;
            font-weight: 600;
            color: #2D3748;
            margin-bottom: 20px;
            line-height: 1.3;
            background: linear-gradient(135deg, #99D7A9, #6BA577);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .description {
            font-size: 16px;
            color: #718096;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .actions {
            margin-top: 40px;
        }
        
        .close-button {
            background: linear-gradient(135deg, #99D7A9 0%, #7BC891 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
            position: relative;
            overflow: hidden;
            margin-right: 16px;
        }
        
        .close-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .close-button:hover::before {
            left: 100%;
        }
        
        .close-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(153, 215, 169, 0.4);
        }
        
        .close-button:active {
            transform: translateY(-1px);
        }
        
        .secondary-button {
            background: transparent;
            color: #99D7A9;
            border: 2px solid #99D7A9;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .secondary-button:hover {
            background: #99D7A9;
            color: white;
            transform: translateY(-1px);
        }
        
        .brand-footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(153, 215, 169, 0.2);
        }
        
        .brand-name {
            color: #99D7A9;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .brand-tagline {
            color: #A0AEC0;
            font-size: 14px;
        }
        
        /* Auto-close countdown */
        .countdown {
            background: rgba(153, 215, 169, 0.1);
            border-radius: 8px;
            padding: 12px 20px;
            margin-top: 20px;
            font-size: 14px;
            color: #4A5568;
        }
        
        .countdown-number {
            font-weight: 600;
            color: #99D7A9;
        }
        
        /* Responsive design */
        @media (max-width: 640px) {
            .container {
                padding: 40px 30px;
                margin: 20px;
            }
            
            .message {
                font-size: 24px;
            }
            
            .status-icon {
                width: 80px;
                height: 80px;
                font-size: 40px;
            }
            
            .close-button, .secondary-button {
                width: 100%;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Background decorations -->
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <!-- Dynamic status icon based on message -->
        <div class="status-icon">
            <span id="statusEmoji">✅</span>
        </div>
        
        <!-- Main message -->
        <h1 class="message" id="mainMessage">{{ $mensaje }}</h1>
        
        <p class="description">
            Tu verificación se ha procesado correctamente. 
            Esta ventana se cerrará automáticamente en unos segundos.
        </p>
        
        <!-- Auto-close countdown -->
        <div class="countdown">
            <span>Esta ventana se cerrará automáticamente en </span>
            <span class="countdown-number" id="countdown">10</span>
            <span> segundos</span>
        </div>
        
        <!-- Brand footer -->
        <div class="brand-footer">
            <div class="brand-name">AbastoPro</div>
            <div class="brand-tagline">Tu plataforma de abastecimiento inteligente</div>
        </div>
    </div>

    <script>
        // Auto-close functionality
        let timeLeft = 10;
        const countdownElement = document.getElementById('countdown');
        const statusEmoji = document.getElementById('statusEmoji');
        const mainMessage = document.getElementById('mainMessage');
        
        // Determine status icon based on message content
        function updateStatusIcon() {
            const message = mainMessage.textContent.toLowerCase();
            if (message.includes('error') || message.includes('falló') || message.includes('problema')) {
                statusEmoji.textContent = '❌';
                document.querySelector('.status-icon').style.background = 'linear-gradient(135deg, #FEB2B2, #F56565)';
            } else if (message.includes('éxito') || message.includes('verificado') || message.includes('completado')) {
                statusEmoji.textContent = '✅';
            } else {
                statusEmoji.textContent = 'ℹ️';
                document.querySelector('.status-icon').style.background = 'linear-gradient(135deg, #90CDF4, #3182CE)';
            }
        }
        
        // Countdown timer
        const countdown = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                closeWindow();
            }
        }, 1000);
        
        // Functions
        function closeWindow() {
            // Try to close the window
            if (window.opener) {
                window.close();
            } else {
                // If can't close, redirect to main app or show message
                alert('Por favor, cierra esta pestaña manualmente.');
            }
        }
        
        function goToApp() {
            // Redirect to main application
            window.location.href = '/'; // Adjust this URL as needed
        }
        
        // Pause countdown on hover
        const container = document.querySelector('.container');
        container.addEventListener('mouseenter', () => {
            clearInterval(countdown);
            countdownElement.parentElement.innerHTML = '<span>Temporizador pausado - Mueve el cursor para reanudar</span>';
        });
        
        container.addEventListener('mouseleave', () => {
            location.reload(); // Restart the page to resume countdown
        });
        
        // Initialize
        updateStatusIcon();
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeWindow();
            }
        });
    </script>
</body>
</html>