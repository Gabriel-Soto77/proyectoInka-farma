<?php
include("conexion.php");                          // Página PÚBLICA a propósito (portal para los clientes): no usa verificarSesion()
include("lib/personal.php");                      // Arreglo con el personal de la botica

$paginaActual = "catalogo";
$tituloPagina = "Catálogo de la botica";

$personal  = obtenerPersonal();                   // Arreglo multidimensional del personal
$buscar    = $_GET['buscar'] ?? "";
$categoria = $_GET['categoria'] ?? "";
$like      = "%" . $buscar . "%";

try {
    // Todas las consultas usan sentencias preparadas (prepare + execute)
    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM insumos_medicos");
    $stmt->execute();
    $totalProductos = $stmt->fetch()['total'];

    $stmt = $conexion->prepare("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria");
    $stmt->execute();
    $categorias = $stmt->fetchAll();

    // Consulta con INNER JOIN; el filtro de categoría se agrega solo si se eligió
    $sql = "SELECT i.codigo, i.nombre, i.precio, i.stock, i.foto, i.id_categoria, c.nombre_categoria
            FROM insumos_medicos i
            INNER JOIN categorias c ON i.id_categoria = c.id_categoria
            WHERE (i.nombre LIKE :b1 OR i.codigo LIKE :b2)";
    $parametros = [':b1' => $like, ':b2' => $like];

    if ($categoria != "") {
        $sql .= " AND i.id_categoria = :cat";
        $parametros[':cat'] = $categoria;
    }
    $sql .= " ORDER BY i.nombre ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("catalogo.php: " . $e->getMessage());          // El detalle queda en el log, no en pantalla
    die("No se pudo cargar el catálogo. Intente nuevamente más tarde.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Inkafarma Chaclacayo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<?php include("menu.php"); ?>

<div class="contenedor-pagina">

    <!-- Banner de bienvenida -->
    <div class="hero mb-4">
        <div class="row align-items-center g-3">
            <div class="col-xl-8">
                <h1>Botica Inkafarma - Sede Chaclacayo</h1>
                <p class="mb-0">Conoce al equipo que te atiende y revisa el catálogo de medicamentos y productos de cuidado personal.</p>
            </div>
            <div class="col-xl-4">
                <div class="row g-2">
                    <div class="col-6"><div class="dato"><strong><?php echo count($personal); ?></strong>Profesionales</div></div>
                    <div class="col-6"><div class="dato"><strong><?php echo $totalProductos; ?></strong>Productos</div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1: PERSONAL -->
    <div class="panel mb-4" id="equipo">
        <h2 class="titulo-seccion">Nuestro equipo</h2>
        <p class="subtitulo-seccion">Personal que trabaja en la botica</p>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
            <?php foreach ($personal as $p): ?>
                <div class="col">
                    <div class="card tarjeta-persona h-100">
                        <!-- Foto en cuadro -->
                        <img src="<?php echo htmlspecialchars($p['foto']); ?>" class="foto-persona" alt="<?php echo htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']); ?>">
                        <!-- Nombre y datos debajo de la foto -->
                        <div class="card-body text-center">
                            <h6 class="card-title mb-2"><?php echo htmlspecialchars($p['nombres'] . " " . $p['apellidos']); ?></h6>
                            <span class="badge insignia-cargo mb-2"><?php echo htmlspecialchars($p['cargo']); ?></span>
                            <p class="small text-muted mb-0">Área: <?php echo htmlspecialchars($p['area']); ?></p>
                        </div>
                        <div class="card-footer text-center bg-white border-0 pb-3">
                            <a href="ficha.php?id=<?php echo $p['id']; ?>" class="btn btn-verde btn-sm">Ver ficha completa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SECCIÓN 2: MEDICAMENTOS -->
    <div class="panel" id="medicamentos">
        <h2 class="titulo-seccion">Medicamentos y productos</h2>
        <p class="subtitulo-seccion">Catálogo de la botica por categorías</p>

        <form method="GET" action="catalogo.php#medicamentos" class="row g-2 mb-4">
            <div class="col-lg-5">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar producto o código" value="<?php echo htmlspecialchars($buscar); ?>">
            </div>
            <div class="col-lg-3">
                <select name="categoria" class="form-select" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>" <?php if ($cat['id_categoria'] == $categoria) { echo 'selected'; } ?>>
                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <button type="submit" class="btn btn-verde w-100">Filtrar</button>
            </div>
            <div class="col-6 col-lg-2">
                <a href="catalogo.php#medicamentos" class="btn btn-outline-secondary w-100">Limpiar</a>
            </div>
        </form>

        <?php if (count($productos) == 0) { ?>
            <div class="alert alert-warning text-center">No se encontraron productos.</div>
        <?php } ?>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 row-cols-xxl-4 g-3">
            <?php foreach ($productos as $prod): ?>
                <?php
                // Color de la etiqueta según la categoría (estructura switch)
                switch ($prod['id_categoria']) {
                    case 1:
                        $colorCat = "text-bg-info";
                        break;
                    case 2:
                        $colorCat = "text-bg-primary";
                        break;
                    case 3:
                        $colorCat = "text-bg-secondary";
                        break;
                    default:
                        $colorCat = "text-bg-info";
                        break;
                }
                ?>
                <div class="col">
                    <div class="card tarjeta-med h-100">
                        <!-- Cuadro blanco (muestra la foto solo si el producto tiene una subida) -->
                        <div class="caja-producto">
                            <?php if ($prod['foto'] != null) { ?>
                                <img src="uploads/<?php echo htmlspecialchars($prod['foto']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                            <?php } ?>
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title"><?php echo htmlspecialchars($prod['nombre']); ?></h6>
                            <span class="badge <?php echo $colorCat; ?> mb-2"><?php echo htmlspecialchars($prod['nombre_categoria']); ?></span>
                            <p class="precio mb-1">S/ <?php echo number_format($prod['precio'], 2); ?></p>
                            <?php if ($prod['stock'] == 0) { ?>
                                <span class="badge bg-secondary">Agotado</span>
                            <?php } elseif ($prod['stock'] < 20) { ?>
                                <span class="badge bg-danger">Stock bajo</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Disponible</span>
                            <?php } ?>
                        </div>
                        <div class="card-footer text-center small text-muted">Código: <?php echo htmlspecialchars($prod['codigo']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>