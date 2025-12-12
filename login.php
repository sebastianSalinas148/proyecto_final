<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FutbolPro</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="overlay"></div>

    <div class="login-box">
        <h2>Iniciar Sesión</h2>

    
        <?php if (isset($_GET['error'])) { ?>
            <p style="color:red; text-align:center; margin-bottom:10px;">
                 Usuario o contraseña incorrectos
            </p>
        <?php } ?>

        <form action="validar.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuario" class="input" required>
            <input type="password" name="clave" placeholder="Contraseña" class="input" required>

            <button type="submit" class="btn-ingresar">Ingresar</button>
        </form>

        <a href="index.html" class="volver">← Volver al inicio</a>

    </div>

</body>
</html>

