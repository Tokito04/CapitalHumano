<?php

namespace App\Helpers;

/**
 * Clase AuthHelper
 *
 * Helper que proporciona funcionalidades de autenticación y autorización
 * para el sistema de Capital Humano. Maneja verificación de permisos por roles.
 *
 * @package App\Helpers
 * @author Tu Nombre
 * @version 1.0
 */
class AuthHelper
{
    /**
     * Rol de Administrador - Acceso completo al sistema
     */
    const ROL_ADMINISTRADOR = 1;

    /**
     * Rol de Consulta - Solo acceso de lectura
     */
    const ROL_CONSULTA = 2;

    /**
     * Verifica si el usuario actual tiene el rol requerido.
     * Si no tiene permisos, redirige al dashboard.
     * Si no está autenticado, redirige al login.
     *
     * @param int $rolRequerido El ID del rol necesario para acceder
     * @return void
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