<?php
/**
 * JAT2025 - Script de Prueba de Envío de Emails
 * 
 * Este archivo te permite probar si el envío de emails está funcionando correctamente.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar autoloader de Composer (necesario para PHPMailer)
require_once 'vendor/autoload.php';

require_once 'lib/EmailService.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_destino'])) {
    $emailDestino = filter_var($_POST['email_destino'], FILTER_VALIDATE_EMAIL);
    
    if (!$emailDestino) {
        $mensaje = '❌ Email inválido. Por favor, ingresa un email válido.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $emailService = new EmailService();
            
            // Crear participante de prueba
            $participante = [
                'nombres' => 'Participante de Prueba',
                'email' => $emailDestino,
                'area' => 'informatica',
                'celular' => '999999999',
                'institucion' => 'IEST La Recoleta - Prueba',
                'fecha_inscripcion' => date('Y-m-d H:i:s')
            ];
            
            $resultado = $emailService->enviarConfirmacionInscripcion($participante);
            
            if ($resultado) {
                $mensaje = "✅ Email enviado exitosamente a $emailDestino. Revisa tu bandeja de entrada (y spam).";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = '⚠️ Error al enviar email. Revisa logs/emails_enviados.log para más detalles.';
                $tipo_mensaje = 'error';
            }
            
        } catch (Exception $e) {
            $mensaje = '❌ Error crítico: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}

// Cargar configuración para mostrar información
$config = require 'config/email_config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Email - JAT2025</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-8">
            <h1 class="text-3xl font-bold text-center mb-8 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                <i class="fas fa-envelope mr-3"></i>
                Test de Sistema de Emails
            </h1>
            
            <!-- Mensaje de resultado -->
            <?php if ($mensaje): ?>
            <div class="mb-6 p-4 rounded-2xl <?= $tipo_mensaje == 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                <div class="flex items-center">
                    <i class="fas <?= $tipo_mensaje == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-3 text-xl"></i>
                    <span><?= htmlspecialchars($mensaje) ?></span>
                </div>
            </div>
            <?php endif; ?>
            
        <!-- Información actual de configuración -->
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <h3 class="font-semibold text-green-800 mb-3 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Sistema Configurado
            </h3>
            <div class="text-sm text-green-700 space-y-1">
                <div><strong>SMTP:</strong> <?= $config['smtp']['host'] ?>:<?= $config['smtp']['port'] ?></div>
                <div><strong>Usuario:</strong> <?= $config['smtp']['username'] ? '***@' . substr($config['smtp']['username'], strpos($config['smtp']['username'], '@')) : 'No configurado' ?></div>
                <div><strong>Estado:</strong> <span class="font-bold">✅ Listo para envío</span></div>
            </div>
        </div>
            
            <!-- Formulario de prueba -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email_destino" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>
                        Email de destino (donde recibirás el email de prueba)
                    </label>
                    <input type="email" 
                           id="email_destino" 
                           name="email_destino" 
                           required
                           placeholder="tu-email@ejemplo.com"
                           value="<?= isset($_POST['email_destino']) ? htmlspecialchars($_POST['email_destino']) : '' ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar Email de Prueba
                </button>
            </form>
            
            <!-- Información del test -->
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h3 class="font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información del Test
                </h3>
                <ul class="text-sm text-blue-700 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Envía un email de confirmación real con diseño profesional</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Si no aparece en bandeja principal, revisa spam</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Logs disponibles en: <code class="bg-blue-100 px-1 rounded">logs/emails_enviados.log</code></span>
                    </li>
                </ul>
            </div>
            
            <!-- Enlaces -->
            <div class="mt-6 text-center">
                <a href="admin/dashboard.php" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
        
        <!-- Tarjetas de ayuda -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg border border-white/20 p-4 text-center">
                <i class="fas fa-envelope-open-text text-4xl text-indigo-500 mb-2"></i>
                <h4 class="font-semibold text-gray-800">Inscripción</h4>
                <p class="text-sm text-gray-600">Email automático al inscribirse</p>
            </div>
            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg border border-white/20 p-4 text-center">
                <i class="fas fa-paper-plane text-4xl text-purple-500 mb-2"></i>
                <h4 class="font-semibold text-gray-800">Manual</h4>
                <p class="text-sm text-gray-600">Envío desde administración</p>
            </div>
            <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg border border-white/20 p-4 text-center">
                <i class="fas fa-file-alt text-4xl text-pink-500 mb-2"></i>
                <h4 class="font-semibold text-gray-800">Logs</h4>
                <p class="text-sm text-gray-600">Registro de todos los envíos</p>
            </div>
        </div>
    </div>
</body>
</html>
