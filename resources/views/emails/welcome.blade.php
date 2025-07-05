<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido a TrainIA!</title>
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
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2d3748;
            text-align: center;
        }
        .welcome-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #4a5568;
            text-align: center;
        }
        .features {
            background-color: #f7fafc;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .features h3 {
            margin: 0 0 20px 0;
            color: #2c5282;
            font-size: 20px;
            text-align: center;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #2a4365;
            font-size: 16px;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list li:before {
            content: "✅ ";
            margin-right: 10px;
            font-weight: bold;
        }
        .cta-section {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            margin: 30px 0;
        }
        .cta-section h3 {
            margin: 0 0 15px 0;
            font-size: 22px;
        }
        .cta-section p {
            margin: 0 0 20px 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .cta-button {
            display: inline-block;
            background-color: white;
            color: #38a169;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .account-info {
            background-color: #ebf8ff;
            border-left: 4px solid #3182ce;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .account-info h4 {
            margin: 0 0 10px 0;
            color: #2c5282;
            font-size: 18px;
        }
        .account-info p {
            margin: 5px 0;
            color: #2a4365;
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
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .emoji {
            font-size: 24px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏋️ TrainIA</div>
            <h1>¡Bienvenido a TrainIA!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola <strong>{{ $user->name }}</strong>! <span class="emoji">🎉</span>
            </div>
            
            <div class="welcome-message">
                ¡Tu cuenta ha sido creada exitosamente! Estamos emocionados de tenerte como parte de la comunidad TrainIA.
            </div>
            
            <div class="account-info">
                <h4>📋 Información de tu cuenta:</h4>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Plan actual:</strong> {{ ucfirst($user->subscription_status) }}</p>
                <p><strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div class="features">
                <h3>🚀 ¿Qué puedes hacer con TrainIA?</h3>
                <ul class="feature-list">
                    <li>Entrenamientos personalizados con IA</li>
                    <li>Seguimiento de tu progreso</li>
                    <li>Ejercicios adaptados a tu nivel</li>
                    <li>Rutinas de entrenamiento inteligentes</li>
                    <li>Análisis de tu rendimiento</li>
                    <li>Comunidad de entrenadores</li>
                </ul>
            </div>
            
            <div class="cta-section">
                <h3>¡Comienza tu viaje fitness!</h3>
                <p>Tu cuenta está lista para usar. Accede ahora y descubre todo lo que TrainIA puede hacer por ti.</p>
                <a href="{{ config('app.url') }}" class="cta-button">🚀 Ir a TrainIA</a>
            </div>
            
            <div style="text-align: center; margin-top: 30px; color: #718096; font-size: 14px;">
                <p>¿Tienes preguntas? No dudes en contactarnos.</p>
                <p>¡Nos vemos en el gimnasio! 💪</p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>TrainIA</strong> - Tu entrenador personal con IA</p>
            <p>Este es un email automático, no respondas a este mensaje</p>
            <p>Si no solicitaste esta cuenta, contacta con soporte técnico</p>
        </div>
    </div>
</body>
</html> 