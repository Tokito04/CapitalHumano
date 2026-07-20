<?php

namespace App\Helpers;

/**
 * Clase UploadHelper
 *
 * Valida y almacena de forma segura archivos subidos por el usuario:
 * whitelist de extensión, verificación de tipo MIME real (magic bytes,
 * no el Content-Type declarado por el cliente), límite de tamaño y
 * nombre de archivo generado aleatoriamente (no se conserva el nombre
 * original ni su extensión declarada).
 *
 * @package App\Helpers
 */
class UploadHelper
{
    const PERFIL_ALLOWED = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    const DOCUMENTO_ALLOWED = [
        'pdf' => 'application/pdf',
    ];

    const MAX_BYTES = 5 * 1024 * 1024; // 5 MB

    /**
     * Valida y mueve un archivo subido a $destDir.
     *
     * @param array $file Entrada de $_FILES['campo']
     * @param string $destDir Directorio destino (con slash final)
     * @param array $allowedExtToMime Mapa extensión => MIME esperado
     * @return string Nombre de archivo generado
     * @throws \RuntimeException Si el archivo no pasa la validación
     */
    public static function guardar(array $file, string $destDir, array $allowedExtToMime): string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('El archivo no se subió correctamente.');
        }

        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('El archivo excede el tamaño máximo permitido (5 MB).');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Subida de archivo inválida.');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($extension, $allowedExtToMime)) {
            throw new \RuntimeException('Tipo de archivo no permitido.');
        }

        // Verificamos el tipo real del contenido (magic bytes), no el
        // Content-Type ni la extensión declarados por el cliente.
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeReal !== $allowedExtToMime[$extension]) {
            throw new \RuntimeException('El contenido del archivo no coincide con el tipo declarado.');
        }

        if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
            throw new \RuntimeException('No se pudo preparar el directorio de destino.');
        }

        // Nombre generado aleatoriamente: no se conserva el nombre ni la
        // extensión original del archivo del cliente.
        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetFile = rtrim($destDir, '/') . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new \RuntimeException('No se pudo guardar el archivo.');
        }

        chmod($targetFile, 0644);

        return $fileName;
    }
}
