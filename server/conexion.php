<?php
// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ppa_control-inventario";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Eliminamos el mensaje de "Conexión exitosa"
// else {
//    echo "Conexión exitosa";
// }
?>