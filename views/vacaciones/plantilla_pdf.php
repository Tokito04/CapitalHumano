<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resuelto de Vacaciones</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; }
        .header { text-align: center; margin-bottom: 40px; }
        .content { margin: 0 50px; }
        h1 { font-size: 24px; }
        p { font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
<div class="header">
    <h1>Resuelto de Vacaciones</h1>
    <h3>Capital Humano, Inc.</h3>
</div>
<div class="content">
    <p><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></p>
    <p><strong>Colaborador:</strong> <?php echo htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido']); ?></p>
    <p><strong>Identificación:</strong> <?php echo htmlspecialchars($colaborador['identificacion']); ?></p>
    <p><strong>Cargo Actual:</strong> <?php echo htmlspecialchars($cargo_actual['ocupacion'] ?? 'N/A'); ?></p>
    <hr>
    <p>
        Por medio de la presente, se hace constar que al colaborador se le aprueba el uso de
        <strong><?php echo htmlspecialchars($dias_a_tomar); ?></strong> días de vacaciones,
        de un total de <strong><?php echo htmlspecialchars($dias_vacaciones); ?></strong> días acumulados a la fecha.
    </p>
    <br><br><br><br>
    <p>_________________________</p>
    <p>Firma del Supervisor</p>
</div>
</body>
</html><?php
