<?php
require_once __DIR__ . '/vendor/autoload.php';

echo "<pre>";
print_r(get_declared_classes());
echo "</pre>";

if (!class_exists('\Afip\Afip')) {
    die('La clase Afip\Afip no se encontró. Verifica el autoload y la instalación de Composer.');
} else {
    echo "La clase Afip\Afip se ha cargado correctamente.";
}

