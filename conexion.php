<?php
$host = "localhost";
$usuario = "root";
$clave = "";
$bd = "proyecto_final";

$conexion = new mysqli($host, $usuario, $clave, $bd);


if ($conexion->connect_error) {
    die(" Error de conexión: " . $conexion->connect_error);
}

$ejecutado_directo = (basename($_SERVER["PHP_SELF"]) == "conexion.php");

if ($ejecutado_directo) {
    echo "<h2 style='color: green; text-align:center; margin-top:40px;'>✔ Conexión exitosa a la base de datos</h2>";
}
?>



