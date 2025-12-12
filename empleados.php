<?php 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php'; 
$usuario = $_SESSION['usuario'];

$query = $conexion->query("SELECT * FROM empleados ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Admin</title>
    <link rel="stylesheet" href="reset.css?v=4">
    <link rel="stylesheet" href="dashboard.css?v=6">
    <link rel="stylesheet" href="empleados.css?v=3">
</head>
<body>

    <div class="dashboard-layout">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🎯 FutbolSebasPro</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="item">📊 Inicio</a>
                <a href="usuarios.php" class="item">👥 Usuarios</a>
                <a href="empleados.php" class="item active">⚽ Empleados</a>
                <a href="clientes.php" class="item">👤 Clientes</a>
                <a href="gestionar-canchas.php" class="item">🏟️ Canchas</a>
                <a href="gestionar-proveedores.php" class="item">🏢 Proveedores</a>
                <a href="logout.php" class="item logout">🚪 Cerrar Sesión</a>
            </nav>
        </aside>

        
        <div class="content">
        <header class="topbar">
            <p>Buenas tardes, <b><?php echo $usuario; ?></b></p>
            <div class="user-box"><?php echo $usuario; ?></div>
        </header>

        <h1>Empleados</h1>
        
        <button class="btn-agregar" onclick="abrirModal()">+ Agregar Empleado</button>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Teléfono</th>
                        <th>Salario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $query->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['nombre'] ?></td>
                            <td><?= $row['cargo'] ?></td>
                            <td><?= $row['telefono'] ?></td>
                            <td><?= $row['salario'] ?></td>
                            <td><?= $row['estado'] ?></td>
                            <td>
                                <a href="editar_empleado.php?id=<?= $row['id'] ?>" class="editar-btn">✏️</a>
                                <a href="eliminar_empleado.php?id=<?= $row['id'] ?>" class="eliminar-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este empleado?');">🗑️</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>

    
    <div class="modal" id="modalAgregar">
        <div class="modal-content">
            <h2>Agregar Empleado</h2>
            <form method="POST" action="insert_empleado.php">
                <label>Nombre</label>
                <input type="text" name="nombre" required>

                <label>Cargo</label>
                <input type="text" name="cargo" required>

                <label>Teléfono</label>
                <input type="text" name="telefono" required>

                <label>Salario</label>
                <input type="number" name="salario" step="0.01" required>

                <label>Estado</label>
                <select name="estado">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>

                <div class="modal-footer">
                    <button type="button" class="cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
       
        function abrirModal() {
            document.getElementById("modalAgregar").style.display = "flex";
        }

       
        function cerrarModal() {
            document.getElementById("modalAgregar").style.display = "none";
        }
    </script>
</body>
</html>

