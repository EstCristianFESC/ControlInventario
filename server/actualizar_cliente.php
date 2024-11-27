<?php
include 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $departamento = $_POST['departamento'];
    $ciudad = $_POST['ciudad'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    // Preparar la consulta de actualización
    $sql = "UPDATE clientes SET tipo_documento=?, numero_documento=?, nombre=?, apellidos=?, departamento=?, ciudad=?, direccion=?, telefono=?, correo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssssi', $tipo_documento, $numero_documento, $nombre, $apellidos, $departamento, $ciudad, $direccion, $telefono, $correo, $id);

    // Ejecutar la consulta de actualización
    if ($stmt->execute()) {
        $success = "Cliente actualizado exitosamente.";
        header("Location: ../views/lista-clientes.html?success=" . urlencode($success));
        exit();
    } else {
        $error = "Error al actualizar el cliente.";
        header("Location: ../views/lista-clientes.html?error=" . urlencode($error));
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    $error = "Solicitud inválida.";
    header("Location: ../views/lista-clientes.html?error=" . urlencode($error));
    exit();
}
?>