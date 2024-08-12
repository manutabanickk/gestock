<?php
session_start();
require_once __DIR__ . '/../../config/money_string.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Ajusta la ruta según sea necesario

require_once __DIR__ . '/../../controller/Venta_controller.php'; // Ajusta la ruta si es necesario
require_once __DIR__ . '/../../controller/Caja_controller.php'; // Ajusta la ruta si es necesario
require_once __DIR__ . '/../../afip_config.php'; // Incluye el archivo de configuración


// use Afip;

// Inicializa la clase Afip
// $afip = new Afip(array('CUIT' => 'XXXXXXXXX'));

// Instancia de la clase Venta y Caja
$funcion = new Venta();
$caja_funcion = new Caja();

$idproducto = isset($_GET['idproducto']) ? $_GET['idproducto'] : '';
if ($idproducto != "") {
    $funcion->Fechas_Vencimiento($idproducto);
}

if (!empty($_POST)) {
    try {
        // Tu código para procesar la venta...
        // Por ejemplo:
        $cuantos = $_POST['cuantos'];
        $stringdatos = $_POST['stringdatos'];
        $listadatos = explode('#', $stringdatos);
        $pagado = trim($_POST['pagado']);
        $comprobante = trim($_POST['comprobante']);
        $tipo_pago = trim($_POST['tipo_pago']);
        $idcliente = trim($_POST['idcliente']);
        $sumas = trim($_POST['sumas']);
        $iva = trim($_POST['iva']);
        $exento = trim($_POST['exento']);
        $retenido = trim($_POST['retenido']);
        $descuento = trim($_POST['descuento']);
        $total = trim($_POST['total']);
        $cambio = trim($_POST['cambio']);
        $efectivo = trim($_POST['efectivo']);
        $pago_tarjeta = trim($_POST['pago_tarjeta']);
        $numero_tarjeta = trim($_POST['numero_tarjeta']);
        $tarjeta_habiente = trim($_POST['tarjeta_habiente']);
        $fecha = date("Y-m-d");
        $son_letras = num2letras($total);
        $numero_tarjeta = str_replace("-", "", $numero_tarjeta);

        if ($tipo_pago == '1') {
            $tipo_pago = 'EFECTIVO';
        } elseif ($tipo_pago == '2') {
            $tipo_pago = 'TARJETA';
        } elseif ($tipo_pago == '3') {
            $tipo_pago = 'EFECTIVO Y TARJETA';
        }

        if ($idcliente == '') {
            if ($pagado == '1') {
                $funcion->Insertar_Venta($tipo_pago, $comprobante, $sumas, $iva, $exento, $retenido, $descuento, $total, $son_letras, $efectivo,
                    $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, 1, 0, $_SESSION['user_id']);
            } else if ($pagado == '0') {
                $funcion->Insertar_Venta($tipo_pago, $comprobante, $sumas, $iva, $exento, $retenido, $descuento, $total, $son_letras, $efectivo,
                    $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, 2, 0, $_SESSION['user_id']);
            }
        } else if ($idcliente != '') {
            if ($pagado == '1') {
                $funcion->Insertar_Venta($tipo_pago, $comprobante, $sumas, $iva, $exento, $retenido, $descuento, $total, $son_letras, $efectivo,
                    $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, 1, $idcliente, $_SESSION['user_id']);
            } else if ($pagado == '0') {
                $funcion->Insertar_Venta($tipo_pago, $comprobante, $sumas, $iva, $exento, $retenido, $descuento, $total, $son_letras, $efectivo,
                    $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, 2, $idcliente, $_SESSION['user_id']);
            }
        }

        for ($i = 0; $i < $cuantos; $i++) {
            list($idproducto, $cantidad, $precio_unitario, $exentos, $descuento, $fecha_vence, $importe) = explode('|', $listadatos[$i]);

            if ($fecha_vence == '') {
                $fecha_vence = '2000-01-01';
            } else {
                $fecha_vence = DateTime::createFromFormat('d/m/Y', $fecha_vence)->format('Y-m-d');
            }

            $funcion->Insertar_DetalleVenta($idproducto, $cantidad, $precio_unitario, $exentos, $descuento, $fecha_vence, $importe);
        }
    } catch (Exception $e) {
        $data = "Error";
        echo json_encode($data);
    }
}

