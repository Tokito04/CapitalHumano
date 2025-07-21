<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colaboradores - Capital Humano</title>
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
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="main-container">
        <h2>Lista de Colaboradores</h2>
        <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
            <a href="<?php echo BASE_PATH; ?>/colaboradores/crear" class="action-button">Añadir Nuevo Colaborador</a>
        <?php endif; ?>
        <table class="data-table">
            <thead>
            <tr>
                <th scope="col">Foto</th>
                <th>Identificación</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($colaboradores as $colaborador): ?>
                <tr>
                    <td>
                        <?php if (!empty($colaborador['foto_perfil'])): ?>
                            <a href="<?php echo BASE_PATH . '/uploads/fotos/' . htmlspecialchars($colaborador['foto_perfil']); ?>" target="_blank" class="enlace-modal-foto">
                                <img src="<?php echo BASE_PATH . '/uploads/fotos/' . htmlspecialchars($colaborador['foto_perfil']); ?>" alt="Foto de perfil" class="profile-photo">
                            </a>
                        <?php else: ?>
                            <span>Sin foto</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($colaborador['identificacion']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['correo_personal']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['celular']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['estatus']); ?></td>
                    <td>
                        <div class="table-actions">
                            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                                <a class = "edit-btn" href="<?php echo BASE_PATH; ?>/colaboradores/editar?id=<?php echo $colaborador['id']; ?>">Editar</a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_PATH; ?>/vacaciones?colaborador_id=<?php echo $colaborador['id']; ?>" class="btn btn-sm btn-warning">Vacaciones</a>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <nav aria-label="Navegación de páginas">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <div id="miModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal" sizes="20%">
    </div>
    <script>
        // Obtener los elementos del modal
        let modal = document.getElementById("miModal");
        let modalImg = document.getElementById("imgModal");
        let enlaces = document.getElementsByClassName("enlace-modal-foto");

        // Recorrer todos los enlaces y añadirles el evento de clic
        for (let i = 0; i < enlaces.length; i++) {
            enlaces[i].onclick = function(e) {
                e.preventDefault(); // Prevenir la acción por defecto del enlace
                modal.style.display = "block";
                modalImg.src = this.href;
            }
        }

        // Obtener el elemento <span> que cierra el modal
        let span = document.getElementsByClassName("close")[0];

        // Cuando el usuario hace clic en <span> (x), cerrar el modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // También cerrar el modal si se hace clic fuera de la imagen
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>