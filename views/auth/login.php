// Añadir esto al principio del body en views/auth/login.php
<?php if (isset($_GET['error'])): ?>
    <p style="color: red;">El email o la contraseña son incorrectos.</p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h2>Iniciar Sesión</h2>
<form action="" method="POST">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br>
    <label for="password">Contraseña:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Entrar">
</form>
</body>
</html>