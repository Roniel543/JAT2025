<?php
// Configuración de la base de 
include 'config/conexion.php';


// Recuperar y sanitizar datos
$nombres = trim($_POST['nombres'] ?? '');
$email   = trim($_POST['email'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$institucion = trim($_POST['institucion'] ?? '');
$area_interes = trim($_POST['area_interes'] ?? '');

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
if (empty($area_interes)) {
    header("Location: error.html?tipo=validacion&campo=area_interes");
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
        INSERT INTO preinscripciones (nombres, email, celular, institucion, area_interes)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt_insert->execute([$nombres, $email, $celular, $institucion, $area_interes]);

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