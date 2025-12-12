<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$reserva_id = isset($_GET['id']) ? $_GET['id'] : null;
$reserva = null;

if ($reserva_id) {
    $query = "SELECT r.*, c.nombre as cancha_nombre, c.precio_hora, u.nombre as usuario_nombre, u.email 
              FROM reservas r 
              JOIN canchas c ON r.cancha_id = c.id 
              JOIN usuarios u ON r.usuario_id = u.id 
              WHERE r.id = ? AND r.usuario_id = ?";
    
    if ($stmt = $conexion->prepare($query)) {
        $usuario_id = $_SESSION['usuario_id'];
        $stmt->bind_param('ii', $reserva_id, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $reserva = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if (!$reserva) {
    echo "Reserva no encontrada";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva - FutbolSebasPro</title>
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
            <a href="mis-reservas.php">Mis Reservas</a>
            <a href="salir.php">Salir</a>
        </nav>
    </div>
</header>

<section class="confirmacion-container">
    <div class="confirmacion-box">
        <div class="success-icon">✓</div>
        <h1>¡Reserva Confirmada!</h1>
        <p class="subtitle">Tu reserva ha sido registrada exitosamente</p>

        <div class="confirmacion-detalles">
            <div class="detalle-item">
                <span class="label">Número de Reserva:</span>
                <span class="valor">#<?= str_pad($reserva['id'], 5, '0', STR_PAD_LEFT) ?></span>
            </div>

            <div class="detalle-item">
                <span class="label">Cancha:</span>
                <span class="valor"><?= htmlspecialchars($reserva['cancha_nombre']) ?></span>
            </div>

            <div class="detalle-item">
                <span class="label">Fecha:</span>
                <span class="valor"><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></span>
            </div>

            <div class="detalle-item">
                <span class="label">Horario:</span>
                <span class="valor"><?= substr($reserva['hora_inicio'], 0, 5) ?> - <?= substr($reserva['hora_fin'], 0, 5) ?></span>
            </div>

            <div class="detalle-item">
                <span class="label">Cantidad de Personas:</span>
                <span class="valor"><?= $reserva['cantidad_personas'] ?> personas</span>
            </div>

            <div class="detalle-item destacado">
                <span class="label">Total Pagado:</span>
                <span class="valor">$<?= number_format($reserva['total'], 2) ?></span>
            </div>

            <div class="detalle-item">
                <span class="label">Estado:</span>
                <span class="valor estado-confirmada">Confirmada</span>
            </div>
        </div>

        <div class="acciones-confirmacion">
            <a href="mis-reservas.php" class="btn-primary">Ver mis Reservas</a>
            <a href="canchas.php" class="btn-secondary">Hacer otra Reserva</a>
        </div>

        <div class="nota-importante">
            <strong>Nota:</strong> Se ha enviado un correo de confirmación a <strong><?= htmlspecialchars($reserva['email']) ?></strong> con todos los detalles de tu reserva.
        </div>
    </div>
</section>

<footer class="footer">
    <p>© 2025 FutbolSebasPro - Todos los derechos reservados</p>
</footer>

</body>
</html>
