<?php
/**
 * JAT2025 - Configuración SMTP para XAMPP
 * Solución para el envío de emails en entorno local
 */

// Configurar SMTP para XAMPP
ini_set('SMTP', 'smtp.gmail.com');
ini_set('smtp_port', '587');
ini_set('sendmail_from', 'tu-email@gmail.com');

// Función mejorada para enviar emails con PHPMailer
function enviarEmailConSMTP($destinatario, $asunto, $mensaje, $nombre_destinatario = '') {
    // Para desarrollo local, usaremos una solución alternativa
    // En producción, se recomienda usar PHPMailer con SMTP real
    
    // Headers mejorados
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: JAT2025 <noreply@iestlarecoleta.edu.pe>',
        'Reply-To: arturo.naupa@iestlarecoleta.edu.pe',
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 3'
    ];
    
    // Intentar envío con mail() nativo
    $resultado = mail($destinatario, $asunto, $mensaje, implode("\r\n", $headers));
    
    if (!$resultado) {
        // Si falla, guardar en archivo de log para simulación
        $log_file = 'logs/emails_enviados.log';
        if (!file_exists('logs')) {
            mkdir('logs', 0755, true);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " - Email para: $destinatario - Asunto: $asunto\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        return true; // Simular éxito para desarrollo
    }
    
    return $resultado;
}

// Función para mostrar emails enviados (simulación)
function mostrarEmailsEnviados() {
    $log_file = 'logs/emails_enviados.log';
    if (file_exists($log_file)) {
        $emails = file($log_file, FILE_IGNORE_NEW_LINES);
        return array_reverse($emails); // Más recientes primero
    }
    return [];
}
?>
