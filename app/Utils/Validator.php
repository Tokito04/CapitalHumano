<?php

namespace App\Utils;

class Validator
{
    /**
     * Limpia una cadena de texto, eliminando etiquetas HTML y espacios extra.
     *
     * @param string $data El dato a limpiar.
     * @return string El dato limpio.
     */
    public static function sanitizeString($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * Valida que un dato no esté vacío.
     *
     * @param string $data El dato a validar.
     * @return bool True si es válido, false si no.
     */
    public static function validateRequired($data)
    {
        return !empty(trim($data));
    }

    /**
     * Valida que un email tenga un formato correcto.
     *
     * @param string $email El email a validar.
     * @return bool True si es válido, false si no.
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Sanitiza un email, eliminando caracteres no válidos.
     *
     * @param string $email El email a sanitizar.
     * @return string El email sanitizado.
     */
    public static function sanitizeEmail($email)
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeAlphaNumeric($data)
    {
        // Elimina todo excepto letras, números y el guión
        return preg_replace('/[^a-zA-Z0-9-]/', '', $data);
    }

    /**
     * Valida si una cadena tiene el formato de una cédula panameña.
     * Ej: 8-123-456, PE-123-456, N-123-456, E-123-456
     *
     * @param string $cedula La cédula a validar.
     * @return bool True si el formato es válido, false si no.
     */
    public static function validatePanamanianID($cedula)
    {
        // Expresión regular que acepta los formatos más comunes.
        $pattern = '/^([1-9]|1[0-3]|N|E|PE)-(\d{1,4})-(\d{1,6})$/i';
        return preg_match($pattern, $cedula) === 1;
    }

}