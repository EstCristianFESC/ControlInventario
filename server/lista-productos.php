<?php
include 'conexion.php';

// Obtener el término de búsqueda desde la solicitud
$termino_busqueda = isset($_GET['termino_busqueda']) ? $_GET['termino_busqueda'] : '';

// Construir la consulta SQL
$sql = "SELECT id, sku_producto, nombre_producto, precio_producto, stock_producto, marca_producto FROM productos";
if (!empty($termino_busqueda)) {
    $sql .= " WHERE sku_producto LIKE ? OR nombre_producto LIKE ?";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);

// Si hay un término de búsqueda, vincular los parámetros
if (!empty($termino_busqueda)) {
    $termino_busqueda_param = '%' . $termino_busqueda . '%';
    $stmt->bind_param("ss", $termino_busqueda_param, $termino_busqueda_param);
}

$stmt->execute();
$result = $stmt->get_result();

// Generar el HTML para la tabla de productos
if ($result->num_rows > 0) {
    $productos = '';

    while ($row = $result->fetch_assoc()) {
        $productos .= '
            <tr>
                <td>' . $row['id'] . '</td>
                <td>' . $row['nombre_producto'] . '</td>
                <td>' . $row['precio_producto'] . '</td>
                <td>' . $row['stock_producto'] . '</td>
                <td>' . $row['marca_producto'] . '</td>
            </tr>
        ';
    }
} else {
    $productos = '<tr><td colspan="5" class="text-center">No hay productos disponibles.</td></tr>';
}

echo $productos;

$stmt->close();
$conn->close();
?>