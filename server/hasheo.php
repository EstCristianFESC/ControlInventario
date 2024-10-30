<?php
include 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté configurada correctamente

// ID del usuario que deseas actualizar
$userId = 1;

// Contraseña sin hashear
$plainPassword = '31306';

// Hashear la contraseña
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Actualizar la contraseña en la base de datos
$updateQuery = "UPDATE usuarios SET password = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("si", $hashedPassword, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Contraseña actualizada correctamente.";
} else {
    echo "No se pudo actualizar la contraseña.";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
