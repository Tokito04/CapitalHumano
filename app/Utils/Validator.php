<?php

namespace App\Utils;

/**
 * Clase Validator
 *
 * Proporciona métodos estáticos para validar y sanitizar datos de entrada
 * en la aplicación de Capital Humano.
 *
 * @package App\Utils
 * @author Joseph Guerrero <joseph.guerrero2@utp.ac.pa> - ACJ Development Team
 * @version 1.0
 */
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
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
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

    /**
     * Sanitiza una cadena permitiendo solo caracteres alfanuméricos y algunos especiales.
     *
     * @param string $data El dato a sanitizar.
     * @return string El dato sanitizado.
     */
    public static function sanitizeAlphaNumeric($data)
    {
        // Permitir letras, números, espacios, guiones y algunos caracteres especiales
        return preg_replace('/[^a-zA-Z0-9\s\-_.]/', '', trim($data));
    }

    /**
     * Valida el formato de una cédula panameña.
     *
     * @param string $cedula La cédula a validar.
     * @return bool True si la cédula es válida, false si no.
     */
    public static function validatePanamanianID($cedula)
    {
        // Expresión regular que acepta los formatos más comunes.
        $pattern = '/^([1-9]|1[0-3]|N|E|PE)-(\d{1,4})-(\d{1,6})$/i';
        return preg_match($pattern, $cedula) === 1;
    }

}