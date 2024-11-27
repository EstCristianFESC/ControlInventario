<?php
include 'conexion.php';

// Obtener el SKU del producto desde la solicitud
$sku_producto = isset($_GET['sku_producto']) ? $_GET['sku_producto'] : '';

if ($sku_producto) {
    $sql = "SELECT id, sku_producto, nombre_producto, precio_producto, stock_producto, marca_producto FROM productos WHERE sku_producto = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['error' => 'Error al preparar la declaración: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $sku_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $productos = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    echo json_encode($productos);
    $stmt->close();
} else {
    echo json_encode(['error' => 'SKU del producto no proporcionado']);
}

$conn->close();
?>