<?php
require_once 'model/Conexion.php'; // Asegúrate de que la ruta sea correcta

$conexion = new Conexion();

try {
    $db = $conexion->Conectar();
    if ($db) {
        echo "Conexión exitosa";    
    } else {
        echo "Conexión fallida";
    }
} catch (Exception $e) {
    echo "Error al intentar conectarse: " . $e->getMessage();
}
?>
