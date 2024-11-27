<?php
include 'conexion.php'; // Asegúrate de tener tu archivo de conexión a la base de datos

// Obtener el término de búsqueda desde la solicitud
$termino_busqueda = isset($_GET['termino_busqueda']) ? $_GET['termino_busqueda'] : '';

// Construir la consulta SQL
$sql = "SELECT sku_producto, nombre_producto, precio_producto, stock_producto, marca_producto FROM productos";
if (!empty($termino_busqueda)) {
    $sql .= " WHERE sku_producto LIKE ? OR nombre_producto LIKE ?";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);

if (!empty($termino_busqueda)) {
    $termino_busqueda_param = '%' . $termino_busqueda . '%';
    $stmt->bind_param("ss", $termino_busqueda_param, $termino_busqueda_param);
}

$stmt->execute();
$result = $stmt->get_result();

// Generar el HTML para la tabla de productos
$productos = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos .= '
            <tr>
                <td>' . htmlspecialchars($row['sku_producto']) . '</td>
                <td>' . htmlspecialchars($row['nombre_producto']) . '</td>
                <td>' . htmlspecialchars($row['precio_producto']) . '</td>
                <td>' . htmlspecialchars($row['stock_producto']) . '</td>
                <td>' . htmlspecialchars($row['marca_producto']) . '</td>
            </tr>
        ';
    }
} else {
    $productos = '<tr><td colspan="5" class="text-center">No se encontraron productos.</td></tr>';
}

// Imprimir el HTML
echo $productos;

$stmt->close();
$conn->close();
?>