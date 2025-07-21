<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Colaborador - Capital Humano</title>
    <link rel="stylesheet" href="<?php use App\Helpers\AuthHelper;
    use App\Models\Cargo;
    echo BASE_PATH ?>/css/main.css">
</head>
    <div class="navbar">
        <a href="<?php echo BASE_PATH; ?>/dashboard">Dashboard</a>
            <?php if ($_SESSION['user_rol'] == AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/usuarios">Usuarios</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/colaboradores" class="active">Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>


    <div class="form-container">
        <h2>Editar Colaborador</h2>
        <a href="<?php echo BASE_PATH; ?>/cargos/crear?colaborador_id=<?php echo $colaborador['id']; ?>" class="btn btn-success" style="margin-bottom: 20px;">Añadir Nuevo Cargo/Movimiento</a>

        <form action="<?php echo BASE_PATH; ?>/colaboradores/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($colaborador['id']); ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="primer_nombre">Primer Nombre:</label>
                    <input type="text" id="primer_nombre" name="primer_nombre" value="<?php echo htmlspecialchars($colaborador['primer_nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="segundo_nombre">Segundo Nombre:</label>
                    <input type="text" id="segundo_nombre" name="segundo_nombre" value="<?php echo htmlspecialchars($colaborador['segundo_nombre']); ?>">
                </div>

                <div class="form-group">
                    <label for="primer_apellido">Primer Apellido:</label>
                    <input type="text" id="primer_apellido" name="primer_apellido" value="<?php echo htmlspecialchars($colaborador['primer_apellido']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="segundo_apellido">Segundo Apellido:</label>
                    <input type="text" id="segundo_apellido" name="segundo_apellido" value="<?php echo htmlspecialchars($colaborador['segundo_apellido']); ?>">
                </div>

                <div class="form-group">
                    <label for="sexo">Sexo:</label>
                    <select id="sexo" name="sexo" required>
                        <option value="M" <?php echo ($colaborador['sexo'] === 'M') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo ($colaborador['sexo'] === 'F') ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="identificacion">Identificación:</label>
                    <input type="text" id="identificacion" name="identificacion" value="<?php echo htmlspecialchars($colaborador['identificacion']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($colaborador['fecha_nacimiento']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="correo_personal">Correo Personal:</label>
                    <input type="email" id="correo_personal" name="correo_personal" value="<?php echo htmlspecialchars($colaborador['correo_personal']); ?>">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($colaborador['telefono']); ?>">
                </div>

                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="text" id="celular" name="celular" value="<?php echo htmlspecialchars($colaborador['celular']); ?>">
                </div>

                <div class="form-group">
                    <label for="estatus">Estatus del Colaborador:</label>
                    <select id="estatus" name="estatus" class="form-control" required>
                        <option value="Activo Laborando" <?php echo ($colaborador['estatus'] === 'Activo Laborando') ? 'selected' : ''; ?>>Activo Laborando</option>
                        <option value="De Vacaciones" <?php echo ($colaborador['estatus'] === 'De Vacaciones') ? 'selected' : ''; ?>>De Vacaciones</option>
                        <option value="Licencia Médica" <?php echo ($colaborador['estatus'] === 'Licencia Médica') ? 'selected' : ''; ?>>Licencia Médica</option>
                        <option value="Incapacitado" <?php echo ($colaborador['estatus'] === 'Incapacitado') ? 'selected' : ''; ?>>Incapacitado</option>
                    </select>
                </div>
            </div>
            <div class="form-grid">
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
            </div>

            <div class="form-group">
                <label for="historial_academico_pdf">Historial Académico (PDF):</label>
                <input type="file" id="historial_academico_pdf" name="historial_academico_pdf" accept="application/pdf">

                <?php // Mostramos un enlace al PDF actual si existe ?>
                <?php if (!empty($colaborador['historial_academico_pdf'])): ?>
                    <p class="mt-2">
                        <a href="<?php echo BASE_PATH . '/uploads/pdf/' . htmlspecialchars($colaborador['historial_academico_pdf']); ?>" target="_blank">Ver Historial Actual</a>
                    </p>
                <?php endif; ?>
            </div>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea id="direccion" name="direccion" placeholder="Ingrese la dirección completa..."><?php echo htmlspecialchars($colaborador['direccion']); ?></textarea>
            </div>

            <input type="submit" value="Actualizar Cambios" class="submit-btn">
        </form>

        <hr style="margin-top: 40px;">
        <h3>Historial de Cargos y Movimientos</h3>

        <table class="table">
            <thead>
            <tr>
                <th>Ocupación</th>
                <th>Sueldo</th>
                <th>Departamento</th>
                <th>Fecha Contratación</th>
                <th>Estado</th>
                <th>Integridad</th> </tr>
            </thead>
            <tbody>
            <?php if (!empty($cargos)): ?>
                <?php foreach ($cargos as $cargo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cargo['ocupacion']); ?></td>
                        <td>$<?php echo number_format($cargo['sueldo'], 2); ?></td>
                        <td><?php echo htmlspecialchars($cargo['departamento_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cargo['fecha_contratacion']); ?></td>
                        <td>
                            <?php if ($cargo['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Histórico</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            // Llamamos a nuestro método de verificación
                            $esIntegro = Cargo::verificarIntegridad($cargo);
                            ?>
                            <?php if ($esIntegro): ?>
                                <span class="badge bg-success">Verificado</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inválido</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay cargos registrados para este colaborador.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>