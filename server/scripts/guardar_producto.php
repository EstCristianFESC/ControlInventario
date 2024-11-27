<?php
include '../controllers/productos.php';

$datos = [
    'nombreProducto' => $_POST['nombreProducto'],
    'categoriaProducto' => $_POST['categoriaProducto'],
    'marcaProducto' => $_POST['marcaProducto'],
    'generoProducto' => $_POST['generoProducto'],
    'tallaProducto' => $_POST['tallaProducto'],
    'precioProducto' => floatval($_POST['precioProducto']),
    'stockProducto' => intval($_POST['stockProducto']),
    'skuProducto' => $_POST['skuProducto'],
    'proveedor' => $_POST['proveedor']
];

$response = agregarProducto($datos);

if ($response['status'] === 'success') {
    echo "<script>alert('{$response['message']}'); window.location.href='../views/lista-productos.html';</script>";
} else {
    echo "<script>alert('{$response['message']}'); window.history.back();</script>";
}
?>