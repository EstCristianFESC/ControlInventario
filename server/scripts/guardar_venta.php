<?php
session_start();
include 'conexion.php'; // Asegúrate de ajustar la ruta correctamente

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
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al parsear JSON: " . json_last_error_msg());
        }

        $fecha = filter_var($data['fecha'], FILTER_SANITIZE_STRING);
        $documento = filter_var($data['documento'], FILTER_SANITIZE_STRING);
        $cliente = filter_var($data['cliente'], FILTER_SANITIZE_STRING);
        $totalPagado = filter_var($data['totalPagado'], FILTER_VALIDATE_FLOAT);
        $total = filter_var($data['total'], FILTER_VALIDATE_FLOAT);
        $cambio = filter_var($data['cambio'], FILTER_VALIDATE_FLOAT);

        if (!$fecha || !$cliente || $total === false || $cambio === false) {
            throw new Exception("Datos inválidos. Por favor, revisa el formulario.");
        }

        // Insertar la venta en la base de datos
        $sql = "INSERT INTO ventas (fecha, cliente, total, cambio) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("ssdd", $fecha, $cliente, $total, $cambio);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        $venta_id = $stmt->insert_id;
        $stmt->close();

        // Procesar cada producto en la venta
        foreach ($data['productos'] as $producto) {
            $producto_id = $producto['producto_id'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio'];

            // Verificar stock antes de actualizarlo
            $sql_stock_check = "SELECT stock FROM productos WHERE id = ?";
            $stmt_check = $conn->prepare($sql_stock_check);
            $stmt_check->bind_param("i", $producto_id);
            $stmt_check->execute();
            $stmt_check->bind_result($stock_actual);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($stock_actual < $cantidad) {
                throw new Exception("Stock insuficiente para el producto con ID $producto_id.");
            }

            // Insertar el producto vendido en ventas_productos
            $sql_item = "INSERT INTO ventas_productos (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
            $stmt_item = $conn->prepare($sql_item);
            if (!$stmt_item) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt_item->bind_param("iiid", $venta_id, $producto_id, $cantidad, $precio);
            if (!$stmt_item->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt_item->error);
            }
            $stmt_item->close();

            // Actualizar el stock del producto
            $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt_stock = $conn->prepare($sql_stock);
            if (!$stmt_stock) {
                throw new Exception("Error en la preparación de la consulta de stock: " . $conn->error);
            }
            $stmt_stock->bind_param("ii", $cantidad, $producto_id);
            if (!$stmt_stock->execute()) {
                throw new Exception("Error al ejecutar la consulta de stock: " . $stmt_stock->error);
            }
            $stmt_stock->close();

            // Registrar el movimiento de stock
            $movimiento = "Salida";
            $sql_movimiento = "INSERT INTO movimientos_stock (producto_id, cantidad, tipo, fecha) VALUES (?, ?, ?, NOW())";
            $stmt_movimiento = $conn->prepare($sql_movimiento);
            if (!$stmt_movimiento) {
                throw new Exception("Error en la preparación de la consulta de movimiento: " . $conn->error);
            }
            $stmt_movimiento->bind_param("iis", $producto_id, $cantidad, $movimiento);
            if (!$stmt_movimiento->execute()) {
                throw new Exception("Error al ejecutar la consulta de movimiento: " . $stmt_movimiento->error);
            }
            $stmt_movimiento->close();
        }

        echo json_encode(['success' => true, 'message' => "Venta guardada correctamente."]);
        $conn->close();
    } else {
        throw new Exception("Método no permitido.");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>