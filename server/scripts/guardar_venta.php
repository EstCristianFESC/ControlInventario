<?php
session_start();
include 'conexion.php';  // Ajustar la ruta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_STRING);
    $cliente = filter_input(INPUT_POST, 'cliente', FILTER_SANITIZE_STRING);
    $total = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);
    $cambio = filter_input(INPUT_POST, 'cambio', FILTER_VALIDATE_FLOAT);

    if (!$fecha || !$cliente || $total === false || $cambio === false) {
        $_SESSION['error'] = "Datos inválidos. Por favor, revisa el formulario.";
        header("Location: ../views/nueva-venta.html");
        exit();
    }

    $sql = "INSERT INTO ventas (fecha, cliente, total, cambio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdd", $fecha, $cliente, $total, $cambio);

    if ($stmt->execute()) {
        $venta_id = $stmt->insert_id;

        if (!empty($_SESSION['carrito'])) {
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

            unset($_SESSION['carrito']);
        }

        $_SESSION['mensaje'] = "Venta guardada correctamente.";
    } else {
        $_SESSION['error'] = "Error al guardar la venta.";
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Método no permitido.";
}

header("Location: ../views/nueva-venta.html");
exit();
?>