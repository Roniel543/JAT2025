<?php

require_once 'config/conexion.php';

// Función para enviar confirmación automática
function enviarConfirmacionAutomatica($nombres, $email, $area, $celular, $institucion) {
    $area_texto = $area == 'informatica' ? 'Informática' : 'Metalmecánica';
    $fechaInscripcion = date('d/m/Y H:i');
    
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
            .info-card {
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                padding: 20px;
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
            }
            .next-steps {
                background: linear-gradient(135deg, #f093fb, #f5576c);
                color: white;
                padding: 25px;
                border-radius: 8px;
                margin: 25px 0;
            }
            .contact {
                background: #e3f2fd;
                border: 1px solid #bbdefb;
                padding: 20px;
                border-radius: 8px;
                margin: 25px 0;
            }
            .footer {
                background: #f8f9fa;
                padding: 20px;
                text-align: center;
                color: #6c757d;
                font-size: 14px;
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
                    <h2>¡Bienvenido/a, $nombres!</h2>
                    <p>Tu preinscripción ha sido registrada exitosamente</p>
                </div>
                
                <div class='info-card'>
                    <h3 style='margin-top: 0; color: #667eea;'>📋 Detalles de tu Inscripción</h3>
                    <p><strong>👤 Participante:</strong> $nombres</p>
                    <p><strong>📧 Email:</strong> $email</p>
                    <p><strong>🎯 Área de Interés:</strong> $area_texto</p>
                    <p><strong>📱 Celular:</strong> $celular</p>
                    <p><strong>🏫 Institución:</strong> $institucion</p>
                    <p><strong>📅 Fecha de Inscripción:</strong> $fechaInscripcion</p>
                </div>
                
                <div class='next-steps'>
                    <h3>🚀 Próximos Pasos</h3>
                    <p><strong>1. Confirmación de Pago:</strong> Recibirás instrucciones detalladas para completar tu inscripción</p>
                    <p><strong>2. Materiales del Evento:</strong> Te enviaremos recursos y información una semana antes</p>
                    <p><strong>3. Participación:</strong> ¡Nos vemos el 14 de Noviembre en IEST La Recoleta!</p>
                </div>
                
                <div class='contact'>
                    <h3>📞 ¿Tienes Preguntas?</h3>
                    <p><strong>Mg. Arturo Naupa</strong> - Unidad de Investigación</p>
                    <p>📧 arturo.naupa@iestlarecoleta.edu.pe</p>
                    <p>📱 WhatsApp: 996 560 202</p>
                    <p>☎️ Teléfono: (054) 270947</p>
                </div>
            </div>
            
            <div class='footer'>
                <p><strong>IEST La Recoleta</strong> - Arequipa, Perú</p>
                <p>Jornada de Actualización Tecnológica 2025</p>
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
    
    // Enviar email
    return mail($email, $asunto, $mensaje, implode("\r\n", $headers));
}
// Solo aceptar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido.');
}

// Recuperar y sanitizar datos
$nombres = trim($_POST['nombres'] ?? '');
$email   = trim($_POST['email'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$institucion = trim($_POST['institucion'] ?? '');
$area = trim($_POST['area'] ?? '');

// Validaciones obligatorias
if (empty($nombres)) {
    header("Location: error.html?tipo=validacion&campo=nombres");
    exit;
}
if (empty($email)) {
    header("Location: error.html?tipo=validacion&campo=email");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: error.html?tipo=validacion&campo=email_invalido");
    exit;
}
if (empty($celular)) {
    header("Location: error.html?tipo=validacion&campo=celular");
    exit;
}
if (empty($institucion)) {
    header("Location: error.html?tipo=validacion&campo=institucion");
    exit;
}
if (empty($area)) {
    header("Location: error.html?tipo=validacion&campo=area");
    exit;
}

// Validar que el área sea uno de los valores permitidos
if (!in_array($area, ['informatica', 'metalmecanica'])) {
    header("Location: error.html?tipo=validacion&campo=area");
    exit;
}



// Validar formato básico de celular (solo números, entre 9 y 15 dígitos)
if (!preg_match('/^[0-9]{9,15}$/', $celular)) {
    header("Location: error.html?tipo=validacion&campo=celular_formato");
    exit;
}

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el correo ya está registrado
    $stmt_check = $pdo->prepare("SELECT id FROM preinscripciones WHERE email = ?");
    $stmt_check->execute([$email]);
    if ($stmt_check->fetch()) {
        // Redirigir a página de error personalizada
        header("Location: error.html");
        exit;
    }

    // Insertar nueva preinscripción
    $stmt_insert = $pdo->prepare("
        INSERT INTO preinscripciones (nombres, email, celular, institucion, area)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt_insert->execute([$nombres, $email, $celular, $institucion, $area]);

    // Obtener el ID del participante recién insertado
    $participante_id = $pdo->lastInsertId();
    
    // Enviar email de confirmación
    enviarConfirmacionAutomatica($nombres, $email, $area, $celular, $institucion);

    // Redirigir a página de éxito
    header("Location: gracias.html");
    exit;

} catch (PDOException $e) {
    // Registrar error en logs (en producción)
    error_log("Error JAT2025 DB: " . $e->getMessage());
    die("Error al procesar tu preinscripción. Por favor, inténtalo más tarde.");
} catch (Exception $e) {
    error_log("Error JAT2025 General: " . $e->getMessage());
    die("Ocurrió un error inesperado. Inténtalo nuevamente.");
}
?>