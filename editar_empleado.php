<?php
session_start();


if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}


include 'conexion.php';


if (isset($_GET['id'])) {
    $id = $_GET['id'];

 
    $query = "SELECT * FROM empleados WHERE id = $id";
    $result = $conexion->query($query);

    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
    } else {
        echo "Empleado no encontrado";
        exit();
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $cargo = $_POST['cargo'];
    $telefono = $_POST['telefono'];
    $salario = $_POST['salario'];
    $estado = $_POST['estado'];

 
    $updateQuery = "UPDATE empleados SET nombre = '$nombre', cargo = '$cargo', telefono = '$telefono', salario = '$salario', estado = '$estado' WHERE id = $id";

    if ($conexion->query($updateQuery)) {
        header("Location: empleados.php"); 
    } else {
        echo "Error al actualizar el empleado: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="reset.css?v=3">
    <link rel="stylesheet" href="dashboard.css?v=4">
    <link rel="stylesheet" href="empleados.css?v=3">
</head>
<body class="dashboard-layout">

    <aside class="sidebar">
        <h2 class="logo">FutbolSebasPro</h2>
        <a class="item" href="dashboard.php">Inicio</a>
        <a class="item" href="usuarios.php">Usuarios</a>
        <a class="item active" href="empleados.php">Empleados</a>
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

        <h1>Editar Empleado</h1>

        <div class="form-box">
            <form method="POST" action="editar_empleado.php?id=<?= $empleado['id'] ?>">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?= $empleado['nombre'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo</label>
                    <input type="text" id="cargo" name="cargo" value="<?= $empleado['cargo'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?= $empleado['telefono'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="salario">Salario</label>
                    <input type="number" id="salario" name="salario" value="<?= $empleado['salario'] ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="Activo" <?= $empleado['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= $empleado['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="form-footer">
                    <a href="empleados.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Actualizar Empleado</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
