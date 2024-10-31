<?php
include 'conexion.php'; // Incluir la conexión a la base de datos

// Consulta para obtener todos los clientes
$sql = "SELECT id, tipo_documento, numero_documento, nombre, apellidos, ciudad, direccion, telefono, correo FROM clientes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $index = 1; // Contador para los números de fila
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$index}</td>
                <td>{$row['numero_documento']}</td>
                <td>{$row['nombre']} {$row['apellidos']}</td>
                <td>{$row['correo']}</td>
                <td>
                    <button class='btn btn-success btn-sm'>
                        <i class='fas fa-sync-alt'></i> <!-- Icono de actualización -->
                    </button>
                </td>
            </tr>";
        $index++;
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No hay clientes registrados</td></tr>";
}

$conn->close(); // Cerrar conexión
?>