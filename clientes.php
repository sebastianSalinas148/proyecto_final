<?php
session_start();
include 'conexion.php';

// Verificar que sea administrador o empleado
$rol = strtolower($_SESSION['rol'] ?? '');
if ($rol !== 'administrador' && $rol !== 'admin' && $rol !== 'empleado') {
    header("Location: canchas.php");
    exit();
}

$usuario = $_SESSION['usuario'];


$query = "SELECT id, nombre, usuario, email, rol FROM usuarios WHERE rol = 'Cliente' ORDER BY nombre";
$result = $conexion->query($query);
$clientes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $nombre = $_POST['nombre'];
        $usuario_nuevo = $_POST['usuario'];
        $email = $_POST['email'];
        $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre, usuario, email, clave, rol) VALUES ('$nombre', '$usuario_nuevo', '$email', '$clave', 'Cliente')";
        if ($conexion->query($sql)) {
            header("Location: clientes.php");
            exit();
        }
    } elseif ($_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        
        $sql = "UPDATE usuarios SET nombre = '$nombre', email = '$email' WHERE id = $id";
        $conexion->query($sql);
        header("Location: clientes.php");
        exit();
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM usuarios WHERE id = $id";
        $conexion->query($sql);
        header("Location: clientes.php");
        exit();
    }
}


$busqueda = $_GET['buscar'] ?? '';
if (!empty($busqueda)) {
    $busqueda_safe = $conexion->real_escape_string($busqueda);
    $query = "SELECT id, nombre, usuario, email, rol FROM usuarios WHERE rol = 'Cliente' AND (nombre LIKE '%$busqueda_safe%' OR email LIKE '%$busqueda_safe%' OR usuario LIKE '%$busqueda_safe%') ORDER BY nombre";
    $result = $conexion->query($query);
    $clientes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - FutbolSebasPro</title>
    <link rel="stylesheet" href="reset.css?v=4">
    <link rel="stylesheet" href="dashboard.css?v=6">
    <style>
        .clientes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .clientes-header h1 {
            font-size: 2em;
            color: #1a1a1a;
            margin: 0;
        }

        .btn-agregar {
            background: linear-gradient(135deg, #1a4d1a 0%, #0d3d0d 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.95em;
            transition: all 0.3s;
        }

        .btn-agregar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 77, 26, 0.3);
        }

        .btn-agregar::before {
            content: "+ ";
        }

        .search-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .search-container input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .search-container input:focus {
            outline: none;
            border-color: #1a4d1a;
            box-shadow: 0 0 0 4px rgba(26, 77, 26, 0.1);
        }

        .clientes-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 700;
            color: #1a1a1a;
            font-size: 0.95em;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        tbody tr:hover {
            background: #f9f9f9;
        }

        .estado-activo {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            display: inline-block;
        }

        .estado-inactivo {
            background: #ffebee;
            color: #c62828;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            display: inline-block;
        }

        .acciones {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 1.2em;
            transition: all 0.3s;
        }

        .btn-edit {
            color: #1976d2;
        }

        .btn-edit:hover {
            background: #e3f2fd;
        }

        .btn-delete {
            color: #d32f2f;
        }

        .btn-delete:hover {
            background: #ffebee;
        }

        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.active {
            display: block;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: #1a1a1a;
            font-size: 1.5em;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 2em;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #1a1a1a;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1a4d1a;
            box-shadow: 0 0 0 4px rgba(26, 77, 26, 0.1);
        }

        .form-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #1a4d1a 0%, #0d3d0d 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 77, 26, 0.3);
        }

        .btn-cancel {
            flex: 1;
            background: #f0f0f0;
            color: #333;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .sin-resultados {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .sin-resultados p {
            font-size: 1.1em;
        }

        @media (max-width: 768px) {
            .clientes-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .modal-content {
                width: 95%;
                padding: 20px;
            }

            table {
                font-size: 0.9em;
            }

            th, td {
                padding: 12px;
            }
        }
    </style>
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
                <a href="empleados.php" class="item">⚽ Empleados</a>
                <a href="clientes.php" class="item active">👤 Clientes</a>
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

            
            <main class="main-content">
                
                <div class="clientes-header">
                    <h1>👤 Clientes</h1>
                    <button class="btn-agregar" onclick="abrirModal()">Agregar</button>
                </div>

               
                <div class="search-container">
                    <form method="GET" action="clientes.php">
                        <input type="text" name="buscar" placeholder="Buscar cliente por nombre, email o usuario..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    </form>
                </div>

                
                <div class="clientes-table">
                    <?php if (count($clientes) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['usuario']); ?></td>
                                        <td>
                                            <span class="estado-activo">Activo</span>
                                        </td>
                                        <td>
                                            <div class="acciones">
                                                <button class="btn-icon btn-edit" onclick="editarCliente(<?php echo htmlspecialchars(json_encode($cliente)); ?>)" title="Editar">✏️</button>
                                                <button class="btn-icon btn-delete" onclick="eliminarCliente(<?php echo $cliente['id']; ?>)" title="Eliminar">🗑️</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="sin-resultados">
                            <p>No hay clientes registrados</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

   
    <div id="clienteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Cliente</h2>
                <button class="close-btn" onclick="cerrarModal()">&times;</button>
            </div>
            <form id="clienteForm" method="POST" action="clientes.php">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="clienteId">

                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group" id="passwordGroup">
                    <label for="clave">Contraseña</label>
                    <input type="password" id="clave" name="clave" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Guardar</button>
                    <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById('modalTitle').textContent = 'Agregar Cliente';
            document.getElementById('action').value = 'add';
            document.getElementById('clienteId').value = '';
            document.getElementById('clienteForm').reset();
            document.getElementById('passwordGroup').style.display = 'block';
            document.getElementById('clave').required = true;
            document.getElementById('usuario').readOnly = false;
            document.getElementById('clienteModal').classList.add('active');
        }

        function cerrarModal() {
            document.getElementById('clienteModal').classList.remove('active');
        }

        function editarCliente(cliente) {
            document.getElementById('modalTitle').textContent = 'Editar Cliente';
            document.getElementById('action').value = 'edit';
            document.getElementById('clienteId').value = cliente.id;
            document.getElementById('nombre').value = cliente.nombre;
            document.getElementById('usuario').value = cliente.usuario;
            document.getElementById('email').value = cliente.email;
            document.getElementById('usuario').readOnly = true;
            document.getElementById('passwordGroup').style.display = 'none';
            document.getElementById('clave').required = false;
            document.getElementById('clienteModal').classList.add('active');
        }

        function eliminarCliente(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este cliente?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'clientes.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('clienteModal');
            if (event.target == modal) {
                cerrarModal();
            }
        }
    </script>
</body>
</html>
