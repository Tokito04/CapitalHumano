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
}