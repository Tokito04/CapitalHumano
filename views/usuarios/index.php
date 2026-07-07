<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="main-page">
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="navbar">
        <a href="<?php echo BASE_PATH; ?>/dashboard">Dashboard</a>
        <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
            <a href="<?php echo BASE_PATH; ?>/usuarios">Usuarios</a>
        <?php endif; ?>
        <a href="<?php echo BASE_PATH; ?>/colaboradores">Colaboradores</a>
        <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
        <a href="https://tokito04.github.io/CapitalHumano/" target="_blank" class="right">Documentación</a>
        <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
    </div>
<?php endif; ?>

<div class="main-container">
    <h2>Gestión de Usuarios Administrativos</h2>
    <a href="<?php echo BASE_PATH; ?>/register" class="action-button">Añadir Nuevo Usuario</a>

    <table class="data-table">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre_rol']); ?></td>
                <td>
                    <?php if ($usuario['activo']): ?>
                        <span class="badge bg-success">Activo</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?php echo BASE_PATH; ?>/usuarios/editar?id=<?php echo $usuario['id']; ?>" class="edit-btn">Editar</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>