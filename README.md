# JAT2025 - Sistema de Preinscripciones

Sistema web para gestión de preinscripciones al evento "Jornada de Actualización Tecnológica 2025" del IEST La Recoleta.

## 🚀 Características

- ✅ Formulario de preinscripción con validación
- ✅ Envío automático de email de confirmación
- ✅ Panel de administración
- ✅ Generación de certificados en PDF
- ✅ Gestión de participantes
- ✅ Sistema de logs

## 📋 Requisitos

- PHP 8.2+
- MySQL 5.7+
- Servidor web (Apache/Nginx)
- Composer (para PHPMailer)

## ⚙️ Instalación

### 1. Clonar repositorio
```bash
git clone [tu-repo]
cd JAT2025
```

### 2. Configurar base de datos
```bash
# Importar jat20251.sql en MySQL
mysql -u usuario -p nombre_bd < jat20251.sql
```

### 3. Configurar conexión
Editar `config/conexion.php`:
```php
$host = "localhost";
$db = "nombre_base_datos";
$user = "usuario";
$pass = "contraseña";
```

### 4. Configurar email
```bash
# Copiar archivo de ejemplo
cp config/email_config.example.php config/email_config.php

# Editar y configurar credenciales SMTP
nano config/email_config.php
```

#### Gmail (Recomendado para desarrollo)
1. Ir a [Google App Passwords](https://myaccount.google.com/apppasswords)
2. Crear contraseña de aplicación
3. Configurar en `config/email_config.php`

#### Producción (SendGrid/Mailgun recomendado)
- **SendGrid**: 100 emails/día gratis
- **Mailgun**: 100 emails/día gratis

### 5. Instalar dependencias
```bash
composer install
```

### 6. Crear carpeta de logs
```bash
mkdir logs
chmod 755 logs
```

## 🧪 Pruebas

### Probar envío de emails
```
http://tu-dominio/test_email.php
```

### Acceder al sistema
```
http://tu-dominio/
```

## 📁 Estructura

```
JAT2025/
├── admin/              # Panel de administración
├── assets/             # CSS, JS, imágenes
├── config/             # Configuración
├── lib/                # Librerías (EmailService)
├── logs/               # Logs de sistema
├── vendor/             # Dependencias Composer
├── index.html          # Formulario de inscripción
├── procesar_inscripcion.php
└── test_email.php      # Prueba de emails
```

## 🔒 Seguridad

- ✅ Credenciales SMTP en `.gitignore`
- ✅ Validación de datos
- ✅ Protección SQL injection
- ✅ Headers de seguridad

## 📧 Sistema de Emails

El sistema usa **PHPMailer** con soporte SMTP completo:
- Envío automático al inscribirse
- Envío manual desde administración
- Logs detallados de todos los envíos

## 📞 Soporte

Para problemas o consultas:
- Email: arturo.naupa@iestlarecoleta.edu.pe
- WhatsApp: 996 560 202

## 📄 Licencia

Desarrollado para IEST La Recoleta - Arequipa, Perú

