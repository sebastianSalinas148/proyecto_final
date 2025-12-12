<?php
session_start();


if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}


include 'conexion.php';

// Obtener el ID del empleado a eliminar
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $query = "DELETE FROM empleados WHERE id = $id";

    if ($conexion->query($query)) {
        // Redirigir a la página de empleados después de eliminar
        header("Location: empleados.php");
    } else {
        echo "Error al eliminar el empleado: " . $conexion->error;
    }
}
?>
