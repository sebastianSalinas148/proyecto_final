<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';  


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener los datos del usuario
    $query = "SELECT * FROM usuarios WHERE id = ?";
    
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            
            $user = $result->fetch_assoc();
        } else {
            echo "Usuario no encontrado.";
            exit();
        }
    } else {
        echo "Error en la consulta.";
        exit();
    }
} else {
    echo "No se ha especificado el ID del usuario.";
    exit();
}

// Verificar si el formulario se ha enviado para actualizar los datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];

    
    $update_query = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, estado = ? WHERE id = ?";

    if ($stmt = $conexion->prepare($update_query)) {
        $stmt->bind_param('ssssi', $nombre, $email, $rol, $estado, $id);
        if ($stmt->execute()) {
            header("Location: usuarios.php?mensaje=Usuario actualizado exitosamente");
            exit();
        } else {
            echo "Error al actualizar los datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Admin</title>
    <link rel="stylesheet" href="reset.css?v=3">
    <link rel="stylesheet" href="dashboard.css?v=4">
    <link rel="stylesheet" href="usuarios.css?v=3">
</head>
<body class="dashboard-layout">

<div class="wrapper">

    <aside class="sidebar">
        <h2 class="logo">FutbolSebasPro</h2>

        <a class="item" href="dashboard.php">Inicio</a>
        <a class="item active" href="usuarios.php">Usuarios</a>
        <a class="item" href="empleados.php">Empleados</a>
        <a class="item">Clientes</a>
        <a class="item">Canchas</a>
        <a class="item">Proveedores</a>

        <a class="logout" href="salir.php">⟲ Salir</a>
    </aside>

    <main class="content">

        <header class="topbar">
            <p>Buenas tardes, <b><?php echo $_SESSION['usuario']; ?></b></p>
            <div class="user-box"><?php echo $_SESSION['usuario']; ?></div>
        </header>

        <h1>Editar Usuario</h1>

        <div class="form-box">
            <form method="POST" action="editar_usuario.php?id=<?= $user['id'] ?>">

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?= $user['nombre'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select id="rol" name="rol">
                        <option value="Usuario" <?= $user['rol'] == 'Usuario' ? 'selected' : '' ?>>Usuario</option>
                        <option value="Administrador" <?= $user['rol'] == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="Activo" <?= $user['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $user['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="form-footer">
                    <a href="usuarios.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Actualizar Usuario</button>
                </div>

            </form>
        </div>

    </main>

</div>

</body>
</html>
