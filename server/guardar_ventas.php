<?php
session_start();
include 'conexion.php';  // Asegúrate de ajustar la ruta correctamente

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errores.log'); // Registra errores en un archivo

// Encabezados para manejar CORB y CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si ya se está procesando una solicitud
        $lockfile = __DIR__ . '/guardar_ventas.lock';
        if (file_exists($lockfile)) {
            throw new Exception("El proceso de guardado ya está en curso. Intenta nuevamente en unos momentos.");
        }

        // Crear un archivo de bloqueo
        touch($lockfile);

        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            unlink($lockfile); // Eliminar el archivo de bloqueo
            throw new Exception("Error al parsear JSON: " . json_last_error_msg());
        }

        // Obtener los datos de la factura
        $fecha = date('Y-m-d H:i:s'); // Fecha y hora actual
        $documento = filter_var($data['documento'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cliente = filter_var($data['cliente'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $totalPagado = filter_var($data['totalPagado'], FILTER_VALIDATE_FLOAT);
        $total = filter_var($data['total'], FILTER_VALIDATE_FLOAT);
        $cambio = filter_var($data['cambio'], FILTER_VALIDATE_FLOAT);

        if (!$cliente || $total === false || $cambio === false || $totalPagado === false) {
            unlink($lockfile); // Eliminar el archivo de bloqueo
            throw new Exception("Datos inválidos. Por favor, revisa el formulario.");
        }

        // Insertar la venta en la base de datos
        $sql = "INSERT INTO ventas (fecha, documento, cliente, total, cambio, totalPagado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            unlink($lockfile); // Eliminar el archivo de bloqueo
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("ssssdd", $fecha, $documento, $cliente, $total, $cambio, $totalPagado);
        if (!$stmt->execute()) {
            unlink($lockfile); // Eliminar el archivo de bloqueo
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        $venta_id = $stmt->insert_id; // Obtener el ID de la venta insertada
        $stmt->close();

        // Procesar cada producto en la venta y actualizar el stock
        foreach ($data['productos'] as $producto) {
            $sku_producto = $producto['producto_id'];
            $cantidad = $producto['cantidad'];

            // Verificar si el producto existe
            $sql_verificar_producto = "SELECT sku_producto FROM productos WHERE sku_producto = ?";
            $stmt_verificar_producto = $conn->prepare($sql_verificar_producto);
            $stmt_verificar_producto->bind_param("s", $sku_producto);
            $stmt_verificar_producto->execute();
            $resultado = $stmt_verificar_producto->get_result();
            if ($resultado->num_rows === 0) {
                unlink($lockfile); // Eliminar el archivo de bloqueo
                throw new Exception("Producto con SKU $sku_producto no encontrado.");
            }
            $stmt_verificar_producto->close();

            // Actualizar el stock del producto en la tabla productos
            $sql_stock = "UPDATE productos SET stock_producto = stock_producto - ? WHERE sku_producto = ?";
            $stmt_stock = $conn->prepare($sql_stock);
            if (!$stmt_stock) {
                unlink($lockfile); // Eliminar el archivo de bloqueo
                throw new Exception("Error en la preparación de la consulta de stock: " . $conn->error);
            }
            $stmt_stock->bind_param("is", $cantidad, $sku_producto);
            if (!$stmt_stock->execute()) {
                unlink($lockfile); // Eliminar el archivo de bloqueo
                throw new Exception("Error al ejecutar la consulta de stock: " . $stmt_stock->error);
            }
            $stmt_stock->close();
        }

        // Responder con éxito e incluir el ID de la venta
        echo json_encode(['success' => true, 'message' => "Venta guardada y stock actualizado correctamente.", 'venta_id' => $venta_id]);
        unlink($lockfile); // Eliminar el archivo de bloqueo
        $conn->close();
    } else {
        throw new Exception("Método no permitido.");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    if (isset($lockfile) && file_exists($lockfile)) {
        unlink($lockfile); // Eliminar el archivo de bloqueo en caso de excepción
    }
}
?>