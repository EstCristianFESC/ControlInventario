<?php
session_start();
include 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];
    $clave = $_POST['clave'];
    $repetirClave = $_POST['repetirClave'];

    // Validar campos
    if (empty($usuario) || empty($rol) || (empty($clave) && !empty($repetirClave)) || (!empty($clave) && empty($repetirClave))) {
        $error = "Todos los campos son obligatorios.";
        header("Location: ../views/lista-usuarios.html?error=" . urlencode($error));
        exit();
    }

    // Verificar que las contraseñas coincidan si se proporcionaron
    if (!empty($clave) && $clave !== $repetirClave) {
        $error = "Las contraseñas no coinciden.";
        header("Location: ../views/lista-usuarios.html?error=" . urlencode($error));
        exit();
    }

    // Preparar la consulta de actualización
    if (!empty($clave)) {
        // Encriptar la contraseña
        $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET user=?, rol=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $usuario, $rol, $hashedPassword, $id);
    } else {
        $sql = "UPDATE usuarios SET user=?, rol=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $usuario, $rol, $id);
    }

    // Ejecutar la consulta de actualización
    if ($stmt->execute()) {
        $success = "Usuario actualizado exitosamente.";
        header("Location: ../views/lista-usuarios.html?success=" . urlencode($success));
        exit();
    } else {
        $error = "Error al actualizar el usuario.";
        header("Location: ../views/lista-usuarios.html?error=" . urlencode($error));
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    $error = "Solicitud inválida.";
    header("Location: ../views/lista-usuarios.html?error=" . urlencode($error));
    exit();
}
?>