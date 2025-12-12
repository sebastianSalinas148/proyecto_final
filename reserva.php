<?php
session_start();


if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';


$cancha_id = isset($_GET['cancha_id']) ? $_GET['cancha_id'] : null;
$cancha = null;

if ($cancha_id) {
    $query = "SELECT * FROM canchas WHERE id = ?";
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param('i', $cancha_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $cancha = $result->fetch_assoc();
        }
        $stmt->close();
    }
}


$usuario = $_SESSION['usuario'];
$email_usuario = isset($_SESSION['email']) ? $_SESSION['email'] : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cancha_id = $_POST['cancha_id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $cantidad_personas = $_POST['cantidad_personas'];
    
    // Validar que la fecha sea futura
    if (strtotime($fecha) < strtotime('today')) {
        $error = "La fecha debe ser futura";
    } else {
        // Validar que no haya conflicto de horarios
        $query_check = "SELECT * FROM reservas WHERE cancha_id = ? AND fecha = ? AND estado = 'confirmada' AND (
            (hora_inicio < ? AND hora_fin > ?) OR
            (hora_inicio < ? AND hora_fin > ?)
        )";
        
        if ($stmt = $conexion->prepare($query_check)) {
            $stmt->bind_param('isssss', $cancha_id, $fecha, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Ese horario ya está reservado. Elige otro";
            } else {
               
                $inicio = strtotime($hora_inicio);
                $fin = strtotime($hora_fin);
                $duracion_minutos = ($fin - $inicio) / 60;
                $duracion_horas = $duracion_minutos / 60;
                
                if ($duracion_horas <= 0) {
                    $error = "La hora de fin debe ser posterior a la de inicio";
                } else {
                    $total = $duracion_horas * $cancha['precio_hora'];
                    
                  
                    $query_insert = "INSERT INTO reservas (usuario_id, cancha_id, fecha, hora_inicio, hora_fin, cantidad_personas, total, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmada', NOW())";
                    
                    if ($stmt_insert = $conexion->prepare($query_insert)) {
                        $usuario_id = $_SESSION['usuario_id'];
                        $stmt_insert->bind_param('iisssid', $usuario_id, $cancha_id, $fecha, $hora_inicio, $hora_fin, $cantidad_personas, $total);
                        
                        if ($stmt_insert->execute()) {
                            $reserva_id = $stmt_insert->insert_id;
                            $stmt_insert->close();
                            header("Location: confirmacion-reserva.php?id=$reserva_id");
                            exit();
                        } else {
                            $error = "Error al guardar la reserva: " . $conexion->error;
                        }
                    }
                }
            }
            $stmt->close();
        }
    }
}


$query_canchas = "SELECT id, nombre, precio_hora FROM canchas WHERE disponible = 1";
$canchas_list = $conexion->query($query_canchas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Reserva - FutbolSebasPro</title>
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
        <div class="acciones">
            <span class="usuario-info">Hola, <b><?= htmlspecialchars($usuario) ?></b></span>
        </div>
    </div>
</header>

<section class="reserva-container">
    <div class="reserva-box">
        <h1>Realizar Reserva</h1>
        <p class="subtitle">Completa los datos para reservar tu cancha favorita</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="reserva.php" class="form-reserva">
            
            <div class="form-group">
                <label for="cancha_id">Selecciona una Cancha</label>
                <select id="cancha_id" name="cancha_id" required onchange="actualizarPrecio(this)">
                    <option value="">-- Elige una cancha --</option>
                    <?php while ($row = $canchas_list->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" data-precio="<?= $row['precio_hora'] ?>" 
                            <?= ($cancha_id == $row['id']) ? 'selected' : '' ?>>
                            <?= $row['nombre'] ?> - $<?= number_format($row['precio_hora'], 2) ?>/hora
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha de Reserva</label>
                <input type="date" id="fecha" name="fecha" required min="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="hora_inicio">Hora de Inicio</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" required>
                </div>
                <div class="form-group">
                    <label for="hora_fin">Hora de Fin</label>
                    <input type="time" id="hora_fin" name="hora_fin" required onchange="calcularTotal()">
                </div>
            </div>

            <div class="form-group">
                <label for="cantidad_personas">Cantidad de Personas</label>
                <input type="number" id="cantidad_personas" name="cantidad_personas" min="2" max="22" required placeholder="Ej: 10">
            </div>

            <div class="resumen-reserva">
                <h3>Resumen de tu Reserva</h3>
                <div class="resumen-item">
                    <span>Duración:</span>
                    <span id="duracion">0 horas</span>
                </div>
                <div class="resumen-item">
                    <span>Precio por hora:</span>
                    <span id="precio-hora">$0.00</span>
                </div>
                <div class="resumen-item total">
                    <span>Total a pagar:</span>
                    <span id="total">$0.00</span>
                </div>
            </div>

            <div class="form-footer">
                <a href="canchas.php" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-reservar">Confirmar Reserva</button>
            </div>

        </form>
    </div>
</section>

<footer class="footer">
    <p>© 2025 FutbolSebasPro - Todos los derechos reservados</p>
</footer>

<script>
let precioHora = 0;

function actualizarPrecio(select) {
    precioHora = parseFloat(select.options[select.selectedIndex].dataset.precio) || 0;
    document.getElementById('precio-hora').textContent = '$' + precioHora.toFixed(2);
    calcularTotal();
}

function calcularTotal() {
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (horaInicio && horaFin) {
        const inicio = new Date('2000-01-01 ' + horaInicio);
        const fin = new Date('2000-01-01 ' + horaFin);
        
        const diferencia = fin - inicio;
        const horas = diferencia / (1000 * 60 * 60);
        
        if (horas > 0) {
            document.getElementById('duracion').textContent = horas.toFixed(1) + ' horas';
            const total = horas * precioHora;
            document.getElementById('total').textContent = '$' + total.toFixed(2);
        }
    }
}


document.getElementById('cancha_id').dispatchEvent(new Event('change'));
</script>

</body>
</html>
