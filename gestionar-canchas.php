<?php
session_start();
include 'conexion.php';

// Verificar que sea administrador o empleado
$rol = strtolower($_SESSION['rol'] ?? '');
if ($rol !== 'administrador' && $rol !== 'admin' && $rol !== 'empleado') {
    header("Location: login.php");
    exit();
}
// Procesar formularios (agregar, editar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'add') {
        $nombre = $conexion->real_escape_string($_POST['nombre'] ?? '');
        $descripcion = $conexion->real_escape_string($_POST['descripcion'] ?? '');
        $tipo = $conexion->real_escape_string($_POST['tipo'] ?? '');
        $capacidad = intval($_POST['capacidad'] ?? 0);
        $precio_hora = floatval($_POST['precio_hora'] ?? 0);
        $disponible = isset($_POST['disponible']) ? 1 : 0;

        $sql = "INSERT INTO canchas (nombre, descripcion, tipo, capacidad, precio_hora, disponible) VALUES ('{$nombre}', '{$descripcion}', '{$tipo}', {$capacidad}, {$precio_hora}, {$disponible})";
        if ($conexion->query($sql)) {
            header('Location: gestionar-canchas.php?success=1');
            exit();
        } else {
            $error = 'Error al agregar la cancha: ' . $conexion->error;
        }

    } elseif ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $nombre = $conexion->real_escape_string($_POST['nombre'] ?? '');
        $descripcion = $conexion->real_escape_string($_POST['descripcion'] ?? '');
        $tipo = $conexion->real_escape_string($_POST['tipo'] ?? '');
        $capacidad = intval($_POST['capacidad'] ?? 0);
        $precio_hora = floatval($_POST['precio_hora'] ?? 0);
        $disponible = isset($_POST['disponible']) ? 1 : 0;

        if ($id > 0) {
            $sql = "UPDATE canchas SET nombre='{$nombre}', descripcion='{$descripcion}', tipo='{$tipo}', capacidad={$capacidad}, precio_hora={$precio_hora}, disponible={$disponible} WHERE id={$id}";
            if ($conexion->query($sql)) {
                header('Location: gestionar-canchas.php?success=2');
                exit();
            } else {
                $error = 'Error al actualizar la cancha: ' . $conexion->error;
            }
        } else {
            $error = 'ID de cancha inválido para editar.';
        }

    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $sql = "DELETE FROM canchas WHERE id={$id}";
            if ($conexion->query($sql)) {
                header('Location: gestionar-canchas.php?success=3');
                exit();
            } else {
                $error = 'Error al eliminar la cancha: ' . $conexion->error;
            }
        } else {
            $error = 'ID de cancha inválido para eliminar.';
        }
    }
}
?>

<?php
// Inicializar búsqueda y lista de canchas para evitar warnings
$busqueda = $_GET['buscar'] ?? $_GET['busqueda'] ?? '';
if (!isset($canchas) || !is_array($canchas)) {
    $result = $conexion->query("SELECT id, nombre, descripcion, tipo, capacidad, precio_hora, disponible, fecha_creacion FROM canchas ORDER BY nombre");
    $canchas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Canchas - FutbolSebasPro</title>
    <link rel="stylesheet" href="reset.css?v=4">
    <link rel="stylesheet" href="dashboard.css?v=6">
    <style>
        .canchas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .canchas-header h1 {
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
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .estado-disponible {
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
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85em;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-editar {
            background: #007bff;
            color: white;
        }

        .btn-editar:hover {
            background: #0056b3;
        }

        .btn-eliminar {
            background: #dc3545;
            color: white;
        }

        .btn-eliminar:hover {
            background: #a02622;
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
            min-height: 80px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input {
            width: auto;
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
                <a href="gestionar-canchas.php" class="item active">🏟️ Canchas</a>
                <a href="logout.php" class="item logout">🚪 Cerrar Sesión</a>
            </nav>
        </aside>

      
        <div class="content">
            <header class="topbar">
                <p>Buenas tardes, <b><?php echo $usuario; ?></b></p>
                <div class="user-box"><?php echo $usuario; ?></div>
            </header>

            <div class="canchas-header">
                <div>
                    <h1>🏟️ Gestión de Canchas</h1>
                    <p class="subtitle">Administra todas las canchas disponibles</p>
                </div>
                <button class="btn-agregar" onclick="abrirModalAgregar()">+ Agregar Cancha</button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <?php 
                    $msg = '';
                    if ($_GET['success'] == 1) $msg = '✓ Cancha agregada correctamente';
                    elseif ($_GET['success'] == 2) $msg = '✓ Cancha actualizada correctamente';
                    elseif ($_GET['success'] == 3) $msg = '✓ Cancha eliminada correctamente';
                ?>
                <div class="alert alert-success"><?php echo $msg; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            
            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="buscar" placeholder="🔍 Buscar por nombre, tipo o descripción..." value="<?php echo htmlspecialchars($busqueda); ?>">
                </form>
            </div>

           
            <?php if (!empty($canchas)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Capacidad</th>
                                <th>Precio/Hora</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($canchas as $cancha): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($cancha['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($cancha['tipo']); ?></td>
                                    <td><?php echo $cancha['capacidad']; ?> personas</td>
                                    <td>$<?php echo number_format($cancha['precio_hora'], 2); ?></td>
                                    <td>
                                        <span class="estado-disponible <?php echo $cancha['disponible'] ? 'estado-activo' : 'estado-inactivo'; ?>">
                                            <?php echo $cancha['disponible'] ? '✓ Disponible' : '✗ No disponible'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="acciones">
                                            <button class="btn-editar" onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($cancha)); ?>)" title="Editar">✏️</button>
                                            <button class="btn-eliminar" onclick="abrirModalEliminar(<?php echo $cancha['id']; ?>, '<?php echo htmlspecialchars($cancha['nombre']); ?>')" title="Eliminar">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <div class="sin-datos">
                        <h3>📭 No hay canchas registradas</h3>
                        <p>Agrega tu primera cancha haciendo clic en el botón "Agregar Cancha"</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div id="modalForm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Cancha</h2>
                <button class="close-btn" onclick="cerrarModal()">&times;</button>
            </div>
            <form method="POST" id="formCancha">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="id" value="">

                <div class="form-group">
                    <label for="nombre">Nombre de la Cancha *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion"></textarea>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de Cancha *</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">-- Selecciona --</option>
                        <option value="Fútbol 5">Fútbol 5</option>
                        <option value="Fútbol 7">Fútbol 7</option>
                        <option value="Fútbol 11">Fútbol 11</option>
                        <option value="Indoor">Indoor</option>
                        <option value="Otra">Otra</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="capacidad">Capacidad (personas) *</label>
                    <input type="number" id="capacidad" name="capacidad" min="1" required>
                </div>

                <div class="form-group">
                    <label for="precio_hora">Precio por Hora ($) *</label>
                    <input type="number" id="precio_hora" name="precio_hora" step="0.01" min="0" required>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="disponible" name="disponible">
                    <label for="disponible" style="margin-bottom: 0;">Cancha Disponible</label>
                </div>

                <button type="submit" class="btn-guardar">💾 Guardar</button>
            </form>
        </div>
    </div>

    
    <div id="modalEliminar" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Eliminar Cancha</h2>
                <button class="close-btn" onclick="cerrarModalEliminar()">&times;</button>
            </div>
            <p style="margin-bottom: 20px; color: #666;">
                ¿Estás seguro de que deseas eliminar la cancha "<strong id="nombreCancha"></strong>"? Esta acción no se puede deshacer.
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
        // Funciones del Modal Agregar/Editar
        function abrirModalAgregar() {
            document.getElementById('modalTitle').textContent = 'Agregar Cancha';
            document.getElementById('action').value = 'add';
            document.getElementById('id').value = '';
            document.getElementById('formCancha').reset();
            document.getElementById('modalForm').classList.add('active');
        }

        function abrirModalEditar(cancha) {
            document.getElementById('modalTitle').textContent = 'Editar Cancha';
            document.getElementById('action').value = 'edit';
            document.getElementById('id').value = cancha.id;
            document.getElementById('nombre').value = cancha.nombre;
            document.getElementById('descripcion').value = cancha.descripcion;
            document.getElementById('tipo').value = cancha.tipo;
            document.getElementById('capacidad').value = cancha.capacidad;
            document.getElementById('precio_hora').value = cancha.precio_hora;
            document.getElementById('disponible').checked = cancha.disponible == 1;
            document.getElementById('modalForm').classList.add('active');
        }

        function cerrarModal() {
            document.getElementById('modalForm').classList.remove('active');
        }

        // Funciones del Modal Eliminar
        function abrirModalEliminar(id, nombre) {
            document.getElementById('nombreCancha').textContent = nombre;
            document.getElementById('idEliminar').value = id;
            document.getElementById('modalEliminar').classList.add('active');
        }

        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').classList.remove('active');
        }

        // Cerrar modal al hacer clic fuera
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
