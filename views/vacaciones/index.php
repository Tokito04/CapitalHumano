<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaciones - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="main-page">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="navbar">
            <a href="<?php echo BASE_PATH; ?>/dashboard">Dashboard</a>
            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/usuarios">Usuarios</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/colaboradores" class="active">Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
            <a href="https://tokito04.github.io/CapitalHumano/" target="_blank" class="right">Documentación</a>
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="vacation-container">
        <h2>Módulo de Vacaciones para: <?php echo htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido']); ?></h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Cálculo de Vacaciones</h5>
                <p class="card-text">
                    Según la primera fecha de contratación (simulada), el colaborador tiene derecho a:
                </p>
                <div class="text-center">
                    <h3><?php echo $dias_vacaciones_ganados; ?> días de vacaciones.</h3>
                </div>
            </div>
        </div>
        <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Generar Resuelto de Vacaciones</h5>
                    <p class="card-text">Puedes generar un documento PDF con el resuelto de vacaciones para el colaborador.</p>

                    <form action="<?php echo BASE_PATH; ?>/vacaciones/generar" method="POST" target="_blank">
                        <input type="hidden" name="colaborador_id" value="<?php echo $colaborador['id']; ?>">
                        <input type="hidden" name="dias_vacaciones" value="<?php echo $dias_vacaciones_ganados; ?>">

                        <div class="form-group">
                            <label for="dias_a_tomar">Días a solicitar (mínimo 7):</label>
                            <input type="number" name="dias_a_tomar" id="dias_a_tomar" min="7" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger">Generar PDF</button>
                            <a href="<?php echo BASE_PATH; ?>/colaboradores" class="btn btn-secondary ml-10">Volver a Colaboradores</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>