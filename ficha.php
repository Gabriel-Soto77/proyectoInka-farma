<?php
include("conexion.php");
$paginaActual = "equipo";
$tituloPagina = "Ficha del personal";                          // Página PÚBLICA a propósito (portal informativo): no usa verificarSesion()
include("lib/personal.php");

$personal = obtenerPersonal();
$id = (int)($_GET['id'] ?? 0);                    // ID de la persona que llega por la URL

// Buscar a la persona dentro del arreglo
$persona = null;
foreach ($personal as $p) {
    if ($p['id'] == $id) {
        $persona = $p;
    }
}

// Si no existe, volver al catálogo
if ($persona == null) {
    header("Location: catalogo.php#equipo");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de <?php echo $persona['nombres']; ?> - Inkafarma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<?php include("menu.php"); ?>

<div class="contenedor-pagina">
    <div class="panel">
        <div class="row g-4">

            <!-- Columna izquierda: foto y datos principales -->
            <div class="col-md-4 text-center">
                <img src="<?php echo htmlspecialchars($persona['foto']); ?>" class="foto-ficha mb-3" alt="<?php echo htmlspecialchars($persona['nombres'] . ' ' . $persona['apellidos']); ?>">
                <h3 class="mb-2"><?php echo $persona['nombres'] . " " . $persona['apellidos']; ?></h3>
                <span class="badge insignia-cargo fs-6 mb-3"><?php echo $persona['cargo']; ?></span>
                <p class="text-muted"><?php echo $persona['descripcion']; ?></p>
            </div>

            <!-- Columna derecha: ficha completa -->
            <div class="col-md-8">
                <h2 class="titulo-seccion mb-3">Ficha completa</h2>
                <table class="table table-striped align-middle">
                    <tbody>
                        <tr><th style="width: 35%;">Área</th><td><?php echo $persona['area']; ?></td></tr>
                        <tr><th>Horario</th><td><?php echo $persona['turno']; ?></td></tr>
                        <tr><th>Correo</th><td><?php echo $persona['correo']; ?></td></tr>
                        <tr><th>Teléfono</th><td><?php echo $persona['telefono']; ?></td></tr>
                        <tr><th>Fecha de ingreso</th><td><?php echo $persona['ingreso']; ?></td></tr>
                        <tr><th>Experiencia</th><td><?php echo $persona['experiencia']; ?></td></tr>
                        <tr><th>Formación</th><td><?php echo $persona['formacion']; ?></td></tr>
                    </tbody>
                </table>

                <h5 class="mt-4">Funciones principales</h5>
                <ul class="list-group mb-4">
                    <?php foreach ($persona['funciones'] as $funcion): ?>
                        <li class="list-group-item"><?php echo $funcion; ?></li>
                    <?php endforeach; ?>
                </ul>

                <a href="catalogo.php#equipo" class="btn btn-verde">&larr; Volver al catálogo</a>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
