<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/conexion.php';

// Configuración de paginación
$por_pagina = 20;
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Contar total de registros
$total_inscritos = $pdo->query("SELECT COUNT(*) FROM preinscripciones")->fetchColumn();

// Consultar solo los registros de la página actual
$inscritos = $pdo->query("
    SELECT *, 
    CASE area 
        WHEN 'informatica' THEN 'Informática' 
        WHEN 'metalmecanica' THEN 'Metalmecánica' 
    END AS area_texto
    FROM preinscripciones 
    ORDER BY fecha_inscripcion DESC
    LIMIT $por_pagina OFFSET $offset
")->fetchAll();

$total_paginas = ceil($total_inscritos / $por_pagina);

// Estadísticas adicionales
$estadisticas = [
    'total' => $total_inscritos,
    'informatica' => $pdo->query("SELECT COUNT(*) FROM preinscripciones WHERE area = 'informatica'")->fetchColumn(),
    'metalmecanica' => $pdo->query("SELECT COUNT(*) FROM preinscripciones WHERE area = 'metalmecanica'")->fetchColumn(),
    'hoy' => $pdo->query("SELECT COUNT(*) FROM preinscripciones WHERE DATE(fecha_inscripcion) = CURDATE()")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard JAT2025 - Panel de Administración</title>
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
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            JAT2025
                        </h1>
                        <p class="text-gray-600 text-sm">Panel de Administración</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="../enviar_confirmacion.php" class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-envelope mr-2"></i>
                        Enviar Emails
                    </a>
                    <a href="login.php?logout=1" class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de eliminación -->
        <?php if (isset($_GET['eliminado'])): ?>
        <div class="mb-6 p-4 rounded-2xl bg-green-100 text-green-800 border border-green-200">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                Participante eliminado correctamente
            </div>
        </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Inscritos</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $estadisticas['total'] ?></p>
                        <p class="text-green-600 text-sm font-medium">+<?= $estadisticas['hoy'] ?> hoy</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Informática</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $estadisticas['informatica'] ?></p>
                        <p class="text-blue-600 text-sm font-medium"><?= round(($estadisticas['informatica'] / max($estadisticas['total'], 1)) * 100, 1) ?>%</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-laptop-code text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Metalmecánica</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $estadisticas['metalmecanica'] ?></p>
                        <p class="text-orange-600 text-sm font-medium"><?= round(($estadisticas['metalmecanica'] / max($estadisticas['total'], 1)) * 100, 1) ?>%</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-cogs text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-white/20 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Capacidad</p>
                        <p class="text-3xl font-bold text-gray-900">160</p>
                        <p class="text-purple-600 text-sm font-medium"><?= round(($estadisticas['total'] / 160) * 100, 1) ?>% ocupado</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-pie text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerta de capacidad -->
        <?php if ($estadisticas['total'] >= 160): ?>
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-4 rounded-2xl mb-8 shadow-xl">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-bold text-lg">¡Capacidad Máxima Alcanzada!</h3>
                    <p>Se ha alcanzado la capacidad máxima de 160 participantes.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabla de participantes -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-list-alt mr-3"></i>
                    Lista de Participantes
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Participante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Institución</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Certificado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($inscritos as $inscrito): ?>
                        <tr class="hover:bg-indigo-50/50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                        <?= strtoupper(substr($inscrito['nombres'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($inscrito['nombres']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($inscrito['email']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($inscrito['celular']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($inscrito['institucion']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $inscrito['area'] == 'informatica' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' ?>">
                                    <i class="fas <?= $inscrito['area'] == 'informatica' ? 'fa-laptop-code' : 'fa-cogs' ?> mr-1"></i>
                                    <?= $inscrito['area_texto'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= date('d/m H:i', strtotime($inscrito['fecha_inscripcion'])) ?></div>
                                <div class="text-xs text-gray-500"><?= date('Y', strtotime($inscrito['fecha_inscripcion'])) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="generar_certificado.php?id=<?= $inscrito['id'] ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm font-medium rounded-full hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-1"></i>
                                        PDF
                                    </a>
                                    <a href="editar_participante.php?id=<?= $inscrito['id'] ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-sm font-medium rounded-full hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar
                                    </a>
                                    <a href="eliminar_participante.php?id=<?= $inscrito['id'] ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-medium rounded-full hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                                        <i class="fas fa-trash mr-1"></i>
                                        Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <?php if ($pagina_actual > 1): ?>
                <a href="?page=<?= $pagina_actual - 1 ?>" class="px-4 py-2 bg-white/80 backdrop-blur-lg rounded-lg shadow-lg border border-white/20 hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?page=<?= $i ?>" class="px-4 py-2 rounded-lg transition-all duration-200 <?= $i == $pagina_actual ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg' : 'bg-white/80 backdrop-blur-lg shadow-lg border border-white/20 hover:shadow-xl' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?page=<?= $pagina_actual + 1 ?>" class="px-4 py-2 bg-white/80 backdrop-blur-lg rounded-lg shadow-lg border border-white/20 hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="mt-16 bg-white/80 backdrop-blur-lg border-t border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-gray-600">© 2025 JAT2025 - Jornada de Actualización Tecnológica</p>
                <p class="text-sm text-gray-500 mt-2">Sistema de Certificados Profesionales</p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>