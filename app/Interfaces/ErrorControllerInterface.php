<?php

namespace App\Interfaces;

/**
 * Interface ErrorControllerInterface
 * Define un contrato para los controladores que manejan errores HTTP.
 * Cualquier controlador de errores debe ser capaz de manejar un "Not Found" (404)
 * y un "Server Error" (500).
 */
interface ErrorControllerInterface
{
    /**
     * Maneja y muestra una página de error 404 (Not Found).
     */
    public function notFound();

    /**
     * Maneja y muestra una página de error 500 (Internal Server Error).
     * @param string $message Un mensaje de error opcional para depuración.
     */
    public function serverError($message = 'Ha ocurrido un error inesperado.');
}