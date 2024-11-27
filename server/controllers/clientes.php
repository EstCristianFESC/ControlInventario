<?php
include '../db/conexion.php';

// Obtener todos los clientes
function obtenerClientes() {
    global $conn;
    $sql = "SELECT id, tipo_documento, numero_documento, nombre, apellidos, ciudad, direccion, telefono, correo FROM clientes";
    return $conn->query($sql);
}

function agregarCliente($datos) {
    global $conn;

    try {
        $sql = "INSERT INTO clientes (tipo_documento, numero_documento, nombre, apellidos, departamento, ciudad, direccion, telefono, correo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssss",
            $datos['tipo_documento'],
            $datos['numero_documento'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['estado'],
            $datos['ciudad'],
            $datos['direccion'],
            $datos['telefono'],
            $datos['email']
        );

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Cliente guardado correctamente.'];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
?>