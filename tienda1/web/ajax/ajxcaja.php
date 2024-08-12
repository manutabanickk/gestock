<?php
session_start();

spl_autoload_register(function ($className) {
    $model = "../../model/" . $className . "_model.php";
    $controller = "../../controller/" . $className . "_controller.php";

    echo "Autoloading class: $className\n"; // Mensaje de depuración

    if (file_exists($model)) {
        echo "Loading model: $model\n"; // Mensaje de depuración
        require_once($model);
    } else {
        echo "Model file not found: $model\n"; // Mensaje de depuración
    }

    if (file_exists($controller)) {
        echo "Loading controller: $controller\n"; // Mensaje de depuración
        require_once($controller);
    } else {
        echo "Controller file not found: $controller\n"; // Mensaje de depuración
    }
});

echo "Creating Caja instance\n"; // Mensaje de depuración
$funcion = new Caja();

$iva = isset($_GET['movimientos']) ? $_GET['movimientos'] : '';
if (!$iva == "") {
    echo "Calling Get_Movimientos\n"; // Mensaje de depuración
    $funcion->Get_Movimientos();
}

if (isset($_POST['proceso'])) {
    try {
        $proceso = $_POST['proceso'];

        if ($proceso != 'Validar' && $proceso != 'Cerrar-M') {
            $monto = trim($_POST['monto']);
            $cantidad = trim($_POST['cantidad']);
            $descripcion = trim($_POST['descripcion']);
        } else if ($proceso == 'Cerrar-M') {
            $id = trim($_POST['id']);
        }

        echo "Processing action: $proceso\n"; // Mensaje de depuración

        switch ($proceso) {
            case 'Validar':
                $funcion->Validar_Caja();
                break;

            case 'Abrir':
                $funcion->Abrir_Caja($cantidad);
                break;

            case 'Update':
                $funcion->Update_Caja($cantidad);
                break;

            case 'Cerrar':
                $funcion->Cerrar_Caja($cantidad);
                break;

            case 'Cerrar-M':
                $funcion->Cerrar_Caja_Manual($id);
                break;

            case 'Devolucion':
                $funcion->Insertar_Movimiento(2, $monto, $descripcion);
                break;

            case 'Prestamo':
                $funcion->Insertar_Movimiento(3, $monto, $descripcion);
                break;

            case 'Gasto':
                $funcion->Insertar_Movimiento(4, $monto, $descripcion);
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
