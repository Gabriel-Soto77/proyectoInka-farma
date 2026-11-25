<?php
include("conexion.php");
$paginaActual = "productos";
$tituloPagina = "Gestión de productos";                          // Sesión + conexión PDO
verificarSesion();                                // Solo entra quien inició sesión

$buscar    = $_GET['buscar'] ?? "";               // Texto de búsqueda
$porPagina = 5;                                   // Productos por página
$pagina    = (int)($_GET['pagina'] ?? 1);         // Página actual
if ($pagina < 1) {
    $pagina = 1;
}
$like   = "%" . $buscar . "%";                    // Patrón para LIKE

try {
    // Tarjetas de resumen (todas con sentencias preparadas)
    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM insumos_medicos");
    $stmt->execute();
    $resTotal = $stmt->fetch();

    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM insumos_medicos WHERE stock < :minimo");
    $stmt->execute([':minimo' => 20]);
    $resCritico = $stmt->fetch();

    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM categorias");
    $stmt->execute();
    $resCat = $stmt->fetch();

    // Cantidad de productos que coinciden con la búsqueda
    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM insumos_medicos WHERE nombre LIKE :b1 OR codigo LIKE :b2");
    $stmt->execute([':b1' => $like, ':b2' => $like]);
    $totalFiltrado = $stmt->fetch()['total'];
    $totalPaginas  = (int)ceil($totalFiltrado / $porPagina);

    // Si la página pedida no existe, se usa la última
    if ($totalPaginas > 0 && $pagina > $totalPaginas) {
        $pagina = $totalPaginas;
    }
    $offset = ($pagina - 1) * $porPagina;         // Desde qué fila empezar (OFFSET)

    // Lista con INNER JOIN, LIMIT y OFFSET
    $sql = "SELECT i.id, i.codigo, i.nombre, i.precio, i.stock, i.foto, c.nombre_categoria
            FROM insumos_medicos i
            INNER JOIN categorias c ON i.id_categoria = c.id_categoria
            WHERE i.nombre LIKE :b1 OR i.codigo LIKE :b2
            ORDER BY i.id DESC
            LIMIT :limite OFFSET :desplazamiento";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':b1', $like, PDO::PARAM_STR);
    $stmt->bindParam(':b2', $like, PDO::PARAM_STR);
    $stmt->bindParam(':limite', $porPagina, PDO::PARAM_INT);
    $stmt->bindParam(':desplazamiento', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $filas = $stmt->fetchAll();                   // Arreglo con todas las filas
} catch (PDOException $e) {
    error_log("index.php: " . $e->getMessage());
    die("No se pudo cargar el listado. Intente nuevamente más tarde.");
}

// Mensaje que viene desde agregar, editar o eliminar
$msg = $_GET['msg'] ?? "";
$mensaje = "";
switch ($msg) {
    case "agregado":
        $mensaje = "Producto registrado correctamente.";
        break;
    case "editado":
        $mensaje = "Producto actualizado correctamente.";
        break;
    case "eliminado":
        $mensaje = "Producto eliminado correctamente.";
        break;
    default:
        $mensaje = "";
        break;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Inkafarma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<?php include("menu.php"); ?>

<div class="contenedor-pagina">
    <div class="panel">

        <?php if ($mensaje != "") { ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php } ?>

        <!-- Tarjetas de resumen -->
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-4">
                <div class="tarjeta-resumen p-3">
                    <h6>Total de productos</h6>
                    <h2><?php echo $resTotal['total']; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tarjeta-resumen p-3">
                    <h6>Stock crítico (menor a 20)</h6>
                    <h2 class="text-danger"><?php echo $resCritico['total']; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tarjeta-resumen p-3">
                    <h6>Categorías</h6>
                    <h2><?php echo $resCat['total']; ?></h2>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="m-0">Lista de Productos</h2>
            <a href="agregar.php" class="btn btn-success">+ Nuevo Producto</a>
        </div>

        <!-- Búsqueda -->
        <form method="GET" action="index.php" class="row g-2 mb-3">
            <div class="col-md-9">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o código"
                       value="<?php echo htmlspecialchars($buscar); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-verde w-100">Buscar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="encabezado-arena text-center">
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($filas) == 0) { ?>
                    <tr><td colspan="8" class="text-center">No se encontraron productos.</td></tr>
                <?php } ?>

                <?php foreach ($filas as $fila): ?>
                    <tr>
                        <td class="text-center"><?php echo $fila['id']; ?></td>
                        <td class="text-center">
                            <?php if ($fila['foto'] != null) { ?>
                                <img src="uploads/<?php echo $fila['foto']; ?>" class="miniatura" alt="foto">
                            <?php } else { ?>
                                <span class="sin-foto">Sin foto</span>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td class="text-center"><span class="badge text-bg-info"><?php echo htmlspecialchars($fila['nombre_categoria']); ?></span></td>
                        <td class="text-end">S/ <?php echo number_format($fila['precio'], 2); ?></td>
                        <td class="text-center">
                            <?php if ($fila['stock'] < 20) { ?>
                                <span class="badge bg-danger"><?php echo $fila['stock']; ?></span>
                            <?php } else { ?>
                                <span class="badge bg-success"><?php echo $fila['stock']; ?></span>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <a href="editar.php?id=<?php echo $fila['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="eliminar.php" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Deseas eliminar este producto?');">
                                <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav>
            <ul class="pagination justify-content-center mb-0">
                <!-- Botón Anterior -->
                <li class="page-item <?php if ($pagina <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?pagina=<?php echo $pagina - 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Anterior</a>
                </li>

                <?php for ($p = 1; $p <= $totalPaginas; $p++) { ?>
                    <li class="page-item <?php if ($p == $pagina) { echo 'active'; } ?>">
                        <a class="page-link" href="index.php?pagina=<?php echo $p; ?>&buscar=<?php echo urlencode($buscar); ?>"><?php echo $p; ?></a>
                    </li>
                <?php } ?>

                <!-- Botón Siguiente -->
                <li class="page-item <?php if ($pagina >= $totalPaginas) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?pagina=<?php echo $pagina + 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>

    </div>
</div>
<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>