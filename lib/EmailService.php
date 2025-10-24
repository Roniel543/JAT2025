<?php
/**
 * JAT2025 - Servicio de Email
 * Clase centralizada para envío de emails
 * Compatible con mail() nativo y preparado para PHPMailer
 */

class EmailService {
    private $config;
    private $usePHPMailer = false;
    private $mailer = null;
    
    public function __construct() {
        // Cargar configuración
        $this->config = require __DIR__ . '/../config/email_config.php';
        
        // Verificar si PHPMailer está disponible
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->usePHPMailer = true;
            $this->configurarPHPMailer();
        } else {
            // Usar mail() nativo de PHP
            $this->configurarMailNativo();
        }
    }
    
    /**
     * Configurar PHPMailer (si está disponible)
     */
    private function configurarPHPMailer() {
        try {
            $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración del servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp']['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp']['username'];
            $this->mailer->Password = $this->config['smtp']['password'];
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
            $this->mailer->Port = $this->config['smtp']['port'];
            
            // Configuración de caracteres
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            
            // Configuración de remitente
            $this->mailer->setFrom(
                $this->config['from']['email'],
                $this->config['from']['name']
            );
            
            // Email de respuesta
            $this->mailer->addReplyTo(
                $this->config['reply_to']['email'],
                $this->config['reply_to']['name']
            );
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
            // Fallback a mail() nativo
            $this->usePHPMailer = false;
            $this->configurarMailNativo();
        }
    }
    
    /**
     * Configurar mail() nativo de PHP
     */
    private function configurarMailNativo() {
        // Configurar parámetros de PHP para mail()
        ini_set('SMTP', $this->config['smtp']['host']);
        ini_set('smtp_port', $this->config['smtp']['port']);
        ini_set('sendmail_from', $this->config['from']['email']);
    }
    
    /**
     * Enviar email de confirmación de inscripción
     */
    public function enviarConfirmacionInscripcion($participante) {
        $nombre = htmlspecialchars($participante['nombres']);
        $email = $participante['email'];
        $area = $participante['area'] == 'informatica' ? 'Informática' : 'Metalmecánica';
        $celular = $participante['celular'] ?? 'No especificado';
        $institucion = $participante['institucion'] ?? 'No especificado';
        $fechaInscripcion = isset($participante['fecha_inscripcion']) 
            ? date('d/m/Y H:i', strtotime($participante['fecha_inscripcion']))
            : date('d/m/Y H:i');
        
        // Asunto
        $asunto = "✅ Confirmación de Preinscripción - JAT2025";
        
        // Contenido HTML
        $mensaje = $this->plantillaConfirmacion($nombre, $email, $area, $celular, $institucion, $fechaInscripcion);
        
        // Enviar email
        return $this->enviar($email, $nombre, $asunto, $mensaje);
    }
    
    /**
     * Método genérico para enviar emails
     */
    public function enviar($destinatario, $nombreDestinatario, $asunto, $mensajeHtml, $mensajeTexto = '') {
        try {
            // Usar PHPMailer si está disponible
            if ($this->usePHPMailer && $this->mailer !== null) {
                return $this->enviarConPHPMailer($destinatario, $nombreDestinatario, $asunto, $mensajeHtml, $mensajeTexto);
            }
            
            // Fallback: usar mail() nativo
            return $this->enviarConMailNativo($destinatario, $nombreDestinatario, $asunto, $mensajeHtml);
            
        } catch (Exception $e) {
            // Log de error
            $errorMsg = "Error enviando email a {$destinatario}: " . $e->getMessage();
            error_log($errorMsg);
            $this->logEnvio($destinatario, $asunto, false, $errorMsg);
            return false;
        }
    }
    
    /**
     * Enviar con PHPMailer
     */
    private function enviarConPHPMailer($destinatario, $nombreDestinatario, $asunto, $mensajeHtml, $mensajeTexto) {
        // Limpiar destinatarios anteriores
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
        
        // Configurar destinatario
        $this->mailer->addAddress($destinatario, $nombreDestinatario);
        
        // Configurar contenido
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $asunto;
        $this->mailer->Body = $mensajeHtml;
        $this->mailer->AltBody = $mensajeTexto ?: strip_tags($mensajeHtml);
        
        // Enviar
        $resultado = $this->mailer->send();
        
        // Log de éxito
        $this->logEnvio($destinatario, $asunto, true, 'Enviado con PHPMailer');
        
        return $resultado;
    }
    
    /**
     * Enviar con mail() nativo de PHP
     */
    private function enviarConMailNativo($destinatario, $nombreDestinatario, $asunto, $mensajeHtml) {
        // Headers del email
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->config['from']['name'] . ' <' . $this->config['from']['email'] . '>',
            'Reply-To: ' . $this->config['reply_to']['name'] . ' <' . $this->config['reply_to']['email'] . '>',
            'X-Mailer: PHP/' . phpversion(),
            'X-Priority: 3'
        ];
        
        // Intentar envío con mail() nativo
        $resultado = mail($destinatario, $asunto, $mensajeHtml, implode("\r\n", $headers));
        
        if (!$resultado) {
            $this->logEnvio($destinatario, $asunto, false, 'Error con mail() nativo');
            return false;
        }
        
        // Log de éxito
        $this->logEnvio($destinatario, $asunto, true, 'Enviado con mail() nativo');
        
        return $resultado;
    }
    
    /**
     * Guardar log de envío
     */
    private function logEnvio($destinatario, $asunto, $exito, $detalle = '') {
        $logFile = $this->config['debug']['log_file'];
        $logDir = dirname($logFile);
        
        // Crear directorio de logs si no existe
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $estado = $exito ? 'ÉXITO' : 'ERROR';
        $logEntry = "[$timestamp] $estado - Para: $destinatario - Asunto: $asunto";
        
        if ($detalle) {
            $logEntry .= " - Detalle: $detalle";
        }
        
        $logEntry .= "\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Plantilla HTML de confirmación
     */
    private function plantillaConfirmacion($nombre, $email, $area, $celular, $institucion, $fechaInscripcion) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Confirmación JAT2025</title>
            <style>
                body {
                    font-family: 'Arial', 'Helvetica', sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #f5f5f5;
                    padding: 20px;
                }
                .container {
                    background: white;
                    border-radius: 0;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }
                .header {
                    background: #0A1E3D;
                    color: white;
                    padding: 30px;
                    text-align: center;
                    border-bottom: 4px solid #F7B800;
                }
                .header h1 {
                    margin: 0;
                    font-size: 26px;
                    font-weight: 700;
                    color: #F7B800;
                    text-transform: uppercase;
                }
                .header p {
                    margin: 10px 0 0 0;
                    color: white;
                    font-size: 14px;
                }
                .content {
                    padding: 30px;
                }
                .welcome {
                    background: #F7B800;
                    color: #0A1E3D;
                    padding: 20px;
                    margin-bottom: 25px;
                    text-align: center;
                    border-left: 5px solid #0A1E3D;
                }
                .welcome h2 {
                    margin: 0 0 10px 0;
                    font-size: 20px;
                    font-weight: bold;
                    color: #0A1E3D;
                }
                .welcome p {
                    margin: 0;
                    font-size: 15px;
                    color: #0A1E3D;
                }
                .info-card {
                    background: #f8f9fa;
                    border-left: 4px solid #F7B800;
                    padding: 20px;
                    margin: 20px 0;
                }
                .info-card h3 {
                    margin-top: 0;
                    color: #0A1E3D;
                    font-size: 18px;
                    margin-bottom: 15px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    margin: 10px 0;
                    padding: 8px 0;
                    border-bottom: 1px solid #e0e0e0;
                }
                .info-row:last-child {
                    border-bottom: none;
                }
                .label {
                    font-weight: 600;
                    color: #0A1E3D;
                }
                .value {
                    color: #333;
                    text-align: right;
                }
                .next-steps {
                    background: #0A1E3D;
                    color: white;
                    padding: 25px;
                    margin: 25px 0;
                    border-top: 4px solid #F7B800;
                }
                .next-steps h3 {
                    margin: 0 0 20px 0;
                    font-size: 18px;
                    color: #F7B800;
                }
                .step {
                    display: flex;
                    align-items: flex-start;
                    margin: 15px 0;
                    padding: 10px 0;
                }
                .step-number {
                    background: #F7B800;
                    color: #0A1E3D;
                    border-radius: 50%;
                    width: 35px;
                    height: 35px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 15px;
                    font-weight: bold;
                    font-size: 18px;
                    flex-shrink: 0;
                }
                .contact {
                    background: #f8f9fa;
                    border: 2px solid #F7B800;
                    padding: 20px;
                    margin: 25px 0;
                }
                .contact h3 {
                    color: #0A1E3D;
                    margin: 0 0 15px 0;
                    font-size: 18px;
                }
                .contact-item {
                    margin: 10px 0;
                    color: #333;
                }
                .footer {
                    background: #0A1E3D;
                    padding: 20px;
                    text-align: center;
                    color: white;
                    font-size: 13px;
                }
                .footer strong {
                    color: #F7B800;
                }
                .highlight {
                    background: #FFF8E1;
                    color: #0A1E3D;
                    padding: 15px;
                    margin: 20px 0;
                    text-align: center;
                    font-weight: 600;
                    border: 2px solid #F7B800;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>JAT 2025</h1>
                    <p>Jornada de Actualización Tecnológica 2025</p>
                    <p style='margin: 5px 0 0 0; font-size: 13px;'>IEST La Recoleta - Arequipa, Perú</p>
                </div>
                
                <div class='content'>
                    <div class='welcome'>
                        <h2>¡Bienvenido/a, $nombre!</h2>
                        <p>Tu preinscripción ha sido registrada exitosamente</p>
                    </div>
                    
                    <div class='highlight'>
                        ✓ Confirmación de preinscripción exitosa
                    </div>
                    
                    <div class='info-card'>
                        <h3>DATOS DE TU INSCRIPCIÓN</h3>
                        <div class='info-row'>
                            <span class='label'>Participante: </span>
                            <span class='value'>$nombre</span>
                        </div>
                        <div class='info-row'>
                            <span class='label'>Email: </span>
                            <span class='value'>$email</span>
                        </div>
                        <div class='info-row'>
                            <span class='label'>Área de Interés: </span>
                            <span class='value'>$area</span>
                        </div>
                        <div class='info-row'>
                            <span class='label'>Celular: </span>
                            <span class='value'>$celular</span>
                        </div>
                        <div class='info-row'>
                            <span class='label'>Institución: </span>
                            <span class='value'>$institucion</span>
                        </div>
                        <div class='info-row'>
                            <span class='label'>Fecha: </span>
                            <span class='value'>$fechaInscripcion</span>
                        </div>
                    </div>
                    
                    <div class='next-steps'>
                        <h3>PRÓXIMOS PASOS</h3>
                        <div class='step'>
                            <div class='step-number'>1</div>
                            <div>
                                <strong>Confirmación de Pago</strong><br>
                                <small>Recibirás instrucciones detalladas para completar tu inscripción definitiva</small>
                            </div>
                        </div>
                        <div class='step'>
                            <div class='step-number'>2</div>
                            <div>
                                <strong>Materiales del Evento</strong><br>
                                <small>Te enviaremos recursos y toda la información una semana antes del evento</small>
                            </div>
                        </div>
                        <div class='step'>
                            <div class='step-number'>3</div>
                            <div>
                                <strong>Asistencia al Evento</strong><br>
                                <small>¡Te esperamos el 14 de Noviembre de 2025 en IEST La Recoleta!</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class='contact'>
                        <h3>INFORMACIÓN DE CONTACTO</h3>
                        <div class='contact-item'>
                            <strong>Mg. Arturo Naupa</strong><br>
                            Unidad de Investigación
                        </div>
                        <div class='contact-item'>
                            <strong>Email:</strong> arturo.naupa@iestlarecoleta.edu.pe
                        </div>
                        <div class='contact-item'>
                            <strong>WhatsApp:</strong> 996 560 202
                        </div>
                        <div class='contact-item'>
                            <strong>Teléfono:</strong> (054) 270947
                        </div>
                        <div class='contact-item' style='margin-top: 15px;'>
                            <strong>Ubicación:</strong> Arequipa, Perú
                        </div>
                    </div>
                </div>
                
                <div class='footer'>
                    <p><strong>INSTITUTO DE EDUCACIÓN SUPERIOR TECNOLÓGICO</strong></p>
                    <p style='margin: 5px 0;'><strong>LA RECOLETA</strong></p>
                    <p style='font-size: 12px; margin: 10px 0 5px 0;'>Jornada de Actualización Tecnológica 2025</p>
                    <p style='font-size: 11px; margin-top: 10px; opacity: 0.8;'>
                        Este es un email automático. Por favor, no respondas a este mensaje.
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }
}

