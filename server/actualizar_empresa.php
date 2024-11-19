<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = trim($_POST['nombreEmpresa']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($telefono) || empty($email) || empty($direccion)) {
        header("Location: /views/configuracion.html?status=error&message=complete_fields");
        exit();
    } else {
        // Verificar si ya existen datos en la tabla
        $sql_check = "SELECT COUNT(*) as total FROM datos_empresa WHERE id = 1";
        $result = $conn->query($sql_check);
        $row = $result->fetch_assoc();

        if ($row['total'] == 0) {
            // Insertar datos si no existen
            $sql_insert = "INSERT INTO datos_empresa (id, nombre, telefono, email, direccion) VALUES (1, ?, ?, ?, ?)";
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("ssss", $nombre, $telefono, $email, $direccion);
                if ($stmt_insert->execute()) {
                    header("Location: /views/configuracion.html?status=success&message=inserted");
                } else {
                    header("Location: /views/configuracion.html?status=error&message=insert_failed");
                }
                $stmt_insert->close();
            } else {
                header("Location: /views/configuracion.html?status=error&message=prepare_failed");
            }
        } else {
            // Actualizar los datos si ya existen
            $sql_update = "UPDATE datos_empresa 
                            SET nombre = ?, telefono = ?, email = ?, direccion = ? 
                            WHERE id = 1";

            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("ssss", $nombre, $telefono, $email, $direccion);
                if ($stmt_update->execute()) {
                    header("Location: /views/configuracion.html?status=success&message=updated");
                } else {
                    header("Location: /views/configuracion.html?status=error&message=update_failed");
                }
                $stmt_update->close();
            } else {
                header("Location: /views/configuracion.html?status=error&message=prepare_failed");
            }
        }

        // Redirigir a la misma página
        exit();
    }
}
$conn->close();
?>