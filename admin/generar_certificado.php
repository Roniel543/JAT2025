<?php
// Versión corregida del generador de certificados
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/conexion.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Evitar caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Validar ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    die('ID inválido');
}

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM preinscripciones WHERE id = ?");
    $stmt->execute([$id]);
    $inscrito = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inscrito) {
        http_response_code(404);
        die('Participante no encontrado');
    }

    // Configuración de DomPDF optimizada para imágenes
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', false);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isFontSubsettingEnabled', true);
    $options->set('debugKeepTemp', false);
    $options->set('isPhpEnabled', true);
    $options->set('isJavascriptEnabled', false);
    $dompdf = new Dompdf($options);

    // === Cargar imágenes ===
    $logoPath = __DIR__ . '/../assets/images/logo.png';
    $logoData = '';
    if (file_exists($logoPath)) {
        $logoMimeType = mime_content_type($logoPath);
        if (in_array($logoMimeType, ['image/png', 'image/jpeg', 'image/gif'])) {
            $logoData = 'data:' . $logoMimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    $fondoPath = __DIR__ . '/../assets/images/cert_optimizada.png';
    $fondoData = '';
    if (file_exists($fondoPath)) {
        $fondoMimeType = mime_content_type($fondoPath);
        if (in_array($fondoMimeType, ['image/png', 'image/jpeg', 'image/gif'])) {
            $fondoData = 'data:' . $fondoMimeType . ';base64,' . base64_encode(file_get_contents($fondoPath));
        }
    }

    // Sanitizar nombre
    $nombreCompleto = htmlspecialchars(trim($inscrito['nombres']), ENT_QUOTES, 'UTF-8');

    // Diseño optimizado para una sola página A4
    $css = "
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .certificate {
            width: 100%;
            height: 100vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        .content-overlay {
            background: rgba(255, 255, 255, 0);
            padding: 40px;
            width: 90%;
            max-width: 900px;
            text-align: center;
            position: relative;
            height: calc(100vh - 80px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            z-index: 2;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 30px;
        }
        .logo {
            height: 90px;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        .institution {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .resolution {
            font-size: 14px;
            color: #666;
            font-style: italic;
        }
        .main-title {
            font-size: 32px;
            font-weight: bold;
            color: #8b4513;
            margin: 20px 0 15px;
            letter-spacing: 3px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            position: relative;
        }
        .main-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }
        .subtitle {
            font-size: 18px;
            color: #4b5563;
            margin: 15px 0;
            font-style: italic;
        }
        .recipient-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
            font-family: 'Georgia', serif;
            position: relative;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .event-info {
            margin: 20px 0;
        }
        .event-text {
            font-size: 16px;
            color: #4b5563;
            margin: 8px 0;
            line-height: 1.5;
        }
        .event-title {
            font-size: 20px;
            font-weight: bold;
            color: #8b4513;
            margin: 12px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .event-theme {
            font-size: 18px;
            font-style: italic;
            color: #6b7280;
            margin: 8px 0;
        }
        .date-location {
            font-size: 16px;
            color: #4b5563;
            margin: 20px 0;
            line-height: 1.5;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 18px;
            border-left: 4px solid #d4af37;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }f
        .signatures {
            margin-top: 20em;
            text-align: center;
            width: 100%;
        }
        .signature {
            display: inline-block;
            text-align: center;
            margin: 0 auto;
        }
        .signature-line {
            width: 350px;
            height: 1px;
            background: #1f2937;
            margin: 0 auto 10px;
            margin-top: 60px;
            border-bottom: 1px solid #1f2937;
        }
        .signature-name {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 10px 0;
        }
        .signature-title {
            font-size: 14px;
            color: #6b7280;
            margin: 5px 0;
        }
    ";

    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado JAT 2025</title>
    <style>{$css}</style>
</head>
<body>
    <div class="certificate">
        <img src="$fondoData" class="background-image" alt="Fondo del certificado">
        <div class="content-overlay">
            <div class="header">
HTML;

    if (!empty($logoData)) {
        $html .= '<img src="' . $logoData . '" alt="Logo IEST La Recoleta" class="logo">';
    }

    $html .= <<<HTML
            </div>
            
            <div class="main-title">CERTIFICADO DE PARTICIPACIÓN</div>
            
            <div class="subtitle">Se otorga el presente certificado a:</div>
            
            <div class="recipient-name">{$nombreCompleto}</div>
            
            <div class="event-info">
                <div class="event-text">Por su destacada participación en la</div>
                <div class="event-title">Jornada de Actualización Tecnológica 2025</div>
                <div class="event-theme">"Tecnologías emergentes en la región Arequipa"</div>
            </div>
            
            <div class="date-location">
                <strong>Fecha:</strong> Viernes, 14 de Noviembre de 2025<br>
                <strong>Lugar:</strong> IEST La Recoleta – Arequipa, Perú
            </div>
            
            <div class="signatures">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">Mg. Marisol Coaguila Valdivia</div>
                    <div class="signature-title">DIRECTORA GENERAL</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

    // Cargar HTML en Dompdf
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Enviar PDF al navegador
    $filename = "certificado_jat2025_{$inscrito['id']}.pdf";
    $dompdf->stream($filename, ["Attachment" => false]);

} catch (Exception $e) {
    error_log("Error generando certificado (ID: {$id}): " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Mostrar error detallado en desarrollo
    echo "<h2>Error al generar certificado</h2>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}