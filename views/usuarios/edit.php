<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="main-page">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="navbar">
            <a href="<?php echo BASE_PATH; ?>/dashboard">Dashboard</a>
            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/usuarios" class="active">Usuarios</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/colaboradores">Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
            <a href="https://tokito04.github.io/CapitalHumano/" target="_blank" class="right">Documentación</a>
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h2>Editar Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h2>

        <form action="<?php echo BASE_PATH; ?>/usuarios/update" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="rol_id">Rol:</label>
                <select id="rol_id" name="rol_id" required>
                    <option value="1" <?php echo ($usuario['rol_id'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                    <option value="2" <?php echo ($usuario['rol_id'] == 2) ? 'selected' : ''; ?>>Consulta</option>
                </select>
            </div>

            <div class="form-group">
                <label for="activo">Estado del Usuario:</label>
                <select id="activo" name="activo" required>
                    <option value="1" <?php echo ($usuario['activo']) ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo (!$usuario['activo']) ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                <a href="<?php echo BASE_PATH; ?>/usuarios" class="btn btn-secondary ml-10">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>