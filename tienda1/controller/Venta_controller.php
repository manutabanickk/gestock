<?php
require_once __DIR__ . '/../afip_config.php'; // Asegura la ruta correcta al archivo
require_once __DIR__ . '/../model/Venta_model.php'; // Asegura la ruta correcta al archivo VentaModel.php

class Venta {

    private $afip;
    private $ventaModel;

    public function __construct() {
        global $afip;
        $this->afip = $afip;
        $this->ventaModel = new VentaModel(); // Crear una instancia de VentaModel
    }

    public function Ver_Moneda_Reporte() {
        $filas = $this->ventaModel->Ver_Moneda_Reporte();
        return $filas;
    }

    public function Listar_Ventas($criterio, $date, $date2, $estado) {
        $filas = $this->ventaModel->Listar_Ventas($criterio, $date, $date2, $estado);
        return $filas;
    }

    public function Listar_Ventas_Totales($criterio, $date, $date2, $estado) {
        $filas = $this->ventaModel->Listar_Ventas_Totales($criterio, $date, $date2, $estado);
        return $filas;
    }

    public function Listar_Ventas_Detalle($criterio, $date, $date2, $estado) {
        $filas = $this->ventaModel->Listar_Ventas_Detalle($criterio, $date, $date2, $estado);
        return $filas;
    }

    public function Imprimir_Ticket_DetalleVenta($idVenta) {
        $filas = $this->ventaModel->Imprimir_Ticket_DetalleVenta($idVenta);
        return $filas;
    }

    public function Imprimir_Factura_DetalleVenta($idVenta) {
        $filas = $this->ventaModel->Imprimir_Factura_DetalleVenta($idVenta);
        return $filas;
    }

    public function Imprimir_Ticket_Venta($idVenta) {
        $filas = $this->ventaModel->Imprimir_Ticket_Venta($idVenta);
        return $filas;
    }

    public function Imprimir_Corte_Z_Dia($date) {
        $filas = $this->ventaModel->Imprimir_Corte_Z_Dia($date);
        return $filas;
    }

    public function Imprimir_Corte_Z_Mes($date) {
        $filas = $this->ventaModel->Imprimir_Corte_Z_Mes($date);
        return $filas;
    }

    public function Listar_Detalle($idVenta) {
        $filas = $this->ventaModel->Listar_Detalle($idVenta);
        return $filas;
    }

    public function Listar_Info($idVenta) {
        $filas = $this->ventaModel->Listar_Info($idVenta);
        return $filas;
    }

    public function Count_Ventas($criterio, $date, $date2) {
        $filas = $this->ventaModel->Count_Ventas($criterio, $date, $date2);
        return $filas;
    }

    public function Listar_Clientes() {
        $filas = $this->ventaModel->Listar_Clientes();
        return $filas;
    }

    public function Listar_Comprobantes() {
        $filas = $this->ventaModel->Listar_Comprobantes();
        return $filas;
    }

    public function Listar_Empresas() {
        $filas = $this->ventaModel->Listar_Empresas();
        return $filas;
    }

    public function Autocomplete_Producto($search) {
        return $this->ventaModel->Autocomplete_Producto($search);
    }
    

    public function Insertar_Venta($tipo_pago, $tipo_comprobante, $sumas, $iva, $exento, $retenido, $descuento, $total, $sonletras, $pago_efectivo, $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, $estado, $idcliente, $idusuario) {
        return $this->ventaModel->Insertar_Venta($tipo_pago, $tipo_comprobante, $sumas, $iva, $exento, $retenido, $descuento, $total, $sonletras, $pago_efectivo, $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, $estado, $idcliente, $idusuario);
    }
    
    public function Insertar_DetalleVenta($idproducto, $cantidad, $precio_unitario, $exento, $descuento, $fecha_vence, $importe) {
        return $this->ventaModel->Insertar_DetalleVenta($idproducto, $cantidad, $precio_unitario, $exento, $descuento, $fecha_vence, $importe);
    }
    
    public function Anular_Venta($idventa) {
        return $this->ventaModel->Anular_Venta($idventa);
    }
    
    public function Fechas_Vencimiento($idproducto) {
        return $this->ventaModel->Fechas_Vencimiento($idproducto);
    }
    
    public function Finalizar_Venta($idventa) {
        return $this->ventaModel->Finalizar_Venta($idventa);
    }
    

    public function GenerarFacturaAFIP($data) {
        // Datos de la factura
        $factura = array(
            'CantReg'    => 1, // Cantidad de facturas a registrar
            'PtoVta'     => 1, // Punto de venta
            'CbteTipo'   => 6, // Tipo de comprobante (Factura B)
            'Concepto'   => 1, // Concepto de la factura (1: Productos)
            'DocTipo'    => 80, // Tipo de documento del cliente (80: CUIT)
            'DocNro'     => $data['DocNro'], // Número de documento del cliente
            'CbteDesde'  => 1, // Número de comprobante
            'CbteHasta'  => 1, // Número de comprobante
            'CbteFch'    => intval(date('Ymd')), // Fecha de emisión de la factura
            'ImpTotal'   => $data['ImpTotal'], // Importe total de la factura
            'ImpTotConc' => 0, // Importe neto no gravado
            'ImpNeto'    => $data['ImpNeto'], // Importe neto gravado
            'ImpOpEx'    => 0, // Importe exento de IVA
            'ImpIVA'     => $data['ImpIVA'], // Importe total de IVA
            'ImpTrib'    => 0, // Importe total de tributos
            'FchServDesde' => null, // Fecha de inicio del servicio (solo para servicios)
            'FchServHasta' => null, // Fecha de fin del servicio (solo para servicios)
            'FchVtoPago' => null, // Fecha de vencimiento del pago (solo para servicios)
            'MonId'      => 'PES', // Tipo de moneda (PES: Pesos)
            'MonCotiz'   => 1, // Cotización de la moneda
            'Iva'        => array( // Alícuotas de IVA
                array(
                    'Id'       => 5, // Id del tipo de IVA (5: 21%)
                    'BaseImp'  => $data['ImpNeto'], // Base imponible
                    'Importe'  => $data['ImpIVA'] // Importe de IVA
                )
            ),
        );

        // Emitir factura
        $res = $this->afip->ElectronicBilling->CreateVoucher($factura);

        // Retornar la respuesta de AFIP
        return $res;
    }
}

