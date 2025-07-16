<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
</head>
<body>
<h2>Registrar Nuevo Usuario</h2>
<form method="POST">
    <label for="nombre">Nombre:</label><br>
    <input type="text" id="nombre" name="nombre" required><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Contraseña:</label><br>
    <input type="password" id="password" name="password" required><br>

    <label for="rol_id">Rol:</label><br>
    <select name="rol_id" id="rol_id">
        <option value="1">Administrador</option>
        <option value="2">Consulta</option>
    </select><br><br>

    <input type="submit" value="Registrar">
</form>
</body>
</html>