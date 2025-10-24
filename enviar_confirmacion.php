<?php
/**
 * JAT2025 - Envío Manual de Confirmaciones
 * Panel de administración para enviar emails de confirmación
 */

require_once 'vendor/autoload.php';
require_once 'config/conexion.php';
require_once 'lib/EmailService.php';

// Procesar envío de confirmaciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_confirmacion'])) {
    $participante_id = (int)$_POST['participante_id'];
    
    // Obtener datos del participante
    $stmt = $pdo->prepare("SELECT * FROM preinscripciones WHERE id = ?");
    $stmt->execute([$participante_id]);
    $participante = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participante) {
        try {
            $emailService = new EmailService();
            if ($emailService->enviarConfirmacionInscripcion($participante)) {
                $mensaje = "✅ Email de confirmación enviado exitosamente a " . htmlspecialchars($participante['nombres']);
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "⚠️ Hubo un problema al enviar el email. Revisa los logs.";
                $tipo_mensaje = 'error';
            }
        } catch (Exception $e) {
            error_log("Error enviando confirmación: " . $e->getMessage());
            $mensaje = "❌ Error al enviar el email de confirmación: " . $e->getMessage();
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        institutional: {
                            navy: '#0A1E3D',
                            gold: '#F7B800',
                            'gold-light': '#FFE066',
                            'navy-light': '#1a3a5f'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .institutional-gradient {
            background: linear-gradient(135deg, #0A1E3D 0%, #1a3a5f 100%);
        }
        .badge-area {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        .badge-metalmecanica {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="institutional-gradient shadow-2xl border-b-4 border-institutional-gold">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-5">
                    <a href="admin/dashboard.php" class="w-14 h-14 bg-institutional-gold rounded-xl flex items-center justify-center hover:shadow-lg hover:bg-institutional-gold-light transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-arrow-left text-institutional-navy text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-institutional-gold tracking-tight">
                            <i class="fas fa-envelope-open-text mr-2"></i>
                            Enviar Confirmaciones
                        </h1>
                        <p class="text-gray-200 text-sm font-medium mt-1">Sistema de confirmación por email a participantes</p>
                    </div>
                </div>
                <a href="admin/dashboard.php" class="inline-flex items-center bg-institutional-gold text-institutional-navy px-6 py-3 rounded-xl font-bold hover:bg-institutional-gold-light hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-list mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de estado -->
        <?php if (isset($mensaje)): ?>
        <div class="mb-8 p-5 rounded-xl <?= $tipo_mensaje == 'success' ? 'bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-600' : 'bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-600' ?> shadow-lg">
            <div class="flex items-center">
                <div class="w-12 h-12 <?= $tipo_mensaje == 'success' ? 'bg-green-600' : 'bg-red-600' ?> rounded-full flex items-center justify-center mr-4">
                    <i class="fas <?= $tipo_mensaje == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> text-white text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-lg <?= $tipo_mensaje == 'success' ? 'text-green-900' : 'text-red-900' ?>">
                        <?= $tipo_mensaje == 'success' ? '¡Éxito!' : '¡Error!' ?>
                    </p>
                    <p class="text-sm <?= $tipo_mensaje == 'success' ? 'text-green-800' : 'text-red-800' ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Información del sistema -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-14 h-14 bg-institutional-navy rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-info-circle text-institutional-gold text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-institutional-navy">Sistema de Confirmaciones por Email</h2>
                    <p class="text-gray-600 text-sm mt-1">Envío manual de confirmaciones a participantes registrados</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border-2 border-blue-200 hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-envelope-open text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-blue-900 text-lg">Email Automático</h3>
                    <p class="text-sm text-blue-700 mt-2">Confirmación profesional con diseño institucional</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border-2 border-green-200 hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-list-check text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-green-900 text-lg">Próximos Pasos</h3>
                    <p class="text-sm text-green-700 mt-2">Instrucciones claras para el participante</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border-2 border-orange-200 hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-orange-900 text-lg">Información de Contacto</h3>
                    <p class="text-sm text-orange-700 mt-2">Datos de contacto del instituto</p>
                </div>
            </div>
            
            <!-- Nota informativa -->
            <div class="mt-6 p-4 bg-institutional-navy/5 border-l-4 border-institutional-gold rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-institutional-gold text-xl mt-1 mr-3"></i>
                    <div>
                        <p class="font-semibold text-institutional-navy">Uso de esta herramienta:</p>
                        <p class="text-gray-700 text-sm mt-1">
                            Utiliza el botón "Enviar Email" para enviar manualmente confirmaciones a participantes 
                            que no hayan recibido su email automático o necesiten un reenvío.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de participantes -->
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="institutional-gradient px-6 py-5 border-b-4 border-institutional-gold">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-users mr-3 text-institutional-gold"></i>
                    Lista de Participantes
                </h2>
                <p class="text-gray-300 text-sm mt-1">Selecciona un participante para enviar su confirmación por email</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-institutional-gold">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Participante</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($participantes as $participante): ?>
                        <tr class="hover:bg-gradient-to-r hover:from-yellow-50/50 hover:to-transparent transition-all duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-institutional-navy to-institutional-navy-light rounded-xl flex items-center justify-center text-institutional-gold font-bold text-lg shadow-lg">
                                        <?= strtoupper(substr($participante['nombres'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($participante['nombres']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 font-medium flex items-center">
                                    <i class="fas fa-envelope text-institutional-gold mr-2"></i>
                                    <?= htmlspecialchars($participante['email']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($participante['area'] == 'informatica'): ?>
                                    <span class="badge-area inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-white">
                                        <i class="fas fa-laptop-code mr-2"></i>
                                        <?= $participante['area_texto'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge-metalmecanica inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-white">
                                        <i class="fas fa-cogs mr-2"></i>
                                        <?= $participante['area_texto'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 font-medium">
                                    <?= date('d/m/Y', strtotime($participante['fecha_inscripcion'])) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= date('H:i', strtotime($participante['fecha_inscripcion'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="participante_id" value="<?= $participante['id'] ?>">
                                    <button type="submit" name="enviar_confirmacion" 
                                            class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-bold rounded-xl hover:shadow-xl hover:from-green-700 hover:to-emerald-700 transform hover:-translate-y-1 transition-all duration-200">
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
