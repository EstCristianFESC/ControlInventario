<?php
session_start();
include 'conexion.php';  // Incluir el archivo de conexión

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];
    $repetirClave = $_POST["repetirClave"];
    $role = $_POST["role"];

    // Validar campos
    if (empty($usuario) || empty($clave) || empty($repetirClave) || empty($role)) {
        $error = "Todos los campos son obligatorios.";
        header("Location: ../views/nuevo-usuario.html?error=" . urlencode($error));
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($clave !== $repetirClave) {
        $error = "Las contraseñas no coinciden.";
        header("Location: ../views/nuevo-usuario.html?error=" . urlencode($error));
        exit();
    }

    // Preparar y ejecutar la consulta para verificar si el usuario ya existe
    $checkQuery = "SELECT * FROM usuarios WHERE user = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $usuario);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Usuario ya existe
        $error = "El usuario ya existe.";
        header("Location: ../views/nuevo-usuario.html?error=" . urlencode($error));
        exit();
    }

    // Encriptar la contraseña
    $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);

    // Preparar y ejecutar la consulta para insertar el nuevo usuario
    $query = "INSERT INTO usuarios (user, password, rol) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $usuario, $hashedPassword, $role);

    if ($stmt->execute()) {
        $success = "Usuario registrado exitosamente.";
        header("Location: ../views/lista-usuarios.html?success=" . urlencode($success));
        exit();
    } else {
        $error = "Error al registrar el usuario.";
        header("Location: ../views/nuevo-usuario.html?error=" . urlencode($error));
        exit();
    }

    $stmt->close();
    $checkStmt->close(); // Cerrar la consulta de verificación
    $conn->close();
} else {
    $error = "Solicitud inválida.";
    header("Location: ../views/nuevo-usuario.html?error=" . urlencode($error));
    exit();
}
?>