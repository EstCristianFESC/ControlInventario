<?php
include 'conexion.php'; // Asegúrate de que la ruta es correcta según tu estructura de carpetas

// Obtener datos del formulario
$nombreProducto = $_POST['nombreProducto'];
$categoriaProducto = $_POST['categoriaProducto'];
$marcaProducto = $_POST['marcaProducto'];
$generoProducto = $_POST['generoProducto'];
$tallaProducto = $_POST['tallaProducto'];
$precioProducto = $_POST['precioProducto'];
$stockProducto = $_POST['stockProducto'];
$skuProducto = $_POST['skuProducto'];
$proveedor = $_POST['proveedor'];

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO productos (nombre_producto, categoria_producto, marca_producto, genero_producto, talla_producto, precio_producto, stock_producto, sku_producto, proveedor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiddis", $nombreProducto, $categoriaProducto, $marcaProducto, $generoProducto, $tallaProducto, $precioProducto, $stockProducto, $skuProducto, $proveedor);

// Ejecutar la declaración
if ($stmt->execute()) {
    echo "<script>alert('Producto guardado con éxito'); window.location.href='../views/lista-productos.html';</script>";
} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
}

// Cerrar la declaración y la conexión
$stmt->close();
$conn->close();
?>