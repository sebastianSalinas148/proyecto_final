<?php
session_start();
include 'conexion.php';

// Redirigir si no está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener filtro de búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? '';


$query = "SELECT * FROM canchas WHERE disponible = 1";
if (!empty($busqueda)) {
    $busqueda_safe = $conexion->real_escape_string($busqueda);
    $query .= " AND (nombre LIKE '%$busqueda_safe%' OR descripcion LIKE '%$busqueda_safe%')";
}
if (!empty($filtro_tipo)) {
    $filtro_tipo_safe = $conexion->real_escape_string($filtro_tipo);
    $query .= " AND tipo LIKE '%$filtro_tipo_safe%'";
}
$query .= " ORDER BY nombre";

$result = $conexion->query($query);
$canchas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Obtener tipos únicos para el filtro
$query_tipos = "SELECT DISTINCT tipo FROM canchas WHERE disponible = 1 AND tipo IS NOT NULL ORDER BY tipo";
$result_tipos = $conexion->query($query_tipos);
$tipos = [];
if ($result_tipos) {
    while ($row = $result_tipos->fetch_assoc()) {
        if (!empty($row['tipo'])) {
            $tipos[] = $row['tipo'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestras Canchas - FutbolSebasPro</title>
    <link rel="stylesheet" href="dashboard.css?v=5">
    <link rel="stylesheet" href="reserva.css?v=3">
    <style>
        
        .canchas-header {
            background: linear-gradient(135deg, #1a4d1a 0%, #0d3d0d 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
            border-radius: 0;
        }

        .canchas-header h1 {
            font-size: 2.8em;
            margin: 0 0 15px 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .canchas-header p {
            font-size: 1.15em;
            opacity: 0.95;
            margin: 0;
            font-weight: 300;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .filtros-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 40px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .filtros-form {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 20px;
            align-items: end;
        }

        .filtro-group {
            display: flex;
            flex-direction: column;
        }

        .filtro-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .filtro-group input,
        .filtro-group select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
            background: white;
            font-family: inherit;
        }

        .filtro-group input::placeholder {
            color: #999;
        }

        .filtro-group input:focus,
        .filtro-group select:focus {
            outline: none;
            border-color: #1a4d1a;
            box-shadow: 0 0 0 4px rgba(26, 77, 26, 0.1);
        }

        .filtro-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-filtro {
            background: #1a4d1a;
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-filtro:hover {
            background: #0d3d0d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 77, 26, 0.25);
        }

        .btn-limpiar {
            background: #999;
        }

        .btn-limpiar:hover {
            background: #777;
        }

        .resultados-info {
            color: #555;
            font-size: 1em;
            margin-bottom: 25px;
            padding: 12px 16px;
            background: #f0f8f0;
            border-left: 4px solid #1a4d1a;
            border-radius: 6px;
        }

        .resultados-info strong {
            color: #1a4d1a;
            font-weight: 700;
        }

        .canchas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .cancha-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .cancha-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.15);
        }

        .cancha-imagen-container {
            width: 100%;
            height: 240px;
            background: linear-gradient(135deg, #1a4d1a 0%, #0d3d0d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .cancha-imagen-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cancha-placeholder {
            color: white;
            font-size: 4.5em;
            opacity: 0.8;
        }

        .badge-disponible {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ffc107;
            color: #000;
            padding: 8px 16px;
            border-radius: 24px;
            font-weight: 700;
            font-size: 0.85em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .badge-rating {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .cancha-contenido {
            padding: 28px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .cancha-titulo {
            margin: 0 0 10px 0;
            font-size: 1.5em;
            color: #1a1a1a;
            font-weight: 700;
        }

        .cancha-tipo {
            margin: 0 0 18px 0;
            color: #1a4d1a;
            font-weight: 600;
            font-size: 0.98em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cancha-detalles {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid #eee;
        }

        .detalle-item {
            color: #666;
            font-size: 0.96em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detalle-item strong {
            color: #333;
            font-weight: 600;
        }

        .cancha-descripcion {
            color: #555;
            font-size: 0.96em;
            line-height: 1.6;
            margin-bottom: 22px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cancha-pie {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding-top: 18px;
            border-top: 1px solid #eee;
        }

        .precio-contenedor {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .precio-cantidad {
            font-size: 1.8em;
            font-weight: 800;
            color: #1a4d1a;
        }

        .precio-unidad {
            color: #999;
            font-size: 0.95em;
            font-weight: 500;
        }

        .btn-reservar {
            background: linear-gradient(135deg, #1a4d1a 0%, #0d3d0d 100%);
            color: white;
            border: none;
            padding: 13px 26px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            white-space: nowrap;
            gap: 6px;
            font-size: 0.98em;
        }

        .btn-reservar:hover {
            transform: scale(1.06);
            box-shadow: 0 6px 16px rgba(26, 77, 26, 0.35);
        }

        .btn-reservar::before {
            content: "🗓️";
        }

        .sin-resultados {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 40px;
            background: #f9f9f9;
            border-radius: 12px;
            color: #666;
        }

        .sin-resultados h2 {
            font-size: 1.9em;
            margin: 0 0 12px 0;
            color: #333;
            font-weight: 700;
        }

        .sin-resultados p {
            font-size: 1.05em;
            opacity: 0.85;
            margin: 0;
        }

        .sin-resultados a {
            display: inline-block;
            margin-top: 25px;
            color: #1a4d1a;
            font-weight: 600;
            text-decoration: none;
            font-size: 1.05em;
        }

        .sin-resultados a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .canchas-header h1 {
                font-size: 2em;
            }

            .canchas-header p {
                font-size: 1em;
            }

            .filtros-form {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .filtro-buttons {
                width: 100%;
            }

            .btn-filtro {
                flex: 1;
            }

            .canchas-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cancha-imagen-container {
                height: 200px;
            }

            .cancha-contenido {
                padding: 20px;
            }

            .cancha-pie {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-reservar {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .canchas-header {
                padding: 40px 15px;
            }

            .canchas-header h1 {
                font-size: 1.6em;
            }

            .filtros-section {
                padding: 20px;
                margin-bottom: 25px;
            }

            .cancha-titulo {
                font-size: 1.25em;
            }

            .precio-cantidad {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>⚽ FutbolSebasPro</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="item">📊 Dashboard</a>
                <a href="canchas.php" class="item active">⚽ Canchas</a>
                <a href="mis-reservas.php" class="item">📅 Mis Reservas</a>
                <a href="logout.php" class="item">🚪 Cerrar Sesión</a>
            </nav>
        </aside>

        
        <div class="content">
            
            <div class="canchas-header">
                <h1>⚽ Nuestras Canchas</h1>
                <p>Encuentra la cancha perfecta para tu partido</p>
            </div>

            <div class="container">
                
                <div class="filtros-section">
                    <form method="GET" class="filtros-form">
                        <div class="filtro-group">
                            <label for="busqueda">🔍 Buscar Cancha</label>
                            <input type="text" id="busqueda" name="busqueda" 
                                   placeholder="Ej: Principal, Sintética..." 
                                   value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        <div class="filtro-group">
                            <label for="tipo">🏟️ Tipo de Cancha</label>
                            <select id="tipo" name="tipo">
                                <option value="">Todos los tipos</option>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?php echo htmlspecialchars($t); ?>" 
                                            <?php echo ($filtro_tipo === $t) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filtro-buttons">
                            <button type="submit" class="btn-filtro">🔎 Buscar</button>
                            <a href="canchas.php" class="btn-filtro btn-limpiar" style="text-decoration: none; display: flex; align-items: center; justify-content: center; padding: 12px 20px;">✕</a>
                        </div>
                    </form>
                </div>

                
                <?php if (!empty($busqueda) || !empty($filtro_tipo)): ?>
                    <div class="resultados-info">
                         Se encontraron <strong><?php echo count($canchas); ?></strong> cancha<?php echo count($canchas) !== 1 ? 's' : ''; ?>
                        <?php if (!empty($busqueda)) echo " que coinciden con \"<strong>" . htmlspecialchars($busqueda) . "</strong>\""; ?>
                        <?php if (!empty($filtro_tipo)) echo " de tipo <strong>" . htmlspecialchars($filtro_tipo) . "</strong>"; ?>
                    </div>
                <?php endif; ?>

                
                <div class="canchas-grid">
                    <?php if (count($canchas) > 0): ?>
                        <?php foreach ($canchas as $cancha): ?>
                            <div class="cancha-card">
                                
                                <div class="cancha-imagen-container">
                                    <?php if (!empty($cancha['imagen'])): ?>
                                        <img src="<?php echo htmlspecialchars($cancha['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($cancha['nombre']); ?>">
                                    <?php else: ?>
                                        <div class="cancha-placeholder">⚽</div>
                                    <?php endif; ?>
                                    <div class="badge-rating">⭐ 4.9</div>
                                    <div class="badge-disponible">Disponible</div>
                                </div>

                                
                                <div class="cancha-contenido">
                                    <h3 class="cancha-titulo"><?php echo htmlspecialchars($cancha['nombre']); ?></h3>
                                    <p class="cancha-tipo">
                                        ⚽ <?php echo htmlspecialchars($cancha['tipo'] ?? 'Fútbol'); ?>
                                    </p>
                                    
                                    <div class="cancha-detalles">
                                        <span class="detalle-item">
                                            👥 Capacidad: <strong><?php echo $cancha['capacidad']; ?> jugadores</strong>
                                        </span>
                                        <span class="detalle-item">
                                            📏 Superficie: <strong>Premium</strong>
                                        </span>
                                    </div>

                                    <p class="cancha-descripcion">
                                        <?php echo htmlspecialchars($cancha['descripcion'] ?? 'Cancha de fútbol de calidad premium con excelentes instalaciones'); ?>
                                    </p>

                                    <div class="cancha-pie">
                                        <div class="precio-contenedor">
                                            <span class="precio-cantidad">$<?php echo number_format($cancha['precio_hora'], 0); ?></span>
                                            <span class="precio-unidad">/ hora</span>
                                        </div>
                                        <a href="reserva.php?cancha_id=<?php echo $cancha['id']; ?>" class="btn-reservar">
                                            Reservar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="sin-resultados">
                            <h2> Sin resultados</h2>
                            <p>No encontramos canchas que coincidan con tu búsqueda.</p>
                            <a href="canchas.php">← Volver a todas las canchas</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
