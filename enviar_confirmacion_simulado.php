<?php
/**
 * JAT2025 - Sistema de Envío de Confirmaciones (Versión Simulada)
 * Para desarrollo local sin servidor SMTP
 */

require_once 'config/conexion.php';

// Función para simular envío de email
function simularEnvioEmail($participante) {
    $nombre = htmlspecialchars($participante['nombres']);
    $email = $participante['email'];
    $area = $participante['area'] == 'informatica' ? 'Informática' : 'Metalmecánica';
    $fechaInscripcion = date('d/m/Y H:i', strtotime($participante['fecha_inscripcion']));
    
    // Crear directorio de logs si no existe
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    // Guardar email en archivo de log
    $log_file = 'logs/emails_enviados.log';
    $timestamp = date('Y-m-d H:i:s');
    
    $email_content = "
=== EMAIL ENVIADO ===
Fecha: $timestamp
Para: $nombre <$email>
Asunto: ✅ Confirmación de Preinscripción - JAT2025
Área: $area
Fecha Inscripción: $fechaInscripcion

Contenido del Email:
¡Bienvenido/a, $nombre!

Tu preinscripción ha sido registrada exitosamente en la JAT2025.

Detalles de tu Inscripción:
- Participante: $nombre
- Email: $email
- Área de Interés: $area
- Fecha de Inscripción: $fechaInscripcion

Próximos Pasos:
1. Confirmación de Pago: Recibirás instrucciones detalladas
2. Materiales del Evento: Te enviaremos recursos una semana antes
3. Participación: ¡Nos vemos el 14 de Noviembre en IEST La Recoleta!

¿Tienes Preguntas?
- Mg. Arturo Naupa - Unidad de Investigación
- Email: arturo.naupa@iestlarecoleta.edu.pe
- WhatsApp: 996 560 202
- Teléfono: (054) 270947

IEST La Recoleta - Arequipa, Perú
Jornada de Actualización Tecnológica 2025

=====================================
";
    
    // Guardar en log
    file_put_contents($log_file, $email_content, FILE_APPEND | LOCK_EX);
    
    return true;
}

// Procesar envío de confirmaciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_confirmacion'])) {
    $participante_id = (int)$_POST['participante_id'];
    
    // Obtener datos del participante
    $stmt = $pdo->prepare("SELECT * FROM preinscripciones WHERE id = ?");
    $stmt->execute([$participante_id]);
    $participante = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participante) {
        if (simularEnvioEmail($participante)) {
            $mensaje = "✅ Email de confirmación simulado exitosamente para " . htmlspecialchars($participante['nombres']);
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "❌ Error al simular el envío del email";
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = "❌ Participante no encontrado";
        $tipo_mensaje = 'error';
    }
}

// Obtener lista de participantes
$participantes = $pdo->query("
    SELECT *, 
    CASE area 
        WHEN 'informatica' THEN 'Informática' 
        WHEN 'metalmecanica' THEN 'Metalmecánica' 
    END AS area_texto
    FROM preinscripciones 
    ORDER BY fecha_inscripcion DESC
")->fetchAll();

// Obtener emails enviados
$emails_enviados = [];
if (file_exists('logs/emails_enviados.log')) {
    $emails_enviados = array_reverse(file('logs/emails_enviados.log', FILE_IGNORE_NEW_LINES));
}
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
                        <p class="text-gray-600 text-sm">Sistema de confirmación por email (Modo Simulación)</p>
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
                Sistema de Confirmaciones (Modo Simulación)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <i class="fas fa-envelope text-blue-500 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-blue-800">Email Simulado</h3>
                    <p class="text-sm text-blue-600">Se guarda en logs/emails_enviados.log</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <i class="fas fa-file-alt text-green-500 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-green-800">Log de Emails</h3>
                    <p class="text-sm text-green-600">Registro completo de envíos</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <i class="fas fa-cogs text-purple-500 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-purple-800">Desarrollo</h3>
                    <p class="text-sm text-purple-600">Perfecto para testing</p>
                </div>
            </div>
        </div>

        <!-- Lista de participantes -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden mb-8">
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
                                        Simular Email
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Log de emails enviados -->
        <?php if (!empty($emails_enviados)): ?>
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-file-alt mr-3"></i>
                    Log de Emails Enviados
                </h2>
            </div>
            
            <div class="p-6">
                <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto max-h-96">
                    <?php foreach (array_slice($emails_enviados, 0, 10) as $line): ?>
                        <div><?= htmlspecialchars($line) ?></div>
                    <?php endforeach; ?>
                    <?php if (count($emails_enviados) > 10): ?>
                        <div class="text-gray-500">... y <?= count($emails_enviados) - 10 ?> líneas más</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
