<?php
require('fpdf/fpdf.php');
require_once '../controller/Venta_controller.php'; // Incluye la clase Venta
require('../vendor/autoload.php'); // Asegúrate de que Composer esté configurado correctamente
require_once '../tusfacturas/php/tusfacturas_sdk.php'; // Asegúrate de que la ruta sea correcta
require __DIR__ . '/../afip_config.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$idventa = base64_decode(isset($_GET['venta']) ? $_GET['venta'] : '');

class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function Row($data)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

try {
    $objVenta = new Venta();

    if ($idventa == "") {
        $detalle = $objVenta->Imprimir_Factura_DetalleVenta('0');
        $datos = $objVenta->Imprimir_Ticket_Venta('0');
    } else {
        $detalle = $objVenta->Imprimir_Factura_DetalleVenta($idventa);
        $datos = $objVenta->Imprimir_Ticket_Venta($idventa);
    }

    // Generar factura con AFIP usando el SDK de TusFacturas
    $facturaData = array(
        'tipo' => 'B',
        'operacion' => 'V',
        'punto_venta' => 1,
        'fecha' => date('d/m/Y'),
        'moneda' => 'PES',
        'cotizacion' => 1,
        'numero' => 0,
        'rubro' => 'Servicios',
        'detalle' => array(
            array(
                'cantidad' => 1,
                'afecta_stock' => 'N',
                'actualiza_precio' => 'N',
                'bonificacion_porcentaje' => 0,
                'producto' => array(
                    'descripcion' => 'Servicio de prueba',
                    'codigo' => 1,
                    'lista_precios' => 'standard',
                    'unidad_bulto' => 1,
                    'alicuota' => 21,
                    'precio_unitario_sin_iva' => 100
                )
            )
        ),
        'total' => 121
    );

    $clienteData = array(
        'documento_tipo' => 'CUIT',
        'documento_nro' => '20123456789',
        'razon_social' => 'Juan Pedro KJL',
        'email' => 'email@dominio.com',
        'domicilio' => 'Av Sta Fe 23132',
        'provincia' => 2,
        'envia_por_mail' => 'N',
        'condicion_pago' => 0,
        'condicion_iva' => 'CF'
    );

    // Usar el objeto del SDK para generar la factura
    $resultado = $tusfacturas_sdk_obj->comprobante_nuevo($facturaData, $clienteData);

    if ($tusfacturas_sdk_obj->hay_error($resultado)) {
        throw new Exception('Error al generar la factura: ' . implode(', ', $resultado->errores));
    }

    // Datos para generar el PDF
    $pdfData = [
        'tipo_comprobante' => $datos[0]["p_tipo_comprobante"],
        'empresa' => $datos[0]["p_empresa"],
        'propietario' => $datos[0]["p_propietario"],
        'direccion' => $datos[0]["p_direccion"],
        'numero_cedula' => $datos[0]["p_numero_nit"],
        'fecha_resolucion' => $datos[0]["p_fecha_resolucion"],
        'numero_resolucion' => $datos[0]["p_numero_resolucion"],
        'serie' => $datos[0]["p_serie"],
        'numero_comprobante' => $datos[0]["p_numero_comprobante"],
        'empleado' => $datos[0]["p_empleado"],
        'numero_venta' => $datos[0]["p_numero_venta"],
        'fecha_venta' => $datos[0]["p_fecha_venta"],
        'sumas' => $datos[0]["p_sumas"],
        'iva' => $datos[0]["p_iva"],
        'subtotal' => $datos[0]["p_subtotal"],
        'exento' => $datos[0]["p_exento"],
        'retenido' => $datos[0]["p_retenido"],
        'descuento' => $datos[0]["p_descuento"],
        'total' => $datos[0]["p_total"],
        'numero_productos' => $datos[0]["p_numero_productos"],
        'tipo_pago' => $datos[0]["p_tipo_pago"],
        'efectivo' => $datos[0]["p_pago_efectivo"],
        'pago_tarjeta' => $datos[0]["p_pago_tarjeta"],
        'numero_tarjeta' => substr($datos[0]["p_numero_tarjeta"], 0, 4).'-XXXX-XXXX-'.substr($datos[0]["p_numero_tarjeta"], 12, 16),
        'tarjeta_habiente' => $datos[0]["p_tarjeta_habiente"],
        'cambio' => $datos[0]["p_cambio"],
        'moneda' => $datos[0]["p_moneda"],
        'estado' => $datos[0]["p_estado"],
        'nombre_cliente' => $datos[0]["p_nombre_cliente"],
        'direccion_cliente' => $datos[0]["p_direccion_cliente"],
        'telefono_cliente' => substr($datos[0]["p_telefono_cliente"], 0, 4).'-'.substr($datos[0]["p_telefono_cliente"], 4),
        'sonletras' => $datos[0]["p_sonletras"],
    ];

    // Generar el PDF
    generarPDF($pdfData, $detalle);
} catch (Exception $e) {
    generarErrorPDF($e->getMessage());
}

// Función para generar el PDF
function generarPDF($pdfData, $detalle) {
    $pdf = new PDF_MC_Table('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->AliasNbPages();

    if ($pdfData['tipo_comprobante'] == '3') {
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetAutoPageBreak(true, 1);

        include('../includes/ticketheader.inc.php');

        $pdf->SetFont('Arial', '', 9.2);
        $pdf->Text(10, 10, '------------------------------------------------------------------');
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Text(60, 15, 'FACTURA ELECTRONICA');
        $pdf->Text(80, 20, 'F003 - '.str_pad($pdfData['numero_comprobante'], 9, '0', STR_PAD_LEFT));
        $pdf->Text(10, 25, 'Fecha : '.$pdfData['fecha_venta']);
        $pdf->Text(120, 25, 'RUC : '.$pdfData['numero_cedula']);
        $pdf->Text(10, 30, 'R.S : '.substr($pdfData['nombre_cliente'], 0, 35));
        $pdf->Text(10, 35, 'Dir : '.substr($pdfData['direccion_cliente'], 0, 37));
        $pdf->SetFont('Arial', '', 9.2);
        $pdf->SetXY(10, 40);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial','B',8.5);
        $pdf->Cell(30, 10, 'Cantid', 1, 0, 'L', 1);
        $pdf->Cell(80, 10, 'Descripcion', 1, 0, 'L', 1);
        $pdf->Cell(40, 10, 'Precio', 1, 0, 'L', 1);
        $pdf->Cell(40, 10, 'Total', 1, 0, 'L', 1);
        $pdf->SetFont('Arial','',8.5);
        $pdf->Text(10, 50, '-----------------------------------------------------------------------');
        $pdf->Ln(10);
        $item = 0;
        while($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
            $item++;
            $pdf->setX(10);
            $pdf->Cell(30, 10, $row['cantidad'], 1, 0, 'L');
            $pdf->SetFont('Arial','',6);
            $pdf->Cell(80, 10, substr($row['nombre_producto'], 0, 20), 1, 0, 'L', 1);
            $pdf->SetFont('Arial','',8.5);
            $pdf->Cell(40, 10, $row['importe'], 1, 0, 'L', 1);
            $pdf->Ln(10);
            $get_Y = $pdf->GetY();
        }

        $pdf->Text(10, $get_Y+5, '-----------------------------------------------------------------------');
        $pdf->SetFont('Arial','B',8.5);
        $pdf->Text(10, $get_Y + 10, 'G = GRAVADO');
        $pdf->Text(80, $get_Y + 10, 'E = EXENTO');

        $pdf->Text(10, $get_Y + 15, 'SUBTOTAL :');
        $pdf->Text(150, $get_Y + 15, $pdfData['sumas']);
        $pdf->Text(10, $get_Y + 20, 'IGV :');
        $pdf->Text(150, $get_Y + 20, $pdfData['iva']);
        $pdf->Text(10, $get_Y + 25, 'GRAVADO :');
        $pdf->Text(150, $get_Y + 25, $pdfData['subtotal']);
        $pdf->Text(10, $get_Y + 30, 'DESCUENTO :');
        $pdf->Text(150, $get_Y + 30, '-'.$pdfData['descuento']);
        $pdf->Text(10, $get_Y + 35, 'TOTAL A PAGAR :');
        $pdf->SetFont('Arial','B',8.5);
        $pdf->Text(150, $get_Y + 35, $pdfData['total']);
        $pdf->Text(10, $get_Y+40, '-----------------------------------------------------------------------');
        $pdf->Text(10, $get_Y + 45, 'Numero de Productos :');
        $pdf->Text(150, $get_Y + 45, $pdfData['numero_productos']);

        if($pdfData['tipo_pago'] == 'EFECTIVO'){
            $pdf->Text(24, $get_Y + 50, 'Efectivo :');
            $pdf->Text(150, $get_Y + 50, $pdfData['efectivo']);
            $pdf->Text(24, $get_Y + 55, 'Cambio :');
            $pdf->Text(150, $get_Y + 55, $pdfData['cambio']);

            $pdf->Text(10, $get_Y+60, '-----------------------------------------------------------------------');
            $pdf->SetFont('Arial','BI',8.5);
            $pdf->Text(10, $get_Y+65, 'Son: '.$pdfData['sonletras'].' soles');
            if($pdfData['estado'] == '2'):
                $pdf->Text(10, $get_Y+70, 'Esta venta ha sido al credito');
                $pdf->SetFont('Arial','B',8.5);
            endif;

            $writer = new PngWriter();
            $qrCode = QrCode::create($pdfData['numero_venta'])
                ->setSize(200);
            $result = $writer->write($qrCode);
            $file_name = 'qr_codes/'.$pdfData['numero_venta'].'.png';
            $result->saveToFile($file_name);
            $pdf->Image($file_name, 70, $get_Y + 75);
            $pdf->SetFont('Arial','B',8.5);
            $pdf->Text(60, $get_Y+105, 'GRACIAS POR SU COMPRA');
            $pdf->SetFillColor(0,0,0);
        } else if ($pdfData['tipo_pago'] == 'TARJETA'){
            $pdf->Text(20, $get_Y + 50, 'No. Tarjeta :');
            $pdf->Text(80, $get_Y + 50, $pdfData['numero_tarjeta']);
            $pdf->Text(23, $get_Y + 55, 'Debitado :');
            $pdf->Text(150, $get_Y + 55, $pdfData['total']);

            $pdf->Text(10, $get_Y+60, '-----------------------------------------------------------------------');
            $pdf->SetFont('Arial','BI',8.5);
            $pdf->Text(10, $get_Y+65, 'Precios en : '.$pdfData['moneda']);
            $pdf->SetFont('Arial','B',8.5);
            if($pdfData['estado'] == '2'):
                $pdf->Text(10, $get_Y+70, 'Esta venta ha sido al credito');
                $pdf->SetFont('Arial','B',8.5);
            endif;
            $pdf->Text(70, $get_Y+75, 'GRACIAS POR SU COMPRA');
            $pdf->SetFillColor(0,0,0);
            $pdf->Text(90, $get_Y+85, '*'.$pdfData['numero_venta'].'*');
        } else if ($pdfData['tipo_pago'] == 'EFECTIVO Y TARJETA'){
            $pdf->Text(24, $get_Y + 50, 'Efectivo :');
            $pdf->Text(150, $get_Y + 50, $pdfData['efectivo']);

            $pdf->Text(20, $get_Y + 55, 'No. Tarjeta :');
            $pdf->Text(80, $get_Y + 55, $pdfData['numero_tarjeta']);
            $pdf->Text(23, $get_Y + 60, 'Debitado :');
            $pdf->Text(150, $get_Y + 60, $pdfData['pago_tarjeta']);

            $pdf->Text(10, $get_Y+65, '-----------------------------------------------------------------------');
            $pdf->SetFont('Arial','BI',8.5);
            $pdf->Text(10, $get_Y+70, 'Precios en : '.$pdfData['moneda']);
            $pdf->SetFont('Arial','',8.5);
            $pdf->Text(10, $get_Y+75, 'Venta realizada con dos met');
        }
    }

    // Enviar el archivo PDF al navegador
    $pdf->Output('I', 'FACTURA.pdf', true);
}

// Función para generar el PDF de error
function generarErrorPDF($errorMessage) {
    $pdf = new PDF_MC_Table('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Text(10, 10, 'ERROR AL IMPRIMIR COTIZACION');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10, 20);
    $pdf->MultiCell(0, 10, $errorMessage);
    // Enviar el archivo PDF de error al navegador
    $pdf->Output('I', 'COTIZACION_ERROR.pdf', true);
}
?>
