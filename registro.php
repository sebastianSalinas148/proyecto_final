<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include "conexion.php"; 

    $nombre = $_POST["nombre"];
    $usuario = $_POST["usuario"];
    $email = $_POST["email"];
    $clave = password_hash($_POST["clave"], PASSWORD_DEFAULT);
    $rol_input = $_POST["rol"] ?? "cliente";

    // Normalizar el rol a formato válido
    $rol_normalizado = strtolower($rol_input);
    
    
    $roles_map = [
        "cliente" => "Cliente",
        "empleado" => "Empleado",
        "administrador" => "Administrador",
        "admin" => "Administrador"
    ];
    
    
    $rol = $roles_map[$rol_normalizado] ?? "Cliente";

   
    $sql = "INSERT INTO usuarios (nombre, usuario, email, clave, rol)
            VALUES ('$nombre', '$usuario', '$email', '$clave', '$rol')";

    if ($conexion->query($sql)) {
        header("Location: login.php?registro=ok");
        exit();
    } else {
       
        $error = "Error: El usuario o correo ya está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - FutbolYa</title>
    <link rel="stylesheet" href="registro.css">
</head>
<body>

    <div class="overlay"></div>

    <div class="login-box">
        <h2>Crear Cuenta</h2>

        
        <?php if (isset($error)) { ?>
            <p style="color:red; text-align:center; margin-bottom:10px;">
                <?php echo $error; ?>
            </p>
        <?php } ?>

        <form method="POST" action="registro.php">

            <input type="text" placeholder="Nombre completo" name="nombre" class="input" required>

            <input type="text" placeholder="Usuario" name="usuario" class="input" required>

            <input type="email" placeholder="Correo electrónico" name="email" class="input" required>

            <input type="password" placeholder="Contraseña" name="clave" class="input" required>

            <select name="rol" class="input" required>
                <option value="cliente">Cliente</option>
                <option value="empleado">Empleado</option>
            </select>

            <button class="btn-ingresar" type="submit">Registrarse</button>

        </form>

        <a href="index.html" class="volver">← Volver al inicio</a>
    </div>

</body>
</html>

