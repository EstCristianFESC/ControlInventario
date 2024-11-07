<?php
header('Content-Type: application/json');
include 'conexion.php'; // Asegúrate de que la ruta es correcta

// Consultar la lista de productos
$sql = "SELECT id, nombre_producto, precio_producto, stock_producto, categoria_producto FROM productos";
$result = $conn->query($sql);

$productos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Devolver los datos en formato JSON
echo json_encode($productos);

$conn->close();
?>