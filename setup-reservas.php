<?php
session_start();

// Solo accesible para admin
if (!isset($_SESSION['usuario'])) {
    echo "Acceso denegado: No estás autenticado";
    exit();
}


$rol = strtolower($_SESSION['rol'] ?? '');
if ($rol !== 'administrador' && $rol !== 'admin') {
    echo "Acceso denegado: Solo administradores pueden acceder. Tu rol es: " . htmlspecialchars($_SESSION['rol'] ?? 'sin rol');
    exit();
}

include 'conexion.php';

$mensajes = [];
$errores = [];


$query_canchas = "CREATE TABLE IF NOT EXISTS canchas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50),
    capacidad INT,
    precio_hora DECIMAL(10, 2) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    imagen VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conexion->query($query_canchas)) {
    $mensajes[] = "✓ Tabla 'canchas' creada/verificada correctamente";
} else {
    $errores[] = "Error en tabla canchas: " . $conexion->error;
}


$query_reservas = "CREATE TABLE IF NOT EXISTS reservas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    cancha_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    cantidad_personas INT,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'confirmada',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY(cancha_id) REFERENCES canchas(id) ON DELETE CASCADE,
    INDEX(usuario_id),
    INDEX(cancha_id),
    INDEX(fecha)
)";

if ($conexion->query($query_reservas)) {
    $mensajes[] = "✓ Tabla 'reservas' creada/verificada correctamente";
} else {
    $errores[] = "Error en tabla reservas: " . $conexion->error;
}


$query_insert = "INSERT INTO canchas (nombre, descripcion, tipo, capacidad, precio_hora, disponible) 
VALUES 
('Cancha Principal', 'Cancha de fútbol 11 con superficie de pasto sintético de alta calidad', 'Fútbol 11', 22, 50.00, TRUE),
('Cancha Sintética A', 'Cancha de fútbol 7 con superficie sintética profesional', 'Fútbol 7', 14, 35.00, TRUE),
('Cancha Indoor', 'Cancha de fútbol 5 techada con piso de madera', 'Fútbol 5', 10, 25.00, TRUE),
('Cancha de Entrenamiento', 'Cancha auxiliar para entrenamientos y práctica', 'Fútbol 7', 14, 30.00, TRUE)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre)";

if ($conexion->query($query_insert)) {
    $mensajes[] = "✓ Datos de canchas insertados correctamente";
} else {
   
    if (strpos($conexion->error, 'Duplicate') !== false) {
        $mensajes[] = "✓ Datos de canchas ya existen";
    } else {
        $errores[] = "Error al insertar canchas: " . $conexion->error;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Setup Sistema de Reservas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a4d1a;
        }
        .mensaje {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #4caf50;
            background: #e8f5e9;
            color: #2e7d32;
        }
        .error {
            border-left-color: #c62828;
            background: #ffebee;
            color: #c62828;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #1a4d1a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }
        .btn:hover {
            background: #0d3d0d;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>⚙️ Setup Sistema de Reservas</h1>
    <p>Configurando las tablas necesarias...</p>

    <?php if (!empty($mensajes)): ?>
        <h3>✓ Operaciones exitosas:</h3>
        <?php foreach ($mensajes as $msg): ?>
            <div class="mensaje"><?= $msg ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <h3>✗ Errores:</h3>
        <?php foreach ($errores as $err): ?>
            <div class="mensaje error"><?= $err ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="dashboard.php" class="btn">Volver al Dashboard</a>
</div>

</body>
</html>
