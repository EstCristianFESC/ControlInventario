<?php
// Incluir la conexión a la base de datos
include 'conexion.php'; // Asegúrate de que la ruta sea correcta

// Inicializa el array de respuesta
$response = ['status' => '', 'message' => ''];

// Recibir datos del formulario
$tipo_documento = $_POST['tipoDocumento'];
$numero_documento = $_POST['numeroDocumento'];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$estado = $_POST['estado'];
$ciudad = $_POST['ciudad'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];

// Verificar si el número de documento ya existe
$checkSql = "SELECT COUNT(*) FROM clientes WHERE numero_documento = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $numero_documento);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    // El número de documento ya existe
    $response['status'] = 'error';
    $response['message'] = 'El número de documento ya existe.';
} else {
    // Preparar la sentencia SQL
    $sql = "INSERT INTO clientes (tipo_documento, numero_documento, nombre, apellidos, departamento, ciudad, direccion, telefono, correo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) { // Verificar si la preparación fue exitosa
        $stmt->bind_param("sssssssss", $tipo_documento, $numero_documento, $nombres, $apellidos, $estado, $ciudad, $direccion, $telefono, $email);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Si se guarda correctamente
            $response['status'] = 'success';
            $response['message'] = 'Cliente guardado correctamente.';
        } else {
            // Si hubo un error
            $response['status'] = 'error';
            $response['message'] = 'Error al guardar el cliente.';
        }
        
        // Cerrar la declaración
        $stmt->close();
    } else {
        // Si la preparación de la declaración falló
        $response['status'] = 'error';
        $response['message'] = 'Error en la preparación de la declaración SQL.';
    }
}

// Cerrar la conexión
$conn->close();

// Retornar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>