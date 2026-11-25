<?php
include("conexion.php");
$paginaActual = "reportes";
$tituloPagina = "Reportes del inventario";
verificarSesion();

$porPagina = 5;                                   // Filas por página en el listado
$pagina    = (int)($_GET['pagina'] ?? 1);         // Página actual
if ($pagina < 1) {
    $pagina = 1;
}

try {
    // Resumen por categoría: LEFT JOIN + GROUP BY + COUNT, SUM y AVG
    $sql1 = "SELECT c.nombre_categoria,
                    COUNT(i.id) AS cantidad_productos,
                    IFNULL(SUM(i.stock), 0) AS total_stock,
                    IFNULL(SUM(i.precio * i.stock), 0) AS valor_inventario,
                    IFNULL(AVG(i.precio), 0) AS precio_promedio
             FROM categorias c
             LEFT JOIN insumos_medicos i ON c.id_categoria = i.id_categoria
             GROUP BY c.id_categoria, c.nombre_categoria";
    $stmt = $conexion->prepare($sql1);
    $stmt->execute();
    $resumen = $stmt->fetchAll();

    // Listado general con INNER JOIN, paginado con LIMIT y OFFSET
    $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM insumos_medicos");
    $stmt->execute();
    $totalListado = $stmt->fetch()['total'];
    $totalPaginas = (int)ceil($totalListado / $porPagina);
    if ($totalPaginas > 0 && $pagina > $totalPaginas) {
        $pagina = $totalPaginas;
    }
    $offset = ($pagina - 1) * $porPagina;

    $sql2 = "SELECT i.codigo, i.nombre, c.nombre_categoria, i.precio, i.stock
             FROM insumos_medicos i
             INNER JOIN categorias c ON i.id_categoria = c.id_categoria
             ORDER BY c.nombre_categoria ASC, i.nombre ASC
             LIMIT :limite OFFSET :desplazamiento";
    $stmt = $conexion->prepare($sql2);
    $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':desplazamiento', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $listado = $stmt->fetchAll();

    // Productos con stock crítico (menor a 20)
    $sql3 = "SELECT i.codigo, i.nombre, c.nombre_categoria, i.stock
             FROM insumos_medicos i
             INNER JOIN categorias c ON i.id_categoria = c.id_categoria
             WHERE i.stock < :minimo
             ORDER BY i.stock ASC";
    $stmt = $conexion->prepare($sql3);
    $stmt->execute([':minimo' => 20]);
    $criticos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("reportes.php: " . $e->getMessage());
    die("No se pudieron generar los reportes. Intente nuevamente más tarde.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Inkafarma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<?php include("menu.php"); ?>

<div class="contenedor-pagina">
    <div class="panel">
        <h2 class="mb-3">Reportes del Inventario</h2>

        <h5>1. Resumen por categoría</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="encabezado-arena text-center">
                    <tr>
                        <th>Categoría</th>
                        <th>Productos</th>
                        <th>Stock total</th>
                        <th>Valor del inventario</th>
                        <th>Precio promedio</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($resumen as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['nombre_categoria']); ?></td>
                        <td class="text-center"><?php echo $fila['cantidad_productos']; ?></td>
                        <td class="text-center"><?php echo $fila['total_stock']; ?></td>
                        <td class="text-end">S/ <?php echo number_format($fila['valor_inventario'], 2); ?></td>
                        <td class="text-end">S/ <?php echo number_format($fila['precio_promedio'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h5 id="listado">2. Listado de productos con su categoría (INNER JOIN)</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="encabezado-arena text-center">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($listado as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre_categoria']); ?></td>
                        <td class="text-end">S/ <?php echo number_format($fila['precio'], 2); ?></td>
                        <td class="text-center"><?php echo $fila['stock']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación del listado -->
        <nav class="mb-4">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?php if ($pagina <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="reportes.php?pagina=<?php echo $pagina - 1; ?>#listado">Anterior</a>
                </li>
                <?php for ($p = 1; $p <= $totalPaginas; $p++) { ?>
                    <li class="page-item <?php if ($p == $pagina) { echo 'active'; } ?>">
                        <a class="page-link" href="reportes.php?pagina=<?php echo $p; ?>#listado"><?php echo $p; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php if ($pagina >= $totalPaginas) { echo 'disabled'; } ?>">
                    <a class="page-link" href="reportes.php?pagina=<?php echo $pagina + 1; ?>#listado">Siguiente</a>
                </li>
            </ul>
        </nav>

        <h5>3. Alertas de stock crítico</h5>
        <?php if (count($criticos) == 0) { ?>
            <div class="alert alert-success">No hay productos con stock crítico.</div>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="encabezado-arena text-center">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($criticos as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_categoria']); ?></td>
                            <td class="text-center"><span class="badge bg-danger"><?php echo $fila['stock']; ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</div>
<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>