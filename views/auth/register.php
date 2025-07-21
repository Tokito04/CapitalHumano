<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2>Registrar Nuevo Usuario</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="rol_id">Rol:</label>
                <select name="rol_id" id="rol_id">
                    <option value="1">Administrador</option>
                    <option value="2">Consulta</option>
                </select>
            </div>

            <input type="submit" value="Registrar" class="submit-btn">
        </form>
    </div>
</body>
</html>