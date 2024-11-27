<?php
include '../db/conexion.php';

// Guardar venta y productos
function guardarVenta($datos, $carrito) {
    global $conn;

    $sql = "INSERT INTO ventas (fecha, cliente, total, cambio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssdd",
        $datos['fecha'],
        $datos['cliente'],
        $datos['total'],
        $datos['cambio']
    );

    if ($stmt->execute()) {
        $venta_id = $stmt->insert_id;

        // Agregar productos a la tabla ventas_productos
        foreach ($carrito as $producto_id => $producto) {
            $sql_item = "INSERT INTO ventas_productos (venta_id, producto_id, cantidad, precio, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
            $stmt_item = $conn->prepare($sql_item);
            $stmt_item->bind_param(
                "iiidd",
                $venta_id,
                $producto_id,
                $producto['cantidad'],
                $producto['precio'],
                $producto['cantidad'] * $producto['precio']
            );
            $stmt_item->execute();
        }

        return ['status' => 'success', 'message' => 'Venta guardada correctamente.'];
    } else {
        return ['status' => 'error', 'message' => $stmt->error];
    }
}
?>