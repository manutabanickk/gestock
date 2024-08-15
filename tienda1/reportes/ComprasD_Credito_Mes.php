<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        if ($this->page == 1)
        {
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

            $mes = isset($_GET['mes']) ? $_GET['mes'] : '';
            $mes_num = (int) substr($mes, 0, 2); // Convertir a entero el mes
            $ano = substr($mes, 3, 4);

            $this->SetFont('Arial', 'B', 15);
            $this->Cell(98);
            $this->Cell(105, 10, 'COMPRAS (DETALLADAS) A CREDITO DEL MES ' . strtoupper($meses[$mes_num - 1] . ' del ' . $ano), 0, 0, 'C');
            $this->Ln(20);
        }
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(275, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'L');
        $this->Cell(43.2, 10, date('d/m/Y H:i:s'), 0, 0, 'C');
    }
}

function autoloadClasses($className)
{
    $model = "../model/" . $className . "_model.php";
    $controller = "../controller/" . $className . "_controller.php";

    require_once($model);
    require_once($controller);
}

spl_autoload_register('autoloadClasses');

$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$mes = DateTime::createFromFormat('m/Y', $mes)->format('m-Y');

$objCompra = new Compra();
$listado = $objCompra->Listar_Compras_Detalle('MES', $mes, '', '', 0);
$totales = $objCompra->Listar_Compras_Totales('MES', $mes, '', '', 0);
$parametros = $objCompra->Ver_Moneda_Reporte();

foreach ($parametros as $row => $column) {
    $moneda = $column['CurrencyName'];
}

foreach ($totales as $row => $column) {
    $total_iva = (float) $column['total_iva'];
    $total_exento = (float) $column['total_exento'];
    $total_retenido = (float) $column['total_retenido'];
    $total_comprado = (float) $column['total_comprado'];
}

$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$mes_actual = strtoupper($meses[(int)substr($mes, 0, 2) - 1]);
$ano = substr($mes, 3, 4);

try {
    $pdf = new PDF('L', 'mm', array(216, 330));
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(30, 5, 'Cantidad', 0, 0, 'L', 1);
    $pdf->Cell(31, 5, 'Cod. Barra', 0, 0, 'L', 1);
    $pdf->Cell(120, 5, 'Producto', 0, 0, 'L', 1);
    $pdf->Cell(40, 5, 'Marca', 0, 0, 'L', 1);
    $pdf->Cell(22, 5, 'Vence', 0, 0, 'L', 1);
    $pdf->Cell(22, 5, 'Costo', 0, 0, 'C', 1);
    $pdf->Cell(22, 5, 'Exento', 0, 0, 'C', 1);
    $pdf->Cell(22, 5, 'Total', 0, 0, 'C', 1);
    $pdf->Line(322, 28, 10, 28);
    $pdf->Line(322, 37, 10, 37);
    $pdf->Ln(9);
    $total = 0;

    if (is_array($listado) || is_object($listado)) {
        foreach ($listado as $row => $column) {

            $fecha_vence = $column["fecha_vence"];
            if (is_null($fecha_vence)) {
                $c_fecha_vence = '';
            } else {
                $c_fecha_vence = DateTime::createFromFormat('Y-m-d', $fecha_vence)->format('d/m/Y');
            }

            $pdf->setX(9);
            $pdf->Cell(30, 5, (float)$column["cantidad"], 0, 0, 'L', 1);
            $pdf->Cell(31, 5, $column["codigo_barra"], 0, 0, 'L', 1);
            $pdf->Cell(120, 5, $column["nombre_producto"] . ' ' . $column["siglas"], 0, 0, 'L', 1);
            $pdf->Cell(40, 5, $column["nombre_marca"], 0, 0, 'L', 1);
            $pdf->Cell(22, 5, $c_fecha_vence, 0, 0, 'L', 1);
            $pdf->Cell(22, 5, number_format((float)$column["precio_unitario"], 2, '.', ','), 0, 0, 'C', 1);
            $pdf->Cell(22, 5, number_format((float)$column["exento"], 2, '.', ','), 0, 0, 'C', 1);
            $pdf->Cell(22, 5, number_format((float)$column["importe"], 2, '.', ','), 0, 0, 'C', 1);
            $pdf->Ln(6);
            $get_Y = $pdf->GetY();
            $total++;
        }

        $pdf->Line(322, $get_Y + 1, 10, $get_Y + 1);
        $pdf->Text(10, $get_Y + 10, 'TOTAL DE PRODUCTOS COMPRADOS: ' . number_format($total, 2, '.', ','));
        $pdf->Text(10, $get_Y + 15, 'TOTAL GASTADO POR COMPRAS: ' . number_format($total_comprado, 2, '.', ','));
        $pdf->Text(10, $get_Y + 20, 'TOTAL DE IGV EN COMPRAS: ' . number_format($total_iva, 2, '.', ','));
        $pdf->Text(10, $get_Y + 25, 'TOTAL RETENIDO: ' . number_format($total_retenido, 2, '.', ','));
        $pdf->Text(10, $get_Y + 30, 'TOTAL EXENTO: ' . number_format($total_exento, 2, '.', ','));
        $pdf->Text(250, $get_Y + 45, 'PRECIOS EN: ' . $moneda);
    }

    $pdf->Output('I', 'ComprasD_Credito_' . $mes_actual . '_del_' . $ano . '.pdf');
} catch (Exception $e) {
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L', 'Letter');
    $pdf->Text(50, 50, 'ERROR AL IMPRIMIR');
    $pdf->SetFont('Times', '', 12);
    $pdf->Output();
}
?>
