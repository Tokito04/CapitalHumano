<?php

namespace App\Controllers;

use App\Interfaces\ErrorControllerInterface;

/**
 * Controlador para manejar y mostrar páginas de error personalizadas.
 * Implementa la ErrorControllerInterface para asegurar que los métodos estándar
 * de manejo de errores estén presentes.
 */
class ErrorController implements ErrorControllerInterface
{
    /**
     * Maneja errores 404. Establece el código de respuesta HTTP
     * y carga la vista de error 404.
     */
    public function notFound()
    {
        http_response_code(404);
        require_once __DIR__ . '/../../views/errors/404.php';
    }

    /**
     * Maneja errores 500. Establece el código de respuesta HTTP
     * y carga la vista de error 500.
     *
     * @param string $message Un mensaje de error para fines de depuración.
     */
    public function serverError($message = 'Error interno del servidor.')
    {
        http_response_code(500);
        // En un entorno real, aquí registraríamos el $message en un archivo de log.
        // Por ahora, solo cargamos la vista.
        require_once __DIR__ . '/../../views/errors/500.php';
    }
}