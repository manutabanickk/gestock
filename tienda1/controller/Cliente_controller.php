<?php

class Cliente {

    public function Listar_Clientes() {
        // Instanciar ClienteModel
        $clienteModel = new ClienteModel();
        $filas = $clienteModel->Listar_Clientes();  // Llamada no estática
        return $filas;
    }

    public function Ver_Limite_Credito($idcliente) {
        // Instanciar ClienteModel
        $clienteModel = new ClienteModel();
        $filas = $clienteModel->Ver_Limite_Credito($idcliente);  // Llamada no estática
        return $filas;
    }

    public function Listar_Clientes_Activos() {
        // Instanciar ClienteModel
        $clienteModel = new ClienteModel();
        $filas = $clienteModel->Listar_Clientes_Activos();  // Llamada no estática
        return $filas;
    }

    public function Listar_Clientes_Inactivos() {
        // Instanciar ClienteModel
        $clienteModel = new ClienteModel();
        $filas = $clienteModel->Listar_Clientes_Inactivos();  // Llamada no estática
        return $filas;
    }

    public function Insertar_Cliente($nombre_cliente, $numero_nit, $numero_nrc, $direccion, $numero_telefono, $email, $giro, $limite_credito) {
        // Instanciar ClienteModel
        $clienteModel = new ClienteModel();
        $cmd = $clienteModel->Insertar_Cliente($nombre_cliente, $numero_nit, $numero_nrc, $direccion, $numero_telefono, $email, $giro, $limite_credito);  // Llamada no estática
    }

    public function Editar_Cliente($idcliente, $nombre_cliente, $numero_nit, $numero_nrc, $direccion, $numero_telefono, $email, $giro, $limite_credito, $estado) {
        // Instanciar ClienteModel
        $clienteModel = new ClienteModel();
        $cmd = $clienteModel->Editar_Cliente($idcliente, $nombre_cliente, $numero_nit, $numero_nrc, $direccion, $numero_telefono, $email, $giro, $limite_credito, $estado);  // Llamada no estática
    }

}

?>
