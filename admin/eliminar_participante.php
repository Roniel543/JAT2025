<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';
$tipo_mensaje = '';

if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Obtener datos del participante
$stmt = $pdo->prepare("SELECT * FROM preinscripciones WHERE id = ?");
$stmt->execute([$id]);
$participante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participante) {
    header('Location: dashboard.php');
    exit;
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar'])) {
    $stmt = $pdo->prepare("DELETE FROM preinscripciones WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header('Location: dashboard.php?eliminado=1');
        exit;
    } else {
        $mensaje = 'Error al eliminar el participante';
        $tipo_mensaje = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Participante - JAT2025</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        //configuracion de tailwind adicicional para el admin
        //Usamos directiva tailwind.config para configurar tailwind
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#e0e7ff',
                            500: '#667eea',
                            600: '#5a67d8',
                            700: '#4c51bf',
                            900: '#2d3748'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-pink-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-lg shadow-xl border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="w-12 h-12 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-arrow-left text-white text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent">
                            Eliminar Participante
                        </h1>
                        <p class="text-gray-600 text-sm">Confirmar eliminación del participante</p>
                    </div>
                </div>
                <a href="dashboard.php" class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-list mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de estado esto en react es lo que conocemos como estado de alerta -->
        <?php if ($mensaje): ?>
        <div class="mb-6 p-4 rounded-2xl bg-red-100 text-red-800 border border-red-200">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Advertencia -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    Confirmar Eliminación
                </h2>
            </div>
            
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-1"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-red-800 mb-2">¡Atención!</h3>
                            <p class="text-red-700">
                                Estás a punto de eliminar permanentemente este participante. Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información del participante -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2"></i>
                        Información del Participante
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="font-medium text-gray-600">ID:</span>
                            <span class="text-gray-800">#<?= $participante['id'] ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Nombre:</span>
                            <span class="text-gray-800"><?= htmlspecialchars($participante['nombres']) ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Email:</span>
                            <span class="text-gray-800"><?= htmlspecialchars($participante['email']) ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Celular:</span>
                            <span class="text-gray-800"><?= htmlspecialchars($participante['celular']) ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Institución:</span>
                            <span class="text-gray-800"><?= htmlspecialchars($participante['institucion']) ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Área:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $participante['area'] == 'informatica' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' ?>">
                                <?= $participante['area'] == 'informatica' ? 'Informática' : 'Metalmecánica' ?>
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Fecha de Inscripción:</span>
                            <span class="text-gray-800"><?= date('d/m/Y H:i', strtotime($participante['fecha_inscripcion'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Formulario de confirmación -->
                <form method="POST" class="space-y-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-yellow-500 text-xl mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-800 mb-2">Confirmación de Seguridad</h4>
                                <p class="text-yellow-700 text-sm">
                                    Para confirmar la eliminación, escribe <strong>ELIMINAR</strong> en el campo de abajo.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="confirmacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Escribe "ELIMINAR" para confirmar:
                        </label>
                        <input type="text" 
                               id="confirmacion" 
                               name="confirmacion" 
                               placeholder="ELIMINAR"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button type="submit" 
                                name="confirmar"
                                class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-trash mr-2"></i>
                            Eliminar Permanentemente
                        </button>
                        
                        <a href="dashboard.php" 
                           class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 text-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Habilitar botón solo cuando se escriba "ELIMINAR"
        document.getElementById('confirmacion').addEventListener('input', function() {
            const button = document.querySelector('button[type="submit"]');
            if (this.value.toUpperCase() === 'ELIMINAR') {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
