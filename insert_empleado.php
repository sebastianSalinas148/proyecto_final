<?php
include 'conexion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['nombre'];
    $cargo = $_POST['cargo'];
    $telefono = $_POST['telefono'];
    $salario = $_POST['salario'];
    $estado = $_POST['estado'];

  
    $query = "INSERT INTO empleados (nombre, cargo, telefono, salario, estado) 
              VALUES ('$nombre', '$cargo', '$telefono', '$salario', '$estado')";

    if ($conexion->query($query)) {
        echo "Empleado agregado correctamente";
        header("Location: empleados.php"); 
    } else {
        echo "Error al agregar empleado: " . $conexion->error;
    }
}
?>

