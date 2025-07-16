<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Colaborador - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/colaboradores.css">
</head>
<body>
    <div class="form-container">
        <h2>Editar Colaborador</h2>

        <form action="<?php echo BASE_PATH; ?>/colaboradores/update" method="POST">
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
            </div>

            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea id="direccion" name="direccion" placeholder="Ingrese la dirección completa..."><?php echo htmlspecialchars($colaborador['direccion']); ?></textarea>
            </div>

            <input type="submit" value="Actualizar Cambios" class="submit-btn">
        </form>
    </div>
</body>
</html>