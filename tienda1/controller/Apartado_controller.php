<?php
require_once __DIR__ . '/../model/Apartado_model.php';
class Apartado {

    private $model;

    public function __construct() {
        $this->model = new ApartadoModel(); // Crear una instancia del modelo
    }

    public function Ver_Moneda_Reporte() {
        return $this->model->Ver_Moneda_Reporte();
    }

    public function Listar_Apartados($criterio, $date, $date2, $estado) {
        return $this->model->Listar_Apartados($criterio, $date, $date2, $estado);
    }

    public function Listar_Apartados_Totales($criterio, $date, $date2, $estado) {
        return $this->model->Listar_Apartados_Totales($criterio, $date, $date2, $estado);
    }

    public function Listar_Apartados_Detalle($criterio, $date, $date2, $estado) {
        return $this->model->Listar_Apartados_Detalle($criterio, $date, $date2, $estado);
    }

    public function Imprimir_Ticket_DetalleApartado($idApartado) {
        return $this->model->Imprimir_Ticket_DetalleApartado($idApartado);
    }

    public function Imprimir_Ticket_Apartado($idApartado) {
        return $this->model->Imprimir_Ticket_Apartado($idApartado);
    }

    public function Listar_Detalle($idApartado) {
        return $this->model->Listar_Detalle($idApartado);
    }

    public function Listar_Info($idApartado) {
        return $this->model->Listar_Info($idApartado);
    }

    public function Count_Apartados($criterio, $date, $date2) {
        return $this->model->Count_Apartados($criterio, $date, $date2);
    }

    public function Listar_Clientes() {
        return $this->model->Listar_Clientes();
    }

    public function Listar_Comprobantes() {
        return $this->model->Listar_Comprobantes();
    }

    public function Autocomplete_Producto($search) {
        return $this->model->Autocomplete_Producto($search);
    }

    public function Insertar_Apartado($fecha_limite_retiro, $sumas, $iva, $exento, $retenido, $descuento, $total, $abonado_apartado, $restante_pagar, $sonletras, $idcliente, $idusuario) {
        return $this->model->Insertar_Apartado($fecha_limite_retiro, $sumas, $iva, $exento, $retenido, $descuento, $total, $abonado_apartado, $restante_pagar, $sonletras, $idcliente, $idusuario);
    }

    public function Insertar_Venta($idapartado, $tipo_pago, $tipo_comprobante, $pago_efectivo, $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, $idcliente, $idusuario) {
        return $this->model->Insertar_Venta($idapartado, $tipo_pago, $tipo_comprobante, $pago_efectivo, $pago_tarjeta, $numero_tarjeta, $tarjeta_habiente, $cambio, $idcliente, $idusuario);
    }

    public function Insertar_DetalleApartado($idproducto, $cantidad, $precio_unitario, $exento, $descuento, $fecha_vence, $importe) {
        return $this->model->Insertar_DetalleApartado($idproducto, $cantidad, $precio_unitario, $exento, $descuento, $fecha_vence, $importe);
    }

    public function Anular_Apartado($idApartado) {
        return $this->model->Anular_Apartado($idApartado);
    }
}

?>
