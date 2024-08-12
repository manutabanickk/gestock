<?php
// Asegúrate de que la ruta del autoload de Composer sea correcta
require_once __DIR__ . '/vendor/autoload.php';

// Verificación de la existencia de la clase Afip
if (!class_exists('Afip')) {
    die('La clase Afip no se encontró. Verifica el autoload y la instalación de Composer.');
}

// Rutas de los archivos de certificado y clave privada
$cert_path = __DIR__ . '/cert/certificado.pem';
$key_path = __DIR__ . '/cert/clave_privada.key';

// Verificación de la existencia del archivo de certificado
if (!file_exists($cert_path)) {
    die('El archivo de certificado no se encontró en la ruta: ' . $cert_path);
}

// Verificación de la existencia del archivo de clave privada
if (!file_exists($key_path)) {
    die('El archivo de clave privada no se encontró en la ruta: ' . $key_path);
}



?>
