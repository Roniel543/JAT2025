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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombres = trim($_POST['nombres']);
    $email = trim($_POST['email']);
    $celular = trim($_POST['celular']);
    $institucion = trim($_POST['institucion']);
    $area = $_POST['area'];
    
    // Validaciones
    if (empty($nombres) || empty($email) || empty($celular) || empty($institucion) || empty($area)) {
        $mensaje = 'Todos los campos son obligatorios';
        $tipo_mensaje = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El email no es válido';
        $tipo_mensaje = 'error';
    } elseif (!in_array($area, ['informatica', 'metalmecanica'])) {
        $mensaje = 'El área seleccionada no es válida';
        $tipo_mensaje = 'error';
    } else {
        // Verificar si el email ya existe en otro participante
        $stmt = $pdo->prepare("SELECT id FROM preinscripciones WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        
        if ($stmt->fetch()) {
            $mensaje = 'El email ya está registrado por otro participante';
            $tipo_mensaje = 'error';
        } else {
            // Actualizar participante
            $stmt = $pdo->prepare("
                UPDATE preinscripciones 
                SET nombres = ?, email = ?, celular = ?, institucion = ?, area = ?
                WHERE id = ?
            ");
            
            if ($stmt->execute([$nombres, $email, $celular, $institucion, $area, $id])) {
                $mensaje = 'Participante actualizado correctamente';
                $tipo_mensaje = 'success';
                
                // Actualizar datos para mostrar
                $participante['nombres'] = $nombres;
                $participante['email'] = $email;
                $participante['celular'] = $celular;
                $participante['institucion'] = $institucion;
                $participante['area'] = $area;
            } else {
                $mensaje = 'Error al actualizar el participante';
                $tipo_mensaje = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Participante - JAT2025</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-lg shadow-xl border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="w-12 h-12 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-arrow-left text-white text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Editar Participante
                        </h1>
                        <p class="text-gray-600 text-sm">Modificar información del participante</p>
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
        <!-- Mensaje de estado -->
        <?php if ($mensaje): ?>
        <div class="mb-6 p-4 rounded-2xl <?= $tipo_mensaje == 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
            <div class="flex items-center">
                <i class="fas <?= $tipo_mensaje == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-3"></i>
                <?= htmlspecialchars($mensaje) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-user-edit mr-3"></i>
                    Información del Participante
                </h2>
            </div>
            
            <form method="POST" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombres -->
                    <div>
                        <label for="nombres" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>
                            Nombres Completos
                        </label>
                        <input type="text" 
                               id="nombres" 
                               name="nombres" 
                               value="<?= htmlspecialchars($participante['nombres']) ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2"></i>
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($participante['email']) ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Celular -->
                    <div>
                        <label for="celular" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2"></i>
                            Celular
                        </label>
                        <input type="tel" 
                               id="celular" 
                               name="celular" 
                               value="<?= htmlspecialchars($participante['celular']) ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Institución -->
                    <div>
                        <label for="institucion" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-university mr-2"></i>
                            Institución
                        </label>
                        <input type="text" 
                               id="institucion" 
                               name="institucion" 
                               value="<?= htmlspecialchars($participante['institucion']) ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <!-- Área -->
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2"></i>
                        Área de Interés
                    </label>
                    <select id="area" 
                            name="area" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        <option value="">Selecciona un área</option>
                        <option value="informatica" <?= $participante['area'] == 'informatica' ? 'selected' : '' ?>>
                            🤖 Informática (IA y Protección de Datos)
                        </option>
                        <option value="metalmecanica" <?= $participante['area'] == 'metalmecanica' ? 'selected' : '' ?>>
                            ⚙️ Metalmecánica (Diseño y Fabricación Aditiva)
                        </option>
                    </select>
                </div>

                <!-- Información adicional -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        Información Adicional
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-600">ID del Participante:</span>
                            <span class="text-gray-800">#<?= $participante['id'] ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Fecha de Inscripción:</span>
                            <span class="text-gray-800"><?= date('d/m/Y H:i', strtotime($participante['fecha_inscripcion'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cambios
                    </button>
                    
                    <a href="dashboard.php" 
                       class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 text-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Acciones adicionales -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Generar Certificado -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-certificate mr-2"></i>
                    Certificado
                </h3>
                <p class="text-gray-600 mb-4">Generar o regenerar el certificado del participante</p>
                <a href="generar_certificado.php?id=<?= $participante['id'] ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Generar PDF
                </a>
            </div>

            <!-- Información del participante -->
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user mr-2"></i>
                    Vista Previa
                </h3>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium text-gray-600">Nombre:</span> <?= htmlspecialchars($participante['nombres']) ?></div>
                    <div><span class="font-medium text-gray-600">Email:</span> <?= htmlspecialchars($participante['email']) ?></div>
                    <div><span class="font-medium text-gray-600">Celular:</span> <?= htmlspecialchars($participante['celular']) ?></div>
                    <div><span class="font-medium text-gray-600">Institución:</span> <?= htmlspecialchars($participante['institucion']) ?></div>
                    <div><span class="font-medium text-gray-600">Área:</span> 
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $participante['area'] == 'informatica' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' ?>">
                            <?= $participante['area'] == 'informatica' ? 'Informática' : 'Metalmecánica' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
