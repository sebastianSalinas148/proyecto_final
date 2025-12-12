<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$usuario = $_SESSION['usuario'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener las reservas del usuario
$query = "SELECT r.*, c.nombre as cancha_nombre, c.precio_hora 
          FROM reservas r 
          JOIN canchas c ON r.cancha_id = c.id 
          WHERE r.usuario_id = ? 
          ORDER BY r.fecha DESC, r.hora_inicio DESC";

if ($stmt = $conexion->prepare($query)) {
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $reservas = [];
}

// Procesar cancelación si viene por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelar_reserva'])) {
    $reserva_id = $_POST['reserva_id'];
    
    $query_update = "UPDATE reservas SET estado = 'cancelada' WHERE id = ? AND usuario_id = ?";
    if ($stmt = $conexion->prepare($query_update)) {
        $stmt->bind_param('ii', $reserva_id, $usuario_id);
        if ($stmt->execute()) {
            $mensaje = "Reserva cancelada correctamente";
            header("Refresh:0");
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - FutbolSebasPro</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="reserva.css">
</head>
<body>

<header class="header">
    <div class="header-container">
        <div class="logo">
            <img src="https://www.jetpunk.com//img/user-img/51/51cb1ce904-235.webp" alt="Logo">
            <span>FutbolSebasPro</span>
        </div>
        <nav class="menu">
            <a href="index.html">Inicio</a>
            <a href="canchas.php">Canchas</a>
            <a href="mis-reservas.php" class="active">Mis Reservas</a>
            <a href="salir.php">Salir</a>
        </nav>
        <div class="acciones">
            <span class="usuario-info">Hola, <b><?= htmlspecialchars($usuario) ?></b></span>
        </div>
    </div>
</header>

<section class="mis-reservas-container">
    <div class="mis-reservas-header">
        <h1>Mis Reservas</h1>
        <a href="canchas.php" class="btn-nueva-reserva">+ Nueva Reserva</a>
    </div>

    <?php if (empty($reservas)): ?>
        <div class="sin-reservas">
            <p>No tienes reservas aún</p>
            <a href="canchas.php" class="btn-primary">Hacer mi primera reserva</a>
        </div>
    <?php else: ?>
        <div class="reservas-grid">
            <?php foreach ($reservas as $reserva): ?>
                <div class="reserva-card estado-<?= $reserva['estado'] ?>">
                    <div class="reserva-header">
                        <h3><?= htmlspecialchars($reserva['cancha_nombre']) ?></h3>
                        <span class="estado-badge"><?= ucfirst($reserva['estado']) ?></span>
                    </div>

                    <div class="reserva-detalles">
                        <div class="detalle">
                            <span class="label">Fecha:</span>
                            <span><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></span>
                        </div>
                        <div class="detalle">
                            <span class="label">Hora:</span>
                            <span><?= substr($reserva['hora_inicio'], 0, 5) ?> - <?= substr($reserva['hora_fin'], 0, 5) ?></span>
                        </div>
                        <div class="detalle">
                            <span class="label">Personas:</span>
                            <span><?= $reserva['cantidad_personas'] ?></span>
                        </div>
                        <div class="detalle">
                            <span class="label">Total:</span>
                            <span class="precio">$<?= number_format($reserva['total'], 2) ?></span>
                        </div>
                    </div>

                    <?php if ($reserva['estado'] == 'confirmada' && strtotime($reserva['fecha']) > time()): ?>
                        <form method="POST" class="form-cancelar">
                            <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                            <button type="submit" name="cancelar_reserva" class="btn-cancelar" 
                                onclick="return confirm('¿Estás seguro de que deseas cancelar esta reserva?')">
                                Cancelar Reserva
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<footer class="footer">
    <p>© 2025 FutbolSebasPro - Todos los derechos reservados</p>
</footer>

</body>
</html>
