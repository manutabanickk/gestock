<?php
require('fpdf/fpdf.php');

// Asegúrate de que la ruta al archivo Parametro_model.php sea correcta
require_once('../model/Parametro_model.php');

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        if ($this->page == 1)
        {
            // Configuración del encabezado
            $this->SetFont('Arial','B',15);
            $this->Cell(98);
            $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre",
            "Octubre","Noviembre","Diciembre");

            $this->Cell(105,10,'VENTAS (DETALLADAS) VIGENTES DEL DIA '.strtoupper($dias[date('w')])." ".strtoupper(date('d'))." DE ".strtoupper(
            $meses[date('n')-1]). " DEL ".strtoupper(date('Y')),0,0,'C');
            $this->Ln(20);
        }
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(275,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'L');
        $this->Cell(43.2,10,date('d/m/Y H:i:s'),0,0,'C');
    }
}

// Instancia y uso de Parametromodel, asegúrate de usar el nombre correcto de la clase
$parametroModel = new Parametromodel(); // Asegúrate de que la clase se llame así
$parametros = $parametroModel->Ver_Moneda();

foreach ($parametros as $row => $column) {
    $moneda = $column['CurrencyName'];
}

// Resto del código para generar el PDF con FPDF
$pdf = new PDF('L','mm',array(216,330));
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(30,5,'Cantidad',0,0,'L',1);
$pdf->Cell(130,5,'Producto',0,0,'L',1);
$pdf->Cell(32,5,'Precio Venta',0,0,'C',1);
$pdf->Cell(25,5,'Costo',0,0,'C',1);
$pdf->Cell(25,5,'Exento',0,0,'C',1);
$pdf->Cell(25,5,'Descuento',0,0,'C',1);
$pdf->Cell(25,5,'Total',0,0,'C',1);
$pdf->Cell(25,5,'Utilidad',0,0,'C',1);
$pdf->Line(322,28,10,28);
$pdf->Line(322,37,10,37);
$pdf->Ln(9);

// Procesamiento de datos y salida del PDF
// ...

$pdf->Output('I','VentasD_Vigentes_del_'.date('d/m/Y').'.pdf');
