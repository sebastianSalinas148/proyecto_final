<?php
session_start();
include 'conexion.php';

// Verificar que sea administrador o empleado
$rol = strtolower($_SESSION['rol'] ?? '');
if ($rol !== 'administrador' && $rol !== 'admin' && $rol !== 'empleado') {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];


$query = "SELECT id, empresa, contacto, telefono, email, producto_servicio, estado FROM proveedores ORDER BY empresa";
$result = $conexion->query($query);
$proveedores = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $empresa = $conexion->real_escape_string($_POST['empresa']);
        $contacto = $conexion->real_escape_string($_POST['contacto']);
        $telefono = $conexion->real_escape_string($_POST['telefono']);
        $email = $conexion->real_escape_string($_POST['email']);
        $producto_servicio = $conexion->real_escape_string($_POST['producto_servicio']);
        $estado = $conexion->real_escape_string($_POST['estado']);
        
        $sql = "INSERT INTO proveedores (empresa, contacto, telefono, email, producto_servicio, estado) 
                VALUES ('$empresa', '$contacto', '$telefono', '$email', '$producto_servicio', '$estado')";
        if ($conexion->query($sql)) {
            header("Location: gestionar-proveedores.php?success=1");
            exit();
        } else {
            $error = "Error al agregar proveedor: " . $conexion->error;
        }
    } elseif ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $empresa = $conexion->real_escape_string($_POST['empresa']);
        $contacto = $conexion->real_escape_string($_POST['contacto']);
        $telefono = $conexion->real_escape_string($_POST['telefono']);
        $email = $conexion->real_escape_string($_POST['email']);
        $producto_servicio = $conexion->real_escape_string($_POST['producto_servicio']);
        $estado = $conexion->real_escape_string($_POST['estado']);
        
        $sql = "UPDATE proveedores SET empresa = '$empresa', contacto = '$contacto', telefono = '$telefono', 
                email = '$email', producto_servicio = '$producto_servicio', estado = '$estado' WHERE id = $id";
        if ($conexion->query($sql)) {
            header("Location: gestionar-proveedores.php?success=2");
            exit();
        } else {
            $error = "Error al editar proveedor: " . $conexion->error;
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM proveedores WHERE id = $id";
        if ($conexion->query($sql)) {
            header("Location: gestionar-proveedores.php?success=3");
            exit();
        } else {
            $error = "Error al eliminar proveedor: " . $conexion->error;
        }
    }
}

// Buscar proveedor
$busqueda = $_GET['buscar'] ?? '';
if (!empty($busqueda)) {
    $busqueda_safe = $conexion->real_escape_string($busqueda);
    $query = "SELECT id, empresa, contacto, telefono, email, producto_servicio, estado 
              FROM proveedores WHERE empresa LIKE '%$busqueda_safe%' OR contacto LIKE '%$busqueda_safe%' 
              OR producto_servicio LIKE '%$busqueda_safe%' ORDER BY empresa";
    $result = $conexion->query($query);
    $proveedores = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores - FutbolSebasPro</title>
    <link rel="stylesheet" href="reset.css?v=4">
    <link rel="stylesheet" href="dashboard.css?v=6">
    <style>
        .proveedores-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .proveedores-header h1 {
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

        .search-bar {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f8f8;
            border-bottom: 2px solid #e0e0e0;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.95em;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .estado-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .estado-activo {
            background: #d4edda;
            color: #155724;
        }

        .estado-inactivo {
            background: #f8d7da;
            color: #721c24;
        }

        .acciones {
            display: flex;
            gap: 8px;
        }

            .btn-editar, .btn-eliminar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 8px 12px;
                border-radius: 6px;
                border: none;
                cursor: pointer;
                margin-right: 6px;
                font-size: 14px;
            }
            .btn-editar {
                background: #17a2b8;
                color: #fff;
            }
            .btn-editar:hover {
                background: #138496;
            }
            .btn-eliminar {
                background: #dc3545; 
                color: #fff;
                width: 40px; 
                height: 40px;
                padding: 0;
                border-radius: 8px;
            }
            .btn-eliminar:hover {
                background: #c82333;
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            color: #1a1a1a;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .close-btn:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .btn-guardar {
            background: linear-gradient(135deg, #1a4d1a 0%, #0d3d0d 100%);
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 77, 26, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .sin-datos {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .responsive-table {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            th, td {
                padding: 10px 8px;
                font-size: 0.85em;
            }

            .acciones {
                flex-direction: column;
            }

            .btn-editar, .btn-eliminar {
                width: 100%;
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
                <a href="clientes.php" class="item">👤 Clientes</a>
                <a href="gestionar-canchas.php" class="item">🏟️ Canchas</a>
                <a href="gestionar-proveedores.php" class="item active">🏢 Proveedores</a>
                <a href="logout.php" class="item logout">🚪 Cerrar Sesión</a>
            </nav>
        </aside>

        
        <div class="content">
            <header class="topbar">
                <p>Buenas tardes, <b><?php echo $usuario; ?></b></p>
                <div class="user-box"><?php echo $usuario; ?></div>
            </header>

            <div class="proveedores-header">
                <div>
                    <h1>🏢 Gestión de Proveedores</h1>
                    <p class="subtitle">Administra todos tus proveedores y servicios</p>
                </div>
                <button class="btn-agregar" onclick="abrirModalAgregar()">+ Agregar Proveedor</button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <?php 
                    $msg = '';
                    if ($_GET['success'] == 1) $msg = '✓ Proveedor agregado correctamente';
                    elseif ($_GET['success'] == 2) $msg = '✓ Proveedor actualizado correctamente';
                    elseif ($_GET['success'] == 3) $msg = '✓ Proveedor eliminado correctamente';
                ?>
                <div class="alert alert-success"><?php echo $msg; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            
            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="buscar" placeholder="🔍 Buscar por empresa, contacto o producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
                </form>
            </div>

           
            <?php if (count($proveedores) > 0): ?>
                <div class="table-container">
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Contacto</th>
                                    <th>Teléfono</th>
                                    <th>Producto/Servicio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($proveedor['empresa']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($proveedor['contacto']); ?></td>
                                        <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($proveedor['producto_servicio']); ?></td>
                                        <td>
                                            <span class="estado-badge <?php echo $proveedor['estado'] === 'Activo' ? 'estado-activo' : 'estado-inactivo'; ?>">
                                                <?php echo $proveedor['estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="acciones">
                                                <button class="btn-editar" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($proveedor)); ?>)" title="Editar">✏️</button>
                                                <button class="btn-eliminar" onclick="abrirModalEliminar(<?php echo $proveedor['id']; ?>, '<?php echo htmlspecialchars($proveedor['empresa']); ?>')" title="Eliminar">🗑️</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <div class="sin-datos">
                        <h3>📭 No hay proveedores registrados</h3>
                        <p>Agrega tu primer proveedor haciendo clic en el botón "Agregar Proveedor"</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

 
    <div id="modalForm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Proveedor</h2>
                <button class="close-btn" onclick="cerrarModal()">&times;</button>
            </div>
            <form method="POST" id="formProveedor">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="id" value="">

                <div class="form-group">
                    <label for="empresa">Empresa *</label>
                    <input type="text" id="empresa" name="empresa" required>
                </div>

                <div class="form-group">
                    <label for="contacto">Contacto *</label>
                    <input type="text" id="contacto" name="contacto" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>

                <div class="form-group">
                    <label for="producto_servicio">Producto/Servicio *</label>
                    <input type="text" id="producto_servicio" name="producto_servicio" required>
                </div>

                <div class="form-group">
                    <label for="estado">Estado *</label>
                    <select id="estado" name="estado" required>
                        <option value="">-- Selecciona --</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

                <button type="submit" class="btn-guardar">💾 Guardar</button>
            </form>
        </div>
    </div>

    
    <div id="modalEliminar" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Eliminar Proveedor</h2>
                <button class="close-btn" onclick="cerrarModalEliminar()">&times;</button>
            </div>
            <p style="margin-bottom: 20px; color: #666;">
                ¿Estás seguro de que deseas eliminar al proveedor "<strong id="nombreProveedor"></strong>"? Esta acción no se puede deshacer.
            </p>
            <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="idEliminar" value="">
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-editar" onclick="cerrarModalEliminar()" style="background: #6c757d;">Cancelar</button>
                    <button type="submit" class="btn-eliminar" title="Eliminar">🗑️</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        
        function abrirModalAgregar() {
            document.getElementById('modalTitle').textContent = 'Agregar Proveedor';
            document.getElementById('action').value = 'add';
            document.getElementById('id').value = '';
            document.getElementById('formProveedor').reset();
            document.getElementById('modalForm').classList.add('active');
        }

        function abrirModalEditar(proveedor) {
            document.getElementById('modalTitle').textContent = 'Editar Proveedor';
            document.getElementById('action').value = 'edit';
            document.getElementById('id').value = proveedor.id;
            document.getElementById('empresa').value = proveedor.empresa;
            document.getElementById('contacto').value = proveedor.contacto;
            document.getElementById('telefono').value = proveedor.telefono;
            document.getElementById('email').value = proveedor.email;
            document.getElementById('producto_servicio').value = proveedor.producto_servicio;
            document.getElementById('estado').value = proveedor.estado;
            document.getElementById('modalForm').classList.add('active');
        }

        function cerrarModal() {
            document.getElementById('modalForm').classList.remove('active');
        }

        
        function abrirModalEliminar(id, empresa) {
            document.getElementById('nombreProveedor').textContent = empresa;
            document.getElementById('idEliminar').value = id;
            document.getElementById('modalEliminar').classList.add('active');
        }

        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').classList.remove('active');
        }

       
        window.addEventListener('click', function(event) {
            const modalForm = document.getElementById('modalForm');
            const modalEliminar = document.getElementById('modalEliminar');
            if (event.target == modalForm) {
                modalForm.classList.remove('active');
            }
            if (event.target == modalEliminar) {
                modalEliminar.classList.remove('active');
            }
        });
    </script>

</body>
</html>
