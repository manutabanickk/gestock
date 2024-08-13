<?php

class Producto {

    public function Print_Barcode($idproducto){
        $productoModel = new ProductoModel();
        return $productoModel->Print_Barcode($idproducto);
    }

    public function Listar_Productos(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Productos();
    }

    public function Autocomplete_Producto($search){
        $productoModel = new ProductoModel();
        return $productoModel->Autocomplete_Producto($search);
    }

    public function Listar_Productos_Activos(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Productos_Activos();
    }

    public function Listar_Productos_Inactivos(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Productos_Inactivos();
    }

    public function Listar_Productos_Agotados(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Productos_Agotados();
    }

    public function Listar_Productos_Vigentes(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Productos_Vigentes();
    }

    public function Listar_Perecederos(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Perecederos();
    }

    public function Listar_No_Perecederos(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_No_Perecederos();
    }

    public function Listar_Categorias(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Categorias();
    }

    public function Listar_Marcas(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Marcas();
    }

    public function Listar_Presentaciones(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Presentaciones();
    }

    public function Listar_Proveedores(){
        $productoModel = new ProductoModel();
        return $productoModel->Listar_Proveedores();
    }

    public function Insertar_Producto($codigo_barra, $nombre_producto, $precio_compra, $precio_venta, $precio_venta_mayoreo,
    $precio_venta_3, $stock, $stock_min, $idcategoria, $idmarca, $idpresentacion, $exento, $inventariable, $perecedero){
        $productoModel = new ProductoModel();
        $productoModel->Insertar_Producto($codigo_barra, $nombre_producto, $precio_compra, $precio_venta, $precio_venta_mayoreo,
        $precio_venta_3, $stock, $stock_min, $idcategoria, $idmarca, $idpresentacion, $exento, $inventariable, $perecedero);
    }

    public function Editar_Producto($idproducto, $codigo_barra, $nombre_producto, $precio_compra, $precio_venta, $precio_venta_mayoreo,
    $precio_venta_3, $stock_min, $idcategoria, $idmarca, $idpresentacion, $estado, $exento, $inventariable, $perecedero){
        $productoModel = new ProductoModel();
        $productoModel->Editar_Producto($idproducto, $codigo_barra, $nombre_producto, $precio_compra, $precio_venta, $precio_venta_mayoreo,
        $precio_venta_3, $stock_min, $idcategoria, $idmarca, $idpresentacion, $estado, $exento, $inventariable, $perecedero);
    }
}

?>
