<?php
include 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Configurar encabezados para devolver JSON
header('Content-Type: application/json');

// Obtener los datos enviados en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

// Verificar que todos los campos requeridos estén presentes
if (isset($data['tipo_documento'], $data['numero_documento'], $data['nombre'], $data['apellidos'], $data['departamento'], $data['ciudad'], $data['direccion'], $data['telefono'], $data['correo'])) {
    $tipo_documento = $data['tipo_documento'];
    $numero_documento = $data['numero_documento'];
    $nombre = $data['nombre'];
    $apellidos = $data['apellidos'];
    $departamento = $data['departamento'];
    $ciudad = $data['ciudad'];
    $direccion = $data['direccion'];
    $telefono = $data['telefono'];
    $correo = $data['correo'];

    // Preparar y ejecutar la consulta para insertar el nuevo cliente
    $query = "INSERT INTO clientes (tipo_documento, numero_documento, nombre, apellidos, departamento, ciudad, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $error = "Error en la preparación de la consulta: " . $conn->error;
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit();
    }
    $stmt->bind_param("sssssssss", $tipo_documento, $numero_documento, $nombre, $apellidos, $departamento, $ciudad, $direccion, $telefono, $correo);

    if ($stmt->execute()) {
        $success = "Cliente agregado exitosamente.";
        echo json_encode(['status' => 'success', 'message' => $success, 'redirect' => 'lista-clientes.html?success=' . urlencode($success)]);
    } else {
        $error = "Error al agregar cliente: " . $stmt->error;
        echo json_encode(['status' => 'error', 'message' => $error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
}
?>