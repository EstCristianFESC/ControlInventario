<?php
session_start();
include 'conexion.php';  // Incluir el archivo de conexión

// Obtener datos del formulario
$user = $_POST['username']; // Asegúrate de que coincida con el nombre del campo en el formulario
$password = $_POST['password'];

// Preparar y ejecutar la consulta para verificar el usuario
$query = "SELECT * FROM usuarios WHERE user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Credenciales correctas
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = $user;
        header("Location: ../views/home.html");
        exit;
    } else {
        // Contraseña incorrecta
        header("Location: ../views/index.html?error=1"); // Redirigir con parámetro de error
        exit;
    }
} else {
    // Usuario no encontrado
    header("Location: ../views/index.html?error=1"); // Redirigir con parámetro de error
    exit;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>