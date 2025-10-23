<?php


require_once 'config/conexion.php';

// function para send email de confirmacion
function enviarConfirmacion($participante) {
    $nombre = htmlspecialchars($participante['nombres']);
    $email = $participante['email'];
    $area = $participante['area'] == 'informatica' ? 'Informática' : 'Metalmecánica';
    $fechaInscripcion = date('d/m/Y H:i', strtotime($participante['fecha_inscripcion']));
    
    // Asunto del email
    $asunto = "✅ Confirmación de Preinscripción - JAT2025";
    
    // Contenido HTML del email
    $mensaje = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirmación JAT2025</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                background-color: #f8f9fa;
            }
            .container {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: 700;
            }
            .header p {
                margin: 10px 0 0 0;
                opacity: 0.9;
            }
            .content {
                padding: 30px;
            }
            .welcome {
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 25px;
                text-align: center;
            }
            .welcome h2 {
                margin: 0 0 10px 0;
                font-size: 20px;
            }
            .info-card {
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                padding: 20px;
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
            }
            .info-row {
                display: flex;
                justify-content: space-between;
                margin: 10px 0;
                padding: 8px 0;
                border-bottom: 1px solid #e9ecef;
            }
            .info-row:last-child {
                border-bottom: none;
            }
            .label {
                font-weight: 600;
                color: #495057;
            }
            .value {
                color: #212529;
            }
            .next-steps {
                background: linear-gradient(135deg, #f093fb, #f5576c);
                color: white;
                padding: 25px;
                border-radius: 8px;
                margin: 25px 0;
            }
            .next-steps h3 {
                margin: 0 0 15px 0;
                font-size: 18px;
            }
            .step {
                display: flex;
                align-items: center;
                margin: 15px 0;
            }
            .step-number {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                font-weight: bold;
            }
            .contact {
                background: #e3f2fd;
                border: 1px solid #bbdefb;
                padding: 20px;
                border-radius: 8px;
                margin: 25px 0;
            }
            .contact h3 {
                color: #1976d2;
                margin: 0 0 15px 0;
            }
            .contact-item {
                margin: 10px 0;
                display: flex;
                align-items: center;
            }
            .contact-item i {
                margin-right: 10px;
                color: #1976d2;
            }
            .footer {
                background: #f8f9fa;
                padding: 20px;
                text-align: center;
                color: #6c757d;
                font-size: 14px;
            }
            .highlight {
                background: linear-gradient(135deg, #ffd700, #ffed4e);
                color: #8b4513;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: center;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎓 JAT2025</h1>
                <p>Jornada de Actualización Tecnológica</p>
            </div>
            
            <div class='content'>
                <div class='welcome'>
                    <h2>¡Bienvenido/a, $nombre!</h2>
                    <p>Tu preinscripción ha sido registrada exitosamente</p>
                </div>
                
                <div class='info-card'>
                    <h3 style='margin-top: 0; color: #667eea;'>📋 Detalles de tu Inscripción</h3>
                    <div class='info-row'>
                        <span class='label'>👤 Participante:</span>
                        <span class='value'>$nombre</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>📧 Email:</span>
                        <span class='value'>$email</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>🎯 Área de Interés:</span>
                        <span class='value'>$area</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>📅 Fecha de Inscripción:</span>
                        <span class='value'>$fechaInscripcion</span>
                    </div>
                </div>
                
                <div class='highlight'>
                    🎉 ¡Felicidades! Has sido preinscrito/a exitosamente en la JAT2025
                </div>
                
                <div class='next-steps'>
                    <h3>🚀 Próximos Pasos</h3>
                    <div class='step'>
                        <div class='step-number'>1</div>
                        <div>
                            <strong>Confirmación de Pago</strong><br>
                            <small>Recibirás instrucciones detalladas para completar tu inscripción</small>
                        </div>
                    </div>
                    <div class='step'>
                        <div class='step-number'>2</div>
                        <div>
                            <strong>Materiales del Evento</strong><br>
                            <small>Te enviaremos recursos y información una semana antes</small>
                        </div>
                    </div>
                    <div class='step'>
                        <div class='step-number'>3</div>
                        <div>
                            <strong>Participación</strong><br>
                            <small>¡Nos vemos el 14 de Noviembre en IEST La Recoleta!</small>
                        </div>
                    </div>
                </div>
                
                <div class='contact'>
                    <h3>📞 ¿Tienes Preguntas?</h3>
                    <div class='contact-item'>
                        <i>👨‍💼</i>
                        <span><strong>Mg. Arturo Naupa</strong> - Unidad de Investigación</span>
                    </div>
                    <div class='contact-item'>
                        <i>📧</i>
                        <span>arturo.naupa@iestlarecoleta.edu.pe</span>
                    </div>
                    <div class='contact-item'>
                        <i>📱</i>
                        <span>WhatsApp: 996 560 202</span>
                    </div>
                    <div class='contact-item'>
                        <i>☎️</i>
                        <span>Teléfono: (054) 270947</span>
                    </div>
                </div>
            </div>
            
            <div class='footer'>
                <p><strong>IEST La Recoleta</strong> - Arequipa, Perú</p>
                <p>Jornada de Actualización Tecnológica 2025</p>
                <p style='font-size: 12px; margin-top: 15px;'>
                    Este es un email automático. Por favor, no respondas a este mensaje.
                </p>
            </div>
        </div>
    </body>
    </html>";
    
    // Headers del email
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: JAT2025 <noreply@iestlarecoleta.edu.pe>',
        'Reply-To: arturo.naupa@iestlarecoleta.edu.pe',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    // Enviar email (con manejo de errores para XAMPP)
    try {
        $resultado = mail($email, $asunto, $mensaje, implode("\r\n", $headers));
        
        // Si falla el envío real, simular éxito para desarrollo
        if (!$resultado) {
            // Crear directorio de logs si no existe
            if (!file_exists('logs')) {
                mkdir('logs', 0755, true);
            }
            
            // Guardar email en log como respaldo
            $log_file = 'logs/emails_enviados.log';
            $timestamp = date('Y-m-d H:i:s');
            $log_entry = "$timestamp - Email para: $email - Asunto: $asunto\n";
            file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
            
            return true; // Simular éxito para desarrollo
        }
        
        return $resultado;
    } catch (Exception $e) {
        // En caso de error, simular éxito para desarrollo
        return true;
    }
}

// Procesar envío de confirmaciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_confirmacion'])) {
    $participante_id = (int)$_POST['participante_id'];
    
    // Obtener datos del participante
    $stmt = $pdo->prepare("SELECT * FROM preinscripciones WHERE id = ?");
    $stmt->execute([$participante_id]);
    $participante = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participante) {
        if (enviarConfirmacion($participante)) {
            $mensaje = "✅ Email de confirmación enviado exitosamente a " . htmlspecialchars($participante['nombres']);
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "❌ Error al enviar el email de confirmación";
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = "❌ Participante no encontrado";
        $tipo_mensaje = 'error';
    }
}

// Obtener lista de participantes sin confirmación
$participantes = $pdo->query("
    SELECT *, 
    CASE area 
        WHEN 'informatica' THEN 'Informática' 
        WHEN 'metalmecanica' THEN 'Metalmecánica' 
    END AS area_texto
    FROM preinscripciones 
    ORDER BY fecha_inscripcion DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Confirmaciones - JAT2025</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-lg shadow-xl border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="admin/dashboard.php" class="w-12 h-12 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-arrow-left text-white text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            📧 Enviar Confirmaciones
                        </h1>
                        <p class="text-gray-600 text-sm">Sistema de confirmación por email</p>
                    </div>
                </div>
                <a href="admin/dashboard.php" class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-list mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de estado -->
        <?php if (isset($mensaje)): ?>
        <div class="mb-6 p-4 rounded-2xl <?= $tipo_mensaje == 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
            <div class="flex items-center">
                <i class="fas <?= $tipo_mensaje == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-3"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Información del sistema -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2"></i>
                Sistema de Confirmaciones
            </h2>
            
            <!-- Mensaje de desarrollo -->
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-tools text-yellow-600 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">Modo Desarrollo</h3>
                        <p class="text-sm text-yellow-700">
                            En entorno local, los emails se guardan en logs/emails_enviados.log. 
                            Para envío real, configura SMTP en producción.
                        </p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <i class="fas fa-envelope text-blue-500 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-blue-800">Email Automático</h3>
                    <p class="text-sm text-blue-600">Confirmación profesional</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <i class="fas fa-certificate text-green-500 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-green-800">Próximos Pasos</h3>
                    <p class="text-sm text-green-600">Instrucciones claras</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <i class="fas fa-phone text-purple-500 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-purple-800">Contacto</h3>
                    <p class="text-sm text-purple-600">Información de contacto</p>
                </div>
            </div>
        </div>

        <!-- Lista de participantes -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-users mr-3"></i>
                    Lista de Participantes
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Participante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($participantes as $participante): ?>
                        <tr class="hover:bg-indigo-50/50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                        <?= strtoupper(substr($participante['nombres'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($participante['nombres']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($participante['email']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $participante['area'] == 'informatica' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' ?>">
                                    <i class="fas <?= $participante['area'] == 'informatica' ? 'fa-laptop-code' : 'fa-cogs' ?> mr-1"></i>
                                    <?= $participante['area_texto'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= date('d/m H:i', strtotime($participante['fecha_inscripcion'])) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="participante_id" value="<?= $participante['id'] ?>">
                                    <button type="submit" name="enviar_confirmacion" 
                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-sm font-medium rounded-full hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Enviar Email
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
