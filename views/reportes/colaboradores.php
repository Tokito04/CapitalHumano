<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Capital Humano</title>
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
    <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores" class="active">Reportes</a>
    <a href="https://tokito04.github.io/CapitalHumano/" target="_blank" class="right">Documentación</a>
    <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
</div>
<?php endif; ?>

<div class="main-container">
    <h2>Reporte de Colaboradores y Sueldos</h2>

    <form action="" method="GET" class="form-inline mb-4">
        <div class="form-group mr-2">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, apellido..." value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
        </div>
        <div class="form-group mr-2">
            <select name="sexo" class="form-control">
                <option value="">Cualquier Sexo</option>
                <option value="M" <?php echo (($_GET['sexo'] ?? '') === 'M') ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?php echo (($_GET['sexo'] ?? '') === 'F') ? 'selected' : ''; ?>>Femenino</option>
            </select>
        </div>
        <div class="form-group mr-2">
            <input type="number" name="salario_min" class="form-control" placeholder="Salario Mínimo" value="<?php echo htmlspecialchars($_GET['salario_min'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores/exportar?<?php echo http_build_query($_GET); ?>" class="btn btn-success ml-2">Exportar a Excel</a>
        <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores" class="btn btn-secondary ml-2">Limpiar</a>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th>Identificación</th>
            <th>Nombre Completo</th>
            <th>Correo</th>
            <th>Departamento</th>
            <th>Ocupación</th>
            <th>Sueldo Actual</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($datosReporte as $fila): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['identificacion']); ?></td>
                <td><?php echo htmlspecialchars($fila['primer_nombre'] . ' ' . $fila['primer_apellido']); ?></td>
                <td><?php echo htmlspecialchars($fila['correo_personal']); ?></td>
                <td><?php echo htmlspecialchars($fila['departamento'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($fila['ocupacion'] ?? 'N/A'); ?></td>
                <td>
                    <?php if (isset($fila['sueldo'])): ?>
                        $<?php echo number_format($fila['sueldo'], 2); ?>
                    <?php else: ?>
                        Sin asignar
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php
                // Mantenemos los filtros actuales en los enlaces de paginación
                $filtros_query = http_build_query(array_merge($_GET, ['pagina' => $i]));
                ?>
                <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo $filtros_query; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>