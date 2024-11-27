<?php
include '../db/conexion.php';

// Obtener todos los productos
function obtenerProductos() {
    global $conn;
    $sql = "SELECT id, nombre_producto, precio_producto, stock_producto, marca_producto FROM productos";
    return $conn->query($sql);
}

// Guardar un producto
function agregarProducto($datos) {
    global $conn;

    $sql = "INSERT INTO productos (nombre_producto, categoria_producto, marca_producto, genero_producto, talla_producto, precio_producto, stock_producto, sku_producto, proveedor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssiddis",
        $datos['nombreProducto'],
        $datos['categoriaProducto'],
        $datos['marcaProducto'],
        $datos['generoProducto'],
        $datos['tallaProducto'],
        $datos['precioProducto'],
        $datos['stockProducto'],
        $datos['skuProducto'],
        $datos['proveedor']
    );

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'Producto guardado correctamente.'];
    } else {
        return ['status' => 'error', 'message' => $stmt->error];
    }
}
?>