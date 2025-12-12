<?php
include 'conexion.php';


$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$rol = trim($_POST['rol'] ?? 'cliente');
$usuario = trim($_POST['usuario'] ?? '');
$clave = trim($_POST['clave'] ?? '');


if (empty($nombre) || empty($email) || empty($usuario) || empty($clave)) {
    header("Location: usuarios.php?error=Todos los campos son obligatorios");
    exit();
}


$clave_hash = password_hash($clave, PASSWORD_DEFAULT);


$estado = trim($_POST['estado'] ?? 'activo');


$stmt = $conexion->prepare("INSERT INTO usuarios (usuario, clave, email, nombre, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");

if ($stmt) {
    $stmt->bind_param("ssssss", $usuario, $clave_hash, $email, $nombre, $rol, $estado);

    if ($stmt->execute()) {
        header("Location: usuarios.php?ok=1&msg=Usuario creado exitosamente");
        exit();
    } else {
        $error = "Error al insertar: " . $stmt->error;
        header("Location: usuarios.php?error=" . urlencode($error));
        exit();
    }
    $stmt->close();
} else {
    header("Location: usuarios.php?error=" . urlencode("Error en la consulta: " . $conexion->error));
    exit();
}

$conexion->close();
?>

