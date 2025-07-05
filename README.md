# 🏋️ TrainIA Backend

Backend API para aplicación de entrenamiento con IA, desarrollado con Laravel 11.

## 🚀 Características

- **Autenticación completa**: Registro, login, logout con Sanctum
- **Gestión de perfil**: Actualización de datos y avatar
- **Sistema de archivos**: Subida y gestión de avatares
- **Recuperación de contraseña**: Email con contraseña temporal
- **Cambio de contraseña**: Con validación de fortaleza
- **Eliminación de cuenta**: Con confirmación y limpieza de datos
- **Emails automáticos**: Bienvenida y recuperación de contraseña

## 📋 Requisitos

- PHP 8.2+
- Laravel 11
- SQLite (desarrollo) / MySQL/PostgreSQL (producción)
- Composer

## 🛠️ Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd TrainIA/Backend
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**
```bash
# Para SQLite (desarrollo)
touch database/database.sqlite
# O configurar MySQL/PostgreSQL en .env

php artisan migrate
```

5. **Configurar storage**
```bash
php artisan storage:link
```

6. **Configurar email (opcional)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
```

## 🚀 Uso

### Iniciar servidor
```bash
php artisan serve
```

### Ejecutar tests
```bash
php artisan test
```

### Limpiar archivos huérfanos
```bash
php artisan files:clean-orphans
```

## 📚 API Endpoints

### Autenticación
- `POST /api/register` - Registrar usuario
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión (requiere auth)

### Perfil de Usuario
- `GET /api/user` - Obtener perfil actual (requiere auth)
- `POST /api/profile/update` - Actualizar perfil (requiere auth)

### Gestión de Contraseñas
- `POST /api/forgot-password` - Solicitar recuperación
- `POST /api/reset-password` - Resetear contraseña
- `POST /api/change-password` - Cambiar contraseña (requiere auth)

### Eliminación de Cuenta
- `GET /api/account/deletion-warning` - Obtener advertencia (requiere auth)
- `DELETE /api/account` - Eliminar cuenta (requiere auth)

### Gestión de Archivos
- `GET /api/files` - Listar archivos del usuario (requiere auth)
- `DELETE /api/files/{id}` - Eliminar archivo (requiere auth)

## 🔧 Arquitectura

### Estructura de Directorios
```
app/
├── Http/
│   ├── Controllers/     # Controladores de la API
│   └── Middleware/      # Validaciones y middleware
├── Models/              # Modelos Eloquent
├── Services/            # Lógica de negocio
└── Mail/               # Templates de email
```

### Patrones Utilizados
- **Service Layer**: Lógica de negocio en servicios dedicados
- **Repository Pattern**: Acceso a datos a través de modelos
- **Middleware Pattern**: Validaciones específicas por operación
- **Observer Pattern**: Eventos automáticos (emails)

## 🔒 Seguridad

- Autenticación con Laravel Sanctum
- Validación robusta de entrada
- Hash seguro de contraseñas
- Revocación automática de tokens
- Logging de auditoría
- Protección CSRF

## 📝 Logging

El sistema registra automáticamente:
- Cambios de contraseña
- Eliminación de cuentas
- Errores de email
- Acciones de archivos

Logs disponibles en: `storage/logs/laravel.log`

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=AuthTest
```

## 📦 Despliegue

1. Configurar variables de entorno de producción
2. Ejecutar `composer install --optimize-autoloader --no-dev`
3. Ejecutar `php artisan config:cache`
4. Ejecutar `php artisan route:cache`
5. Configurar web server (Nginx/Apache)

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.
