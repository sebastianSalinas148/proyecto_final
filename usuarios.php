<?php 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$usuario = $_SESSION['usuario'];


$query = $conexion->query("SELECT id, nombre, email, rol, creado_en FROM usuarios ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Admin</title>

    <link rel="stylesheet" href="reset.css?v=4">
    <link rel="stylesheet" href="dashboard.css?v=6">
    <link rel="stylesheet" href="usuarios.css?v=3">
</head>
<body>

<div class="dashboard-layout">

    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>🎯 FutbolSebasPro</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="item">📊 Inicio</a>
            <a href="usuarios.php" class="item active">👥 Usuarios</a>
            <a href="empleados.php" class="item">⚽ Empleados</a>
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

        <h1>Usuarios</h1>
        <button class="btn-agregar" onclick="abrirModal()">+ Agregar Usuario</button>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($row = $query->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['rol'] ?></td>
                        <td><?= $row['creado_en'] ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?= $row['id'] ?>"><button>✏️</button></a>
                            <a href="eliminar_usuario.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este usuario?');"><button>🗑️</button></a>
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
        <h2>Agregar Usuario</h2>

        <form method="POST" action="insert_usuario.php">

            <label>Nombre</label>
            <input type="text" name="nombre" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Usuario</label>
            <input type="text" name="usuario" required>

            <label>Contraseña</label>
            <input type="password" name="clave" required>

            <label>Rol</label>
            <select name="rol">
                <option value="cliente">cliente</option>
                <option value="empleado">empleado</option>
                <option value="admin">admin</option>
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
    document.getElementById('modalAgregar').style.display = 'flex';
}
function cerrarModal() {
    document.getElementById('modalAgregar').style.display = 'none';
}
</script>

</body>
</html>
