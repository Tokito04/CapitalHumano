<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                El email o la contraseña son incorrectos.
            </div>
        <?php endif; ?>

        <h2>Iniciar Sesión</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="submit" value="Entrar" class="submit-btn">
        </form>
    </div>
</body>
</html>