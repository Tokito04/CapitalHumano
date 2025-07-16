<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/colaboradores.css">
</head>

<body>
    <div class="main-container">
        <h2>Lista de Colaboradores</h2>
        <a href="<?php echo BASE_PATH; ?>/colaboradores/crear" class="action-button">Añadir Nuevo Colaborador</a>

        <table class="data-table">
            <thead>
            <tr>
                <th>Identificación</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($colaboradores as $colaborador): ?>
                <tr>
                    <td><?php echo htmlspecialchars($colaborador['identificacion']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['correo_personal']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['celular']); ?></td>
                    <td>
                        <div class="table-actions">
                            <a class = "edit-btn" href="<?php echo BASE_PATH; ?>/colaboradores/editar?id=<?php echo $colaborador['id']; ?>">Editar</a>

                            <form action="<?php echo BASE_PATH; ?>/colaboradores/status" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cambiar el estado de este colaborador?');">
                                <input type="hidden" name="id" value="<?php echo $colaborador['id']; ?>">
                                <input type="hidden" name="estado_actual" value="<?php echo $colaborador['activo'] ? '1' : '0'; ?>">
                                <button type="submit">
                                    <?php echo $colaborador['activo'] ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>