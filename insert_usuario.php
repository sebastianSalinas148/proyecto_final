<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$rol = $_POST['rol'];
$estado = $_POST['estado'];

$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, rol, estado) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $rol, $estado);

if ($stmt->execute()) {
    header("Location: usuarios.php?ok=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
