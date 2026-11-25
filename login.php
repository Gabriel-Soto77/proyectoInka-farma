<?php
include("conexion.php");                          // Inicia sesión y conecta con PDO

// Si ya inició sesión, lo mandamos al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);           // Usuario escrito en el formulario
    $clave   = $_POST['password'];                // Contraseña escrita

    try {
        // Consulta preparada para evitar inyección SQL
        $stmt = $conexion->prepare("SELECT id, usuario, password, nombre_completo FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        $fila = $stmt->fetch();

        // Verificar que exista el usuario y que la contraseña coincida con el hash
        if ($fila && password_verify($clave, $fila['password'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $fila['id'];                  // Guardar datos en $_SESSION
            $_SESSION['usuario']    = $fila['usuario'];
            $_SESSION['nombre']     = $fila['nombre_completo'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $error = "Error al iniciar sesión: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Inkafarma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<div class="fondo-login">
    <div class="panel" style="width: 100%; max-width: 420px;">
        <div class="text-center mb-3">
            <img src="imagenes/logo_circulo.png" class="logo-login mb-2" alt="Inkafarma">
            <h3 class="mb-0">Iniciar sesión</h3>
            <p class="text-muted mb-0">Acceso para el personal de la botica</p>
        </div>

        <?php if ($error != "") { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario:</label>
                <input type="text" name="usuario" class="form-control form-control-lg" placeholder="admin@inkafarma.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control form-control-lg" required>
            </div>
            <button type="submit" class="btn btn-verde btn-lg w-100">Ingresar</button>
            <a href="catalogo.php" class="btn btn-outline-secondary w-100 mt-2">Volver al catálogo</a>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>