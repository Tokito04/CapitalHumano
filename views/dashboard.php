<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="main-page" data-base-path="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="navbar">
            <a href="<?php echo BASE_PATH; ?>/dashboard" class="active">Dashboard</a>
            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/usuarios">Usuarios</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/colaboradores">Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
            <a href="https://tokito04.github.io/CapitalHumano/" target="_blank" class="right">Documentación</a>
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="dashboard-container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <p>Dashboard de Estadísticas - Resumen visual de los datos de Capital Humano.</p>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number" id="totalColaboradores">--</div>
                <div class="stat-label">Total Colaboradores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalDepartamentos">--</div>
                <div class="stat-label">Departamentos</div>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h3>Colaboradores por Sexo</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoSexo"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Colaboradores por Rango de Edad</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoEdad"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Top 10 Colaboradores por Dirección</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoDireccion"></canvas>
                </div>
            </div>
        </div>

        <div class="action-buttons text-center">
            <a href="<?php echo BASE_PATH; ?>/colaboradores" class="btn">Ver Colaboradores</a>
            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/colaboradores/crear" class="btn btn-success ml-10">Añadir Colaborador</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores" class="btn btn-primary ml-10">Ver Reportes</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/dashboard.js"></script>
</body>
</html>