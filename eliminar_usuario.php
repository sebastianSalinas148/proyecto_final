<?php
session_start(); 


if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';  

if (isset($_GET['id'])) {
    $id = $_GET['id']; 

  
    $query = "DELETE FROM usuarios WHERE id = ?";
    
    
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param('i', $id); 
        $stmt->execute(); 
        
        
        if ($stmt->affected_rows > 0) {
           
            header("Location: usuarios.php?mensaje=Usuario eliminado exitosamente");
            exit();
        } else {
            echo "Error al eliminar el usuario.";
        }

        $stmt->close(); 
    } else {
        echo "Error en la preparación de la consulta.";
    }
} else {
    echo "No se ha especificado el ID del usuario.";
}
?>

