<?php
include 'conexion.php';  // Asegúrate de ajustar la ruta correctamente

$termino = isset($_GET['termino']) ? $_GET['termino'] : '';

$sql = "SELECT id, fecha, cliente, total FROM ventas WHERE cliente LIKE ? OR documento LIKE ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$param = '%' . $termino . '%';
$stmt->bind_param("ss", $param, $param);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['fecha'] . "</td>";
        echo "<td>" . $row['cliente'] . "</td>";
        echo "<td>" . number_format($row['total'], 2) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No se encontraron ventas.</td></tr>";
}

$stmt->close();
$conn->close();
?>