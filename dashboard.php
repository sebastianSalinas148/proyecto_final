<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar que sea administrador o empleado
$rol = strtolower($_SESSION['rol'] ?? '');
if ($rol !== 'administrador' && $rol !== 'admin' && $rol !== 'empleado') {
    // Si no es admin o empleado, redirigir a canchas (vista de cliente)
    header("Location: canchas.php");
    exit();
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - FutbolYa</title>
    <link rel="stylesheet" href="reset.css?v=4">
    <link rel="stylesheet" href="dashboard.css?v=6">
</head>
<body>

    <div class="dashboard-layout">
       
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🎯 FutbolSebasPro</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="item active">📊 Inicio</a>
                <a href="usuarios.php" class="item">👥 Usuarios</a>
                <a href="empleados.php" class="item">⚽ Empleados</a>
                <a href="clientes.php" class="item">👤 Clientes</a>
                <a href="gestionar-canchas.php" class="item">🏟️ Canchas</a>
                <a href="gestionar-proveedores.php" class="item">🏢 Proveedores</a>
                <a href="logout.php" class="item logout">🚪 Cerrar Sesión</a>
            </nav>
        </aside>

        
        <div class="content">
        <header class="topbar">
            <button id="sidebarToggle" class="sidebar-toggle" aria-label="Alternar menú" onclick="toggleSidebar()">☰</button>
            <p>Buenas tardes, <b><?php echo $usuario; ?></b></p>
            <div class="user-box"><?php echo $usuario; ?></div>
        </header>

        <h1>Dashboard</h1>
        <p class="subtitle">Resumen general del sistema</p>

        <div class="cards">
            <div class="card">
                <h3>156</h3>
                <p>Usuarios</p>
                <span class="stat">+12%</span>
            </div>

            <div class="card">
                <h3>24</h3>
                <p>Reservas Hoy</p>
                <span class="stat">+8%</span>
            </div>

            <div class="card">
                <h3>$4,250</h3>
                <p>Ingresos Mes</p>
                <span class="stat">+23%</span>
            </div>

            <div class="card">
                <h3>87%</h3>
                <p>Ocupación</p>
                <span class="stat">+5%</span>
            </div>
        </div>

        <div class="table-box">
            <h2>Reservas Recientes</h2>

            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Cancha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Juan Pérez</td>
                        <td>Cancha Principal</td>
                        <td>18:00</td>
                        <td><span class="estado ok">Confirmada</span></td>
                    </tr>
                    <tr>
                        <td>María García</td>
                        <td>Cancha Indoor</td>
                        <td>19:00</td>
                        <td><span class="estado pendiente">Pendiente</span></td>
                    </tr>
                    <tr>
                        <td>Carlos López</td>
                        <td>Cancha Sintética A</td>
                        <td>20:00</td>
                        <td><span class="estado ok">Confirmada</span></td>
                    </tr>
                    <tr>
                        <td>Ana Martínez</td>
                        <td>Cancha Indoor</td>
                        <td>21:00</td>
                        <td><span class="estado cancelada">Cancelada</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

       
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h4>FutbolSebasPro</h4>
                        <p>Empresa de alquiler de canchas de fútbol de calidad profesional.</p>
                    </div>
                    <div class="footer-section">
                        <h4>Usuario</h4>
                        <p><?php echo htmlspecialchars($usuario); ?></p>
                    </div>
                    <div class="footer-section">
                        <h4>Fecha</h4>
                        <p id="footer-fecha"></p>
                    </div>
                </div>
                <div class="footer-divider"></div>
                <div class="footer-bottom">
                    <p>&copy; <span id="year"></span> FutbolSebasPro - Todos los derechos reservados</p>
                    <p>Desarrollado por: <strong>Sebastian Salinas</strong></p>
                </div>
            </div>
        </footer>

        <!-- Backdrop para mobile cuando el sidebar está abierto -->
        <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

        </main>
    </div>

    <script>
      
        document.getElementById('year').textContent = new Date().getFullYear();
        
       
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const hoy = new Date().toLocaleDateString('es-ES', options);
        document.getElementById('footer-fecha').textContent = hoy.charAt(0).toUpperCase() + hoy.slice(1);
    </script>
</body>
</html>

