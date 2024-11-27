<?php
include '../controllers/clientes.php';

// Asegúrate de que no hay salida antes de establecer las cabeceras
header('Content-Type: application/json');

try {
    $datos = [
        'tipo_documento' => $_POST['tipoDocumento'],
        'numero_documento' => $_POST['numeroDocumento'],
        'nombres' => $_POST['nombres'],
        'apellidos' => $_POST['apellidos'],
        'estado' => $_POST['estado'],
        'ciudad' => $_POST['ciudad'],
        'direccion' => $_POST['direccion'],
        'telefono' => $_POST['telefono'],
        'email' => $_POST['email']
    ];

    $response = agregarCliente($datos);
    echo json_encode($response);
} catch (Exception $e) {
    // Asegúrate de que cualquier excepción también devuelve JSON
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>