<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-g">
    <title>Dashboard</title>
</head>
<body>
<h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
<p>Has iniciado sesión correctamente.</p>
<a href="/logout">Cerrar Sesión</a>
</body>
</html>