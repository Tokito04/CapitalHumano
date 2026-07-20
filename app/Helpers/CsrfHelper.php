<?php

namespace App\Helpers;

/**
 * Clase CsrfHelper
 *
 * Genera y valida tokens CSRF (sincronizador de token en sesión) para
 * proteger los formularios que modifican estado (POST).
 *
 * @package App\Helpers
 */
class CsrfHelper
{
    const SESSION_KEY = 'csrf_token';

    /**
     * Devuelve el token CSRF actual, generándolo si no existe.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Devuelve el HTML de un campo oculto listo para insertar en un formulario.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida el token recibido en $_POST contra el de la sesión.
     * Comparación en tiempo constante para evitar timing attacks.
     */
    public static function validar(): bool
    {
        $enviado = $_POST['csrf_token'] ?? '';
        $esperado = $_SESSION[self::SESSION_KEY] ?? '';

        return $esperado !== '' && is_string($enviado) && hash_equals($esperado, $enviado);
    }
}
