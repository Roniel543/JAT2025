<?php
session_start();
require_once '../config/conexion.php';

if ($_SESSION['admin_id'] ?? false) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validaciones
    if (empty($username) || strlen($username) < 3) die('Usuario inválido.');
    if (empty($nombre)) die('Nombre es obligatorio.');
    if (strlen($password) < 8) die('La contraseña debe tener al menos 8 caracteres.');
    if ($password !== $confirm) die('Las contraseñas no coinciden.');

    // Verificar si ya existe un admin (solo permitir 1 al inicio)
    $count = $pdo->query("SELECT COUNT(*) FROM usuarios_admin")->fetchColumn();
    if ($count > 0) die('Ya existe un usuario administrador.');

    // Registrar
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO usuarios_admin (username, password_hash, nombre_completo) VALUES (?, ?, ?)")
        ->execute([$username, $hash, $nombre]);

    $_SESSION['admin_id'] = $pdo->lastInsertId();
    $_SESSION['admin_nombre'] = $nombre;
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Admin JAT2025</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
  <div class="bg-white p-6 rounded shadow w-full max-w-md">
    <h2 class="text-xl font-bold text-center text-blue-800 mb-2">Registro de Administrador</h2>
    <p class="text-center text-gray-600 mb-6">Solo se permite un usuario inicial</p>

    <form method="POST">
      <div class="mb-3">
        <input type="text" name="username" placeholder="Nombre de usuario (ej: admin)" required class="w-full p-2 border rounded">
      </div>
      <div class="mb-3">
        <input type="text" name="nombre" placeholder="Nombre completo" required class="w-full p-2 border rounded">
      </div>
      <div class="mb-3">
        <input type="password" name="password" placeholder="Contraseña (mín. 8 caracteres)" required class="w-full p-2 border rounded">
      </div>
      <div class="mb-4">
        <input type="password" name="confirm" placeholder="Confirmar contraseña" required class="w-full p-2 border rounded">
      </div>
      <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 font-medium">
        Crear cuenta
      </button>
    </form>
  </div>
</body>
</html>