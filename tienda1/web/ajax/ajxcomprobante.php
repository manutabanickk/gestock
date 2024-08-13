<?php 

spl_autoload_register(function($className) {
    $model = "../../model/" . $className . "_model.php";
    $controller = "../../controller/" . $className . "_controller.php";

    if (file_exists($model)) {
        require_once($model);
    }

    if (file_exists($controller)) {
        require_once($controller);
    }
});

$funcion = new ComprobanteModel(); // Instancia la clase correcta

if (isset($_POST['comprobante'])) {
    try {
        $proceso = $_POST['proceso'];
        $id = $_POST['id'];
        $comprobante = trim($_POST['comprobante']);
        $estado = trim($_POST['estado']);

        switch ($proceso) {
            case 'Registro':
                $funcion->Insertar_comprobante($comprobante); // Llamada al método correcto
                break;

            case 'Edicion':
                $funcion->Editar_comprobante($id, $comprobante, $estado); // Llamada al método correcto
                break;

            default:
                $data = "Error";
                echo json_encode($data);
                break;
        }
    } catch (Exception $e) {
        $data = "Error";
        echo json_encode($data);
    }
}
?>
