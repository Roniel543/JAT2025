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
        .institutional-card {
            background: white;
            border: 2px solid #e5e5e5;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .institutional-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #F7B800 0%, #FFE066 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .institutional-card:hover {
            border-color: #F7B800;
            box-shadow: 0 10px 25px rgba(10, 30, 61, 0.15);
            transform: translateY(-5px);
        }
        .institutional-card:hover::before {
            transform: scaleX(1);
        }
        .gold-divider {
            height: 3px;
            background: linear-gradient(90deg, transparent, #F7B800, transparent);
        }
        .stat-icon {
            position: relative;
            overflow: hidden;
        }
        .stat-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(247, 184, 0, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }
        .institutional-card:hover .stat-icon::after {
            width: 120%;
            height: 120%;
        }
        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(247, 184, 0, 0.1) 0%, transparent 100%);
        }
        .btn-action {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn-action::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
            z-index: -1;
        }
        .btn-action:hover::before {
            width: 300%;
            height: 300%;
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
                    <div class="w-16 h-16 bg-gradient-to-br from-institutional-gold to-institutional-gold-light rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-graduation-cap text-institutional-navy text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-extrabold text-institutional-gold tracking-tight">
                            JAT2025
                        </h1>
                        <p class="text-gray-200 text-sm font-medium mt-1">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Panel de Administración
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="../enviar_confirmacion.php" 
                       class="btn-action inline-flex items-center bg-institutional-gold text-institutional-navy px-6 py-3 font-bold rounded-xl hover:bg-institutional-gold-light transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-envelope mr-2"></i>
                        Enviar Emails
                    </a>
                    <a href="login.php?logout=1" 
                       class="btn-action inline-flex items-center bg-red-600 text-white px-6 py-3 font-bold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg hover:shadow-xl">
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
        <div class="mb-8 p-5 rounded-xl bg-gradient-to-r from-green-50 to-green-100 text-green-800 border-l-4 border-green-600 shadow-lg">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-lg">¡Acción Exitosa!</p>
                    <p class="text-sm">Participante eliminado correctamente del sistema</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="institutional-card p-6 shadow-lg rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Total Inscritos</p>
                        <p class="text-4xl font-bold text-institutional-navy mb-1"><?= $estadisticas['total'] ?></p>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +<?= $estadisticas['hoy'] ?> hoy
                            </span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-institutional-navy flex items-center justify-center rounded-xl stat-icon shadow-lg">
                        <i class="fas fa-users text-institutional-gold text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="institutional-card p-6 shadow-lg rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Informática</p>
                        <p class="text-4xl font-bold text-institutional-navy mb-1"><?= $estadisticas['informatica'] ?></p>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= round(($estadisticas['informatica'] / max($estadisticas['total'], 1)) * 100, 1) ?>% del total
                            </span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-green-600 flex items-center justify-center rounded-xl stat-icon shadow-lg">
                        <i class="fas fa-laptop-code text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="institutional-card p-6 shadow-lg rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Metalmecánica</p>
                        <p class="text-4xl font-bold text-institutional-navy mb-1"><?= $estadisticas['metalmecanica'] ?></p>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <?= round(($estadisticas['metalmecanica'] / max($estadisticas['total'], 1)) * 100, 1) ?>% del total
                            </span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-orange-600 flex items-center justify-center rounded-xl stat-icon shadow-lg">
                        <i class="fas fa-cogs text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="institutional-card p-6 shadow-lg rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">Capacidad</p>
                        <p class="text-4xl font-bold text-institutional-navy mb-1">160</p>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <?= round(($estadisticas['total'] / 160) * 100, 1) ?>% ocupado
                            </span>
                        </div>
                    </div>
                    <div class="w-16 h-16 bg-purple-600 flex items-center justify-center rounded-xl stat-icon shadow-lg">
                        <i class="fas fa-chart-pie text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerta de capacidad -->
        <?php if ($estadisticas['total'] >= 160): ?>
        <div class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 text-white p-6 rounded-2xl mb-8 shadow-2xl border-l-4 border-white">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mr-5">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-extrabold text-2xl mb-1">¡Capacidad Máxima Alcanzada!</h3>
                    <p class="text-white/90">Se ha alcanzado la capacidad máxima de 160 participantes. No se aceptarán más inscripciones.</p>
                </div>
                <div class="bg-white/20 backdrop-blur px-5 py-3 rounded-xl">
                    <p class="text-3xl font-black"><?= $estadisticas['total'] ?>/160</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Divisor decorativo -->
        <div class="gold-divider mb-8"></div>

        <!-- Tabla de participantes -->
        <div class="bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-200">
            <div class="institutional-gradient px-6 py-5 border-b-4 border-institutional-gold">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-list-alt mr-3 text-institutional-gold"></i>
                    Lista de Participantes
                </h2>
                <p class="text-gray-300 text-sm mt-1">Gestión completa de preinscripciones</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-institutional-gold">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Participante</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Institución</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Área</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-institutional-navy uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($inscritos as $inscrito): ?>
                        <tr class="table-row-hover transition-all duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-institutional-navy to-institutional-navy-light rounded-xl flex items-center justify-center text-institutional-gold font-bold text-lg shadow-lg">
                                        <?= strtoupper(substr($inscrito['nombres'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($inscrito['nombres']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 font-medium flex items-center">
                                    <i class="fas fa-envelope text-institutional-gold mr-2"></i>
                                    <?= htmlspecialchars($inscrito['email']) ?>
                                </div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-phone text-green-600 mr-2"></i>
                                    <?= htmlspecialchars($inscrito['celular']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 font-medium flex items-center">
                                    <i class="fas fa-building text-institutional-navy mr-2"></i>
                                    <?= htmlspecialchars($inscrito['institucion']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($inscrito['area'] == 'informatica'): ?>
                                    <span class="badge-area inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-white">
                                        <i class="fas fa-laptop-code mr-2"></i>
                                        <?= $inscrito['area_texto'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge-metalmecanica inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-white">
                                        <i class="fas fa-cogs mr-2"></i>
                                        <?= $inscrito['area_texto'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 font-medium">
                                    <?= date('d/m/Y', strtotime($inscrito['fecha_inscripcion'])) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= date('H:i', strtotime($inscrito['fecha_inscripcion'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="generar_certificado.php?id=<?= $inscrito['id'] ?>" 
                                       class="btn-action inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <i class="fas fa-file-pdf mr-2"></i>
                                        PDF
                                    </a>
                                    <a href="editar_participante.php?id=<?= $inscrito['id'] ?>" 
                                       class="btn-action inline-flex items-center px-4 py-2 bg-institutional-navy text-white text-sm font-bold rounded-lg hover:bg-institutional-navy-light transition-all duration-200 shadow-md hover:shadow-lg">
                                        <i class="fas fa-edit mr-2"></i>
                                        Editar
                                    </a>
                                    <a href="eliminar_participante.php?id=<?= $inscrito['id'] ?>" 
                                       class="btn-action inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-bold rounded-lg hover:bg-orange-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <i class="fas fa-trash mr-2"></i>
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
        <div class="mt-10 flex justify-center">
            <nav class="flex items-center space-x-2 bg-white p-3 rounded-xl shadow-lg border-2 border-gray-200">
                <?php if ($pagina_actual > 1): ?>
                <a href="?page=<?= $pagina_actual - 1 ?>" 
                   class="w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-institutional-navy hover:text-institutional-gold transition-all duration-200 shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-200 <?= $i == $pagina_actual ? 'bg-gradient-to-br from-institutional-navy to-institutional-navy-light text-institutional-gold font-bold shadow-lg scale-110' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 font-medium shadow-sm' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?page=<?= $pagina_actual + 1 ?>" 
                   class="w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-institutional-navy hover:text-institutional-gold transition-all duration-200 shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="mt-20 institutional-gradient border-t-4 border-institutional-gold shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="text-center">
                <div class="mb-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-institutional-gold rounded-2xl mb-3">
                        <i class="fas fa-graduation-cap text-institutional-navy text-2xl"></i>
                    </div>
                </div>
                <p class="text-white font-extrabold text-lg tracking-wide">
                    © 2025 INSTITUTO DE EDUCACIÓN SUPERIOR TECNOLÓGICO LA RECOLETA
                </p>
                <div class="gold-divider w-32 mx-auto my-4"></div>
                <p class="text-institutional-gold text-base font-bold">
                    Jornada de Actualización Tecnológica 2025
                </p>
                <p class="text-gray-300 text-sm mt-3 opacity-90">
                    Sistema de Gestión de Preinscripciones y Certificados
                </p>
                <div class="mt-6 flex items-center justify-center space-x-6 text-gray-300">
                    <a href="#" class="hover:text-institutional-gold transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>Inicio
                    </a>
                    <span class="text-institutional-gold">•</span>
                    <a href="#" class="hover:text-institutional-gold transition-colors duration-200">
                        <i class="fas fa-info-circle mr-2"></i>Ayuda
                    </a>
                    <span class="text-institutional-gold">•</span>
                    <a href="#" class="hover:text-institutional-gold transition-colors duration-200">
                        <i class="fas fa-envelope mr-2"></i>Contacto
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>