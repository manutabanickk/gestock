<?php

require_once __DIR__ . '/../model/Taller_model.php'; // Asegúrate de incluir el modelo correctamente

class Taller {

    private $model;

    public function __construct() {
        $this->model = new TallerModel(); // Instanciar la clase TallerModel
    }

    public function Ver_Moneda_Reporte() {
        return $this->model->Ver_Moneda_Reporte(); // Llamada al método del modelo
    }

    public function Ver_Max_Orden() {
        return $this->model->Ver_Max_Orden(); // Llamada al método del modelo
    }

    public function Listar_Ordenes($date, $date2) {
        return $this->model->Listar_Ordenes($date, $date2); // Llamada al método del modelo
    }

    public function Reporte_Taller($id) {
        return $this->model->Reporte_Taller($id); // Llamada al método del modelo
    }

    public function Listar_Tecnicos() {
        return $this->model->Listar_Tecnicos(); // Llamada al método del modelo
    }

    public function Count_Ordenes($date, $date2) {
        return $this->model->Count_Ordenes($date, $date2); // Llamada al método del modelo
    }

    public function Insertar_Orden($idcliente, $aparato, $modelo, $idmarca, $serie, $idtecnico, $averia, $observaciones, $deposito_revision, $deposito_reparacion, $parcial_pagar) {
        return $this->model->Insertar_Orden($idcliente, $aparato, $modelo, $idmarca, $serie, $idtecnico, $averia, $observaciones, $deposito_revision, $deposito_reparacion, $parcial_pagar); // Llamada al método del modelo
    }

    public function Insertar_Diagnostico($idorden, $diagnostico, $estado_aparato, $repuestos, $mano_obra, $fecha_alta, $fecha_retiro, $ubicacion, $parcial_pagar) {
        return $this->model->Insertar_Diagnostico($idorden, $diagnostico, $estado_aparato, $repuestos, $mano_obra, $fecha_alta, $fecha_retiro, $ubicacion, $parcial_pagar); // Llamada al método del modelo
    }

    public function Editar_Orden($idorden, $numero_orden, $fecha_ingreso, $idcliente, $aparato, $modelo, $idmarca, $serie, $idtecnico, $averia, $observaciones, $deposito_revision, $deposito_reparacion) {
        return $this->model->Editar_Orden($idorden, $numero_orden, $fecha_ingreso, $idcliente, $aparato, $modelo, $idmarca, $serie, $idtecnico, $averia, $observaciones, $deposito_revision, $deposito_reparacion); // Llamada al método del modelo
    }

    public function Borrar_Orden($idtaller) {
        return $this->model->Borrar_Orden($idtaller); // Llamada al método del modelo
    }
}

?>
