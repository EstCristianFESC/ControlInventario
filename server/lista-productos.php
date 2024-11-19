<?php
header('Content-Type: text/html; charset=UTF-8'); // Asegura la codificación correcta
include 'conexion.php'; // Incluir la conexión a la base de datos

// Consulta para obtener los productos
$sql = "SELECT id, nombre_producto, precio_producto, stock_producto, marca_producto FROM productos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $index = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$index}</td>
                <td>{$row['nombre_producto']}</td>
                <td>{$row['precio_producto']}</td>
                <td>{$row['stock_producto']}</td>
                <td>{$row['marca_producto']}</td>
            </tr>";
        $index++;
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No hay productos disponibles</td></tr>";
}

$conn->close();
?>