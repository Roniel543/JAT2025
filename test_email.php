<?php
/**
 * JAT2025 - Test de Sistema de Emails
 * Página para probar el envío de confirmaciones
 */

// Función para enviar email de prueba
function enviarEmailPrueba($email) {
    $asunto = "🧪 Test Email JAT2025 - " . date('H:i:s');
    
    $mensaje = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Test Email JAT2025</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; }
            .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🧪 Test Email JAT2025</h1>
        </div>
        <div class='content'>
            <div class='success'>
                <h3>✅ Email de Prueba Exitoso</h3>
                <p>Este es un email de prueba del sistema JAT2025.</p>
                <p><strong>Hora de envío:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p><strong>Destinatario:</strong> $email</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: JAT2025 Test <noreply@iestlarecoleta.edu.pe>',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($email, $asunto, $mensaje, implode("\r\n", $headers));
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_email'])) {
    $email = trim($_POST['email']);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (enviarEmailPrueba($email)) {
            $mensaje = "✅ Email de prueba enviado exitosamente a $email";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "❌ Error al enviar el email de prueba";
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = "❌ Email no válido";
        $tipo_mensaje = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - JAT2025</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-8">
            <h1 class="text-3xl font-bold text-center mb-8">
                <i class="fas fa-envelope mr-3"></i>
                Test de Sistema de Emails
            </h1>
            
            <?php if ($mensaje): ?>
            <div class="mb-6 p-4 rounded-2xl <?= $tipo_mensaje == 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                <div class="flex items-center">
                    <i class="fas <?= $tipo_mensaje == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-3"></i>
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>
                        Email de Prueba
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required
                           placeholder="tu@email.com"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <button type="submit" 
                        name="test_email"
                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar Email de Prueba
                </button>
            </form>
            
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h3 class="font-semibold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información del Test
                </h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Este test verifica que el sistema de emails funcione correctamente</li>
                    <li>• El email se enviará desde: noreply@iestlarecoleta.edu.pe</li>
                    <li>• Revisa tu bandeja de entrada y spam</li>
                    <li>• Si no recibes el email, verifica la configuración de PHP mail()</li>
                </ul>
            </div>
            
            <div class="mt-6 text-center">
                <a href="admin/dashboard.php" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
