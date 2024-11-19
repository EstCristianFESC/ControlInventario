<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_barras = trim($_POST['codigo_barras']);
    
    // Verificar si el producto existe en la base de datos
    $sql = "SELECT * FROM productos WHERE codigo_barras = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_barras);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        
        // Añadir el producto a la sesión
        $producto_id = $producto['id'];
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }
        
        if (!isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id] = $producto;
            $_SESSION['carrito'][$producto_id]['cantidad'] = 1;
        } else {
            $_SESSION['carrito'][$producto_id]['cantidad'] += 1;
        }
        
        $_SESSION['mensaje'] = "Producto agregado correctamente.";
    } else {
        $_SESSION['error'] = "Producto no encontrado.";
    }
    $stmt->close();
    $conn->close();
}

header("Location: /views/nueva-venta.html");
exit();
?>