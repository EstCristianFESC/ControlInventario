<?php
include 'conexion.php'; // Incluir la conexión a la base de datos

// Obtener el término de búsqueda si existe
$termino = isset($_GET['termino']) ? $_GET['termino'] : '';

// Construir la consulta SQL
$sql = "SELECT id, tipo_documento, numero_documento, nombre, apellidos, ciudad, direccion, telefono, correo FROM clientes";
if (!empty($termino)) {
    $sql .= " WHERE numero_documento LIKE ? OR nombre LIKE ? OR apellidos LIKE ?";
}

$stmt = $conn->prepare($sql);

if (!empty($termino)) {
    $terminoBusqueda = '%' . $termino . '%';
    $stmt->bind_param('sss', $terminoBusqueda, $terminoBusqueda, $terminoBusqueda);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $index = 1; // Contador para los números de fila
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$index}</td>
                <td>{$row['numero_documento']}</td>
                <td>{$row['nombre']} {$row['apellidos']}</td>
                <td>{$row['correo']}</td>
                <td>
                    <button class='btn btn-success btn-sm update-btn'
                        data-id='{$row['id']}'
                        data-tipo_documento='{$row['tipo_documento']}'
                        data-numero_documento='{$row['numero_documento']}'
                        data-nombres='{$row['nombre']}'
                        data-apellidos='{$row['apellidos']}'
                        data-departamento='{$row['ciudad']}'
                        data-ciudad='{$row['ciudad']}'
                        data-direccion='{$row['direccion']}'
                        data-telefono='{$row['telefono']}'
                        data-correo='{$row['correo']}'
                        data-bs-toggle='modal' data-bs-target='#updateClientModal'>
                        <i class='fas fa-sync-alt'></i> <!-- Icono de actualización -->
                    </button>
                </td>
            </tr>";
        $index++;
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No hay clientes registrados</td></tr>";
}

$stmt->close();
$conn->close(); // Cerrar conexión
?>