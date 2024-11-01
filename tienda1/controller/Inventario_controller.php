<?php 

class Inventario {

    public function Listar_Kardex($mes) {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $filas = $inventarioModel->Listar_Kardex($mes);  // Llamada no estática
        return $filas;
    }

    public function Listar_Entradas($mes) {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $filas = $inventarioModel->Listar_Entradas($mes);  // Llamada no estática
        return $filas;
    }

    public function Listar_Salidas($mes) {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $filas = $inventarioModel->Listar_Salidas($mes);  // Llamada no estática
        return $filas;
    }

    public function Insertar_Entrada($descripcion, $cantidad, $producto) {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $cmd = $inventarioModel->Insertar_Entrada($descripcion, $cantidad, $producto);  // Llamada no estática
    }

    public function Insertar_Salida($descripcion, $cantidad, $producto) {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $cmd = $inventarioModel->Insertar_Salida($descripcion, $cantidad, $producto);  // Llamada no estática
    }

    public function Abrir_Inventario() {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $cmd = $inventarioModel->Abrir_Inventario();  // Llamada no estática
    }

    public function Cerrar_Inventario() {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $cmd = $inventarioModel->Cerrar_Inventario();  // Llamada no estática
    }

    public function Validar_Inventario() {
        // Instanciar InventarioModel
        $inventarioModel = new InventarioModel();
        $cmd = $inventarioModel->Validar_Inventario();  // Llamada no estática
    }
}

?>
