<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="main-page">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="navbar">
            <a href="<?php echo BASE_PATH; ?>/dashboard" class="active">Dashboard</a>
            <a href="<?php echo BASE_PATH; ?>/colaboradores">Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="dashboard-container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <p>Has iniciado sesión correctamente en el Sistema de Capital Humano.</p>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number">--</div>
                <div class="stat-label">Colaboradores Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">--</div>
                <div class="stat-label">Nuevos Este Mes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">--</div>
                <div class="stat-label">Departamentos</div>
            </div>
        </div>

        <div class="action-buttons text-center">
            <a href="<?php echo BASE_PATH; ?>/colaboradores" class="btn">Ver Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/colaboradores/crear" class="btn btn-success ml-10">Añadir Colaborador</a>
        </div>
    </div>
</body>
</html>