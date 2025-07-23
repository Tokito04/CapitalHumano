<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Cargo - Capital Humano</title>
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

    <div class="form-container">
        <h2>Añadir Nuevo Cargo para: <?php echo htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido']); ?></h2>

        <form action="<?php echo BASE_PATH; ?>/cargos/store" method="POST">
            <input type="hidden" name="colaborador_id" value="<?php echo htmlspecialchars($colaborador['id']); ?>">

            <div class="form-group">
                <label for="sueldo">Sueldo:</label>
                <input type="number" step="0.01" id="sueldo" name="sueldo" required>
            </div>

            <div class="form-group">
                <label for="departamento_id">Departamento:</label>
                <select id="departamento_id" name="departamento_id" class="form-control" required>
                    <option value="">-- Seleccione un Departamento --</option>
                    <?php foreach ($departamentos as $depto): ?>
                        <option value="<?php echo htmlspecialchars($depto['id_departamento']); ?>">
                            <?php echo htmlspecialchars($depto['nombre_departamento']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ocupacion">Ocupación (Ej.: Programador, Electricista):</label>
                <input type="text" id="ocupacion" name="ocupacion" required>
            </div>

            <div class="form-group">
                <label for="tipo_contrato">Tipo de Contrato:</label>
                <select id="tipo_contrato" name="tipo_contrato" required>
                    <option value="">Seleccionar...</option>
                    <option value="Permanente">Permanente</option>
                    <option value="Eventual">Eventual</option>
                    <option value="Interino">Interino</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_contratacion">Fecha de Contratación:</label>
                <input type="date" id="fecha_contratacion" name="fecha_contratacion" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Cargo</button>
                <a href="<?php echo BASE_PATH; ?>/colaboradores/editar?id=<?php echo $colaborador['id']; ?>" class="btn btn-secondary ml-10">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>