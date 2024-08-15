<?php
require('fpdf/fpdf.php');

// Clase PDF
class PDF extends FPDF
{
    // Encabezado de página
    function Header()
    {
        if ($this->page == 1)
        {
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

            $mes = isset($_GET['mes']) ? $_GET['mes'] : '';
            $mesNum = intval(substr($mes, 0, 2)); // Corregido para obtener el mes como número entero
            $ano = substr($mes, 3, 4);

            // Validación del mes
            if ($mesNum >= 1 && $mesNum <= 12) {
                $mesNombre = strtoupper($meses[$mesNum - 1]);
            } else {
                $mesNombre = 'MES DESCONOCIDO'; // Manejo de caso inválido
            }

            $this->SetFont('Arial', 'B', 15);
            $this->Cell(98);
            $this->Cell(105, 10, 'VENTAS ANULADAS DEL MES ' . $mesNombre . ' del ' . $ano, 0, 0, 'C');
            $this->Ln(20);
        }
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(275, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'L');
        $this->Cell(43.2, 10, date('d/m/Y H:i:s'), 0, 0, 'C');
    }
}

$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$mes = DateTime::createFromFormat('m/Y', $mes)->format('m-Y');

$objVenta = new VentaModel();
$listado = $objVenta->Listar_Ventas('MES', $mes, '', 0);
$totales = $objVenta->Listar_Ventas_Totales('MES', $mes, '', 0);
$parametros = $objVenta->Ver_Moneda_Reporte();

$moneda = '';
foreach ($parametros as $column) {
    $moneda = $column['CurrencyName'];
}

// Continúa con el resto del código para generar el PDF...
try {
    // Instanciar la clase PDF
    $pdf = new PDF('L', 'mm', array(216, 330));
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(30, 5, 'Cantidad', 0, 0, 'L', 1);
    $pdf->Cell(130, 5, 'Producto', 0, 0, 'L', 1);
    $pdf->Cell(32, 5, 'Precio Venta', 0, 0, 'C', 1);
    $pdf->Cell(25, 5, 'Costo', 0, 0, 'C', 1);
    $pdf->Cell(25, 5, 'Exento', 0, 0, 'C', 1);
    $pdf->Cell(25, 5, 'Descuento', 0, 0, 'C', 1);
    $pdf->Cell(25, 5, 'Total', 0, 0, 'C', 1);
    $pdf->Cell(25, 5, 'Utilidad', 0, 0, 'C', 1);
    $pdf->Line(322, 28, 10, 28);
    $pdf->Line(322, 37, 10, 37);
    $pdf->Ln(9);

    $total = 0;
    $importe = 0;
    $utilidad = 0;

    if (is_array($listado) || is_object($listado)) {
        foreach ($listado as $row => $column) {
            $pdf->setX(9);
            $pdf->Cell(30, 5, $column["cantidad"], 0, 0, 'L', 1);
            $pdf->Cell(130, 5, $column["codigo_barra"] . ' - ' . $column["nombre_producto"] . ' ' . $column["siglas"] . ' ' . $column["nombre_marca"], 0, 0, 'L', 1);
            $pdf->Cell(32, 5, $column["precio_unitario"], 0, 0, 'C', 1);
            $pdf->Cell(25, 5, $column["precio_compra"], 0, 0, 'C', 1);
            $pdf->Cell(25, 5, $column["exento"], 0, 0, 'C', 1);
            $pdf->Cell(25, 5, $column["descuento"], 0, 0, 'C', 1);
            $pdf->Cell(25, 5, $column["importe"], 0, 0, 'C', 1);
            $pdf->Cell(25, 5, number_format($column["utilidad_total"], 2, '.', ','), 0, 0, 'C', 1);
            $pdf->Ln(6);
            $get_Y = $pdf->GetY();
            $total += $column["cantidad"];
            $utilidad += $column["utilidad_total"];
        }

        $importe = (($column["sumas"] + $column["iva"] + $column["total_exento"]) - $column["retenido"]) - $column["total_descuento"];

        $pdf->Line(322, $get_Y + 1, 10, $get_Y + 1);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Text(10, $get_Y + 10, 'TOTAL DE PRODUCTOS VENDIDOS : ' . number_format($total, 2, '.', ','));
        $pdf->Text(10, $get_Y + 15, 'MONTO INGRESADO POR VENTAS : ' . number_format($total_vendido, 2, '.', ','));
        $pdf->Text(10, $get_Y + 20, 'TOTAL DE IGV EN VENTAS : ' . number_format($total_iva, 2, '.', ','));
        $pdf->Text(10, $get_Y + 25, 'TOTAL RETENIDO : ' . number_format($total_retenido, 2, '.', ','));
        $pdf->Text(10, $get_Y + 30, 'TOTAL EXENTO : ' . number_format($total_exento, 2, '.', ','));
        $pdf->Text(10, $get_Y + 35, 'TOTAL EN DESCUENTOS : ' . number_format($total_descuento, 2, '.', ','));
        $pdf->Text(10, $get_Y + 40, 'GANANCIA TOTAL POR VENTAS : ' . number_format($utilidad, 2, '.', ','));
        $pdf->Text(250, $get_Y + 45, 'PRECIOS EN : ' . $moneda);
    }

    $pdf->Output('I', 'Ventas_Anuladas_' . $mes_actual . '_del_' . $ano . '.pdf');
} catch (Exception $e) {
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L', 'Letter');
    $pdf->Text(50, 50, 'ERROR AL IMPRIMIR');
    $pdf->SetFont('Times', '', 12);
    $pdf->Output();
}
