<?php

require_once __DIR__ . '/../model/Tecnico_model.php'; // Asegúrate de incluir el modelo correctamente

class Tecnico {

    private $model;

    public function __construct() {
        $this->model = new TecnicoModel(); // Instanciar la clase TecnicoModel
    }

    public function Listar_Tecnicos() {
        return $this->model->Listar_Tecnicos(); // Llamada al método del modelo
    }

    public function Insertar_Tecnico($nombre, $apellido, $especialidad) {
        return $this->model->Insertar_Tecnico($nombre, $apellido, $especialidad); // Llamada al método del modelo
    }

    public function Editar_Tecnico($idtecnico, $nombre, $apellido, $especialidad) {
        return $this->model->Editar_Tecnico($idtecnico, $nombre, $apellido, $especialidad); // Llamada al método del modelo
    }
}

?>
