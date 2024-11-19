<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $cliente = $_POST['cliente'];
    $total = $_POST['total'];
    $cambio = $_POST['cambio'];
    
    // Insertar la venta en la base de datos
    $sql = "INSERT INTO ventas (fecha, cliente, total, cambio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdd", $fecha, $cliente, $total, $cambio);
    
    if ($stmt->execute()) {
        $venta_id = $stmt->insert_id;

        // Insertar los productos de la venta
        foreach ($_SESSION['carrito'] as $producto_id => $producto) {
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio'];
            $subtotal = $cantidad * $precio;

            $sql_item = "INSERT INTO ventas_productos (venta_id, producto_id, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_item = $conn->prepare($sql_item);
            $stmt_item->bind_param("iiidd", $venta_id, $producto_id, $cantidad, $precio, $subtotal);
            $stmt_item->execute();
            $stmt_item->close();
        }

        $_SESSION['mensaje'] = "Venta guardada correctamente.";
    } else {
        $_SESSION['error'] = "Error al guardar la venta.";
    }
    
    $stmt->close();
    $conn->close();

    // Vaciar el carrito
    unset($_SESSION['carrito']);
}

header("Location: /views/nueva-venta.html");
exit();
?>