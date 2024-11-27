<?php
include 'conexion.php';  // Ajustar la ruta

$documento = $_GET['documento'];

if ($documento) {
    $sql = "SELECT nombre FROM clientes WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();

    if ($cliente) {
        echo json_encode($cliente);
    } else {
        echo json_encode(['nombre' => null]);
    }

    $stmt->close();
} else {
    echo json_encode(['nombre' => null]);
}

$conn->close();
?>