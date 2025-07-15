<?php

// Incluimos el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

echo "¡Variables de entorno cargadas!";

// Aquí irá nuestro código de enrutamiento más adelante