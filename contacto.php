<?php
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $asunto = trim($_POST['asunto'] ?? 'Consulta general');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre === '') {
        $errors[] = 'Por favor ingresa tu nombre.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Por favor ingresa un email válido.';
    }
    if ($mensaje === '') {
        $errors[] = 'El mensaje no puede estar vacío.';
    }

    if (empty($errors)) {
        // Evitar inyección de cabeceras
        $safe_name = preg_replace("/([\r\n])+/", ' ', $nombre);
        $safe_email = preg_replace("/([\r\n])+/", ' ', $email);
        $safe_asunto = preg_replace("/([\r\n])+/", ' ', $asunto);

        $subject = "Nuevo mensaje desde Contacto - {$safe_asunto} - FutbolSebasPro";
        $body = "Nombre: {$safe_name}\nEmail: {$safe_email}\nAsunto: {$safe_asunto}\n\nMensaje:\n{$mensaje}\n\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A');
        $headers = "From: {$safe_name} <{$safe_email}>\r\nReply-To: {$safe_email}\r\n";

        $sent = false;
        if (function_exists('mail')) {
            // Intentamos enviar correo; en muchos entornos locales no funcionará
            $sent = mail('contacto@futbolsebaspro.com', $subject, $body, $headers);
        }

        if ($sent) {
            $success = 'Tu mensaje fue enviado correctamente. Te responderemos pronto.';
            
            $_POST = [];
        } else {
            // Fallback: guardar en archivo en `messages para revisión manual
            $dir = __DIR__ . '/messages';
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $file = $dir . '/contact_messages.txt';
            $log = "----\nFecha: " . date('Y-m-d H:i:s') . "\n" . $body . "\n";
            @file_put_contents($file, $log, FILE_APPEND | LOCK_EX);
            $success = 'Tu mensaje se guardó correctamente. En entornos productivos se debe configurar el envío de correo.';
            $_POST = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Contacto - FutbolSebasPro</title>
    <link rel="stylesheet" href="estilos.css?v=6">
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
                <a href="index.html#beneficios">Beneficios</a>
                <a href="canchas.php">Canchas</a>
                <a href="contacto.php" class="active">Contacto</a>
            </nav>

            <div class="acciones">
                <a class="btn-login" href="login.php">Iniciar Sesión</a>
                <a class="btn-registro" href="registro.php">Registrarse</a>
            </div>
        </div>
    </header>

    <main class="page-container">
        <!-- HERO SECTION CONTACTO -->
        <section class="contacto-hero">
            <div class="contacto-hero-overlay"></div>
            <div class="contacto-hero-content">
                <h1> Ponte en Contacto</h1>
                <p>¿Tienes dudas o quieres coordinar una reserva para tu equipo? Nos encantaría escucharte</p>
            </div>
        </section>

        <section class="contacto" id="contacto-page">
            <div class="container contacto-container">
                
                <?php if (!empty($success)): ?>
                    <div class="alert success" style="margin-bottom: 30px;">
                        <span style="font-size: 20px;">✅</span> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert error" style="margin-bottom: 30px;">
                        <strong>❌ Por favor corrige los siguientes errores:</strong>
                        <ul>
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="contacto-grid">
                  
                    <div class="contacto-info">
                        <div class="info-card">
                            <div class="info-icon">📍</div>
                            <h3>Ubicación</h3>
                            <p>Av. Arequipa Parque de los coritos<br>Ciudad Deportiva</p>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">📱</div>
                            <h3>Teléfono</h3>
                            <p><a href="tel:+51912634123">+51 912 824 212</a></p>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">✉️</div>
                            <h3>Email</h3>
                            <p><a href="mailto:contacto@futbolsebaspro.com">contacto@futbolsebaspro.com</a></p>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">⏰</div>
                            <h3>Horario</h3>
                            <p>Lunes a Domingo<br>06:00 - 22:00</p>
                        </div>

                        <div class="redes-sociales">
                            <h4>Síguenos</h4>
                            <div class="social-links">
                                <a href="#" title="Facebook" class="social-btn facebook">f</a>
                                <a href="#" title="Instagram" class="social-btn instagram">📷</a>
                                <a href="#" title="WhatsApp" class="social-btn whatsapp">💬</a>
                                <a href="#" title="Twitter" class="social-btn twitter">𝕏</a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="contacto-form">
                        <h2>Envíanos un Mensaje</h2>
                        <p class="form-subtitle">Completa el formulario y nos pondremos en contacto lo antes posible</p>
                        
                        <form method="POST" action="contacto.php" class="contact-form">
                            <div class="form-group">
                                <label for="nombre_contacto">Nombre Completo *</label>
                                <input 
                                    type="text" 
                                    id="nombre_contacto" 
                                    name="nombre" 
                                    placeholder="Tu nombre"
                                    required 
                                    value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email_contacto">Correo Electrónico *</label>
                                <input 
                                    type="email" 
                                    id="email_contacto" 
                                    name="email" 
                                    placeholder="tu@email.com"
                                    required 
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="asunto_contacto">Asunto</label>
                                <input 
                                    type="text" 
                                    id="asunto_contacto" 
                                    name="asunto" 
                                    placeholder="¿Sobre qué es tu consulta?"
                                    value="<?php echo htmlspecialchars($_POST['asunto'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="mensaje_contacto">Mensaje *</label>
                                <textarea 
                                    id="mensaje_contacto" 
                                    name="mensaje" 
                                    rows="6" 
                                    placeholder="Cuéntanos tu consulta o sugerencia..."
                                    required><?php echo htmlspecialchars($_POST['mensaje'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-submit">
                                    <span>✉️</span> Enviar Mensaje
                                </button>
                                <a href="index.html" class="btn-cancel">Volver al inicio</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>FutbolSebasPro</h4>
                    <p>Empresa de alquiler de canchas de fútbol de calidad profesional.</p>
                </div>
                <div class="footer-section">
                    <h4>Usuario</h4>
                    <p id="footer-usuario">Visitante</p>
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

    <script>
        
        document.getElementById('year').textContent = new Date().getFullYear();
        
       
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const hoy = new Date().toLocaleDateString('es-ES', options);
        document.getElementById('footer-fecha').textContent = hoy.charAt(0).toUpperCase() + hoy.slice(1);
    </script>
</body>
</html>
