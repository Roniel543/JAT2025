<?php
session_start();
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_destroy();
    header('Location: login.php');
    exit;
}
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, nombre_completo, password_hash FROM usuarios_admin WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_nombre'] = $user['nombre_completo'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login - Admin JAT2025</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form method="POST" class="bg-white p-6 rounded shadow w-80">
    <h2 class="text-xl font-bold mb-4 text-center">Panel de Administración</h2>
    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <input type="text" name="username" placeholder="Usuario" required class="w-full p-2 border rounded mb-3">
    <input type="password" name="password" placeholder="Contraseña" required class="w-full p-2 border rounded mb-3">
    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Ingresar</button>
    <p class="text-center mt-4 text-sm">
      ¿No tienes cuenta? <a href="registro.php" class="text-blue-600">Regístrate</a>
    </p>
  </form>
</body>
</html>