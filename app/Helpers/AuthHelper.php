<?php

namespace App\Helpers;

class AuthHelper
{
    // Definimos los roles como constantes para evitar errores de tipeo.
    // Asumimos que 1 = Administrador, 2 = Consulta.
    const ROL_ADMINISTRADOR = 1;
    const ROL_CONSULTA = 2;

    /**
     * Verifica si el usuario actual tiene el rol requerido.
     * Si no, lo redirige al dashboard.
     *
     * @param int $rolRequerido El ID del rol necesario para acceder.
     */
    public static function verificarPermiso($rolRequerido)
    {
        // Primero, nos aseguramos de que el usuario haya iniciado sesión.
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/login');
            exit();
        }

        // Verificamos si el rol del usuario no es el requerido.
        if ($_SESSION['user_rol'] != $rolRequerido) {
            // Si no tiene permiso, lo enviamos al dashboard.
            header('Location: ' . BASE_PATH . '/dashboard');
            exit();
        }
    }
}