<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseña Temporal - TrainIA</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2d3748;
        }
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            color: #4a5568;
        }
        .password-box {
            background-color: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .password-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .password {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            font-family: 'Courier New', monospace;
            background-color: #edf2f7;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            letter-spacing: 2px;
        }
        .instructions {
            background-color: #ebf8ff;
            border-left: 4px solid #3182ce;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .instructions h3 {
            margin: 0 0 10px 0;
            color: #2c5282;
            font-size: 16px;
        }
        .instructions ul {
            margin: 0;
            padding-left: 20px;
            color: #2a4365;
        }
        .instructions li {
            margin-bottom: 5px;
        }
        .warning {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #c53030;
            font-size: 14px;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 5px 0;
            color: #718096;
            font-size: 14px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .expiry {
            background-color: #fef5e7;
            border: 1px solid #f6ad55;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #c05621;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏋️ TrainIA</div>
            <h1>Contraseña Temporal</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hola <strong>{{ $user->name }}</strong>,
            </div>
            
            <div class="message">
                Has solicitado restablecer tu contraseña en TrainIA. Te hemos generado una contraseña temporal que puedes usar para acceder a tu cuenta.
            </div>
            
            <div class="password-box">
                <div class="password-label">Tu contraseña temporal es:</div>
                <div class="password">{{ $temporaryPassword }}</div>
            </div>
            
            <div class="expiry">
                ⏰ Esta contraseña temporal expira en <strong>1 hora</strong>
            </div>
            
            <div class="instructions">
                <h3>📋 Instrucciones:</h3>
                <ul>
                    <li>Usa esta contraseña temporal para iniciar sesión en TrainIA</li>
                    <li>Una vez dentro, ve a tu perfil y cambia la contraseña por una nueva</li>
                    <li>La contraseña temporal solo funcionará durante 1 hora</li>
                    <li>Si no solicitaste este cambio, ignora este email</li>
                </ul>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Importante:</strong> Por seguridad, cambia tu contraseña inmediatamente después de iniciar sesión. Esta contraseña temporal es solo para acceso de emergencia.
            </div>
        </div>
        
        <div class="footer">
            <p><strong>TrainIA</strong> - Tu entrenador personal con IA</p>
            <p>Este es un email automático, no respondas a este mensaje</p>
            <p>Si tienes problemas, contacta con soporte técnico</p>
        </div>
    </div>
</body>
</html> 