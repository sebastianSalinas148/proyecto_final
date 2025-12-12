<?php
session_start();
include "conexion.php"; 

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' LIMIT 1";
$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {

    $row = $result->fetch_assoc();

    if (password_verify($clave, $row['clave'])) {

        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['rol'] = $row['rol'];

        
        $rol_normalizado = strtolower($row['rol']);
        
        // Redirigir según el rol del usuario
        if ($rol_normalizado === 'administrador' || $rol_normalizado === 'admin') {
          
            header("Location: dashboard.php");
        } elseif ($rol_normalizado === 'empleado') {
            // Si es empleado, también va al dashboard 
            header("Location: dashboard.php");
        } else {
            // Si es cliente, va a la página de canchas
            header("Location: canchas.php");
        }
        exit();
    }
}

header("Location: login.php?error=1");
exit();
?>
