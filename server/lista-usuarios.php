<?php
session_start();
include 'conexion.php';  // Incluir el archivo de conexión

// Preparar y ejecutar la consulta para obtener todos los usuarios
$query = "SELECT id, user FROM usuarios";  // Suponiendo que 'id' es la clave primaria
$result = $conn->query($query);

// Comienza a generar el cuerpo de la tabla
if ($result->num_rows > 0) {
    // Contador para el número de fila
    $count = 1;

    // Iterar a través de los resultados y crear una fila por cada usuario
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . htmlspecialchars($row['user']) . "</td>";
        echo "<td><a href='actualizar-usuario.php?id=" . $row['id'] . "' class='btn btn-warning'>Actualizar</a></td>";
        echo "</tr>";
        $count++;
    }
} else {
    // Si no hay usuarios, mostrar mensaje
    echo "<tr>";
    echo "<td colspan='4' class='text-center'>No hay registros en el sistema</td>";
    echo "</tr>";
}

// Cerrar la conexión
$conn->close();
?>