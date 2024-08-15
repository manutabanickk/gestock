<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        if ($this->page == 1)
        {
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto",
            "Septiembre","Octubre","Noviembre","Diciembre");

            $mes = isset($_GET['mes']) ? $_GET['mes'] : '';
            $ano = substr($mes,3,4);
            
            $this->SetFont('Arial','B',15);
            $this->Cell(103);
            $this->Cell(105,10,'SALIDAS DE PRODUCTOS DEL MES DE '.strtoupper($meses[intval(substr($mes, 0, 2))-1].' del '.$ano),0,0,'C');
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

function __autoload($className){
    $model = "../model/". $className ."_model.php";
    $controller = "../controller/". $className ."_controller.php";
    require_once($model);
    require_once($controller);
}

$objInventario = new Inventario();

$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$mes = DateTime::createFromFormat('m/Y', $mes)->format('Y-m');

$listado = $objInventario->Listar_Salidas($mes);

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto",
"Septiembre","Octubre","Noviembre","Diciembre");

$mes_actual = strtoupper($meses[intval(substr($mes, 5, 2))-1]);
$ano = substr($mes, 0, 4);

try {
    ob_start();  // Inicia el buffer de salida para capturar posibles errores antes de generar el PDF
    
    $pdf = new PDF('L','mm',array(216,330));
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',11);
    $pdf->SetFillColor(255,255,255);
    $pdf->Cell(14,10,' NO.',1,0,'L',1);
    $pdf->Cell(103,10,' PRODUCTOS',1,0,'L',1);
    $pdf->Cell(35,10,'MARCA',1,0,'C',1);
    $pdf->Cell(28,10,'FECHA',1,0,'C',1);
    $pdf->Cell(80,10,'MOTIVO',1,0,'C',1);
    $pdf->Cell(25,10,'CANTIDAD',1,0,'C',1);
    $pdf->Cell(25,10,'VALOR',1,0,'C',1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial','',10);
    $total_salida = 0;
    $total_final = 0;

    if (is_array($listado) || is_object($listado))
    {
        foreach ($listado as $row => $column) {

            $fecha_movimiento = isset($column["fecha_salida"]) ? $column["fecha_salida"] : null;
            $envio_date = is_null($fecha_movimiento) ? '' : DateTime::createFromFormat('Y-m-d',$fecha_movimiento)->format('d/m/Y');

            $idproducto = isset($column["idproducto"]) ? $column["idproducto"] : 'N/A';
            $nombre_producto = isset($column["nombre_producto"]) ? $column["nombre_producto"] : 'N/A';
            $siglas = isset($column["siglas"]) ? $column["siglas"] : '';
            $nombre_marca = isset($column["nombre_marca"]) ? $column["nombre_marca"] : 'N/A';
            $descripcion_salida = isset($column["descripcion_salida"]) ? $column["descripcion_salida"] : '';
            $cantidad_salida = isset($column["cantidad_salida"]) ? $column["cantidad_salida"] : 0;
            $costo_total_salida = isset($column["costo_total_salida"]) ? $column["costo_total_salida"] : 0;

            $pdf->setX(10);
            $pdf->Cell(14,9,$idproducto,1,0,'L',1);
            $pdf->Cell(103,9,$nombre_producto.' '.$siglas,1,0,'L',1);
            $pdf->Cell(35,9,$nombre_marca,1,0,'C',1);
            $pdf->Cell(28,9,$envio_date,1,0,'C',1);
            $pdf->Cell(80,9,$descripcion_salida,1,0,'L',1);
            $pdf->Cell(25,9,$cantidad_salida,1,0,'C',1);
            $pdf->Cell(25,9,$costo_total_salida,1,0,'C',1);

            $total_salida += $cantidad_salida;
            $total_final += $costo_total_salida;

            $pdf->Ln(8);
            $get_Y = $pdf->GetY();
        }

        $pdf->setXY(10,$get_Y+1);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(260,9,'TOTALES',1,0,'C',1);
        $pdf->Cell(25,9,number_format($total_salida, 2, '.', ','),1,0,'C',1);
        $pdf->Cell(25,9,number_format($total_final, 2, '.', ','),1,0,'C',1);
    }

    ob_end_clean();  // Limpia el buffer de salida antes de enviar el PDF
    $pdf->Output('I','Salidas_'.$mes_actual.'_del_'.$ano);
} catch (Exception $e) {
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L','Letter');
    $pdf->Text(50,50,'ERROR AL IMPRIMIR');
    $pdf->SetFont('Times','',12);
    $pdf->Output();
}

?>
