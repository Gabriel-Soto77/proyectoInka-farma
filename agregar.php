<?php
include("conexion.php");
include("lib/fotos.php");                         // Función subirFoto()
$paginaActual = "agregar";
$tituloPagina = "Nuevo producto";
verificarSesion();

$error = "";
$codigo = $nombre = $precio = $stock = $id_categoria = "";

// Categorías para el <select>
$stmt = $conexion->prepare("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria");
$stmt->execute();
$categorias = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo       = trim($_POST['codigo']);
    $nombre       = trim($_POST['nombre']);
    $precio       = $_POST['precio'];
    $stock        = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];
    $nombreFoto   = null;                         // Si no sube foto, queda NULL

    if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $id_categoria == "") {
        $error = "Complete todos los campos obligatorios.";
    } elseif ($precio < 0 || $stock < 0) {
        $error = "El precio y el stock no pueden ser negativos.";
    } else {
        // Subida de la foto (opcional), validada por extensión y por contenido real
        $nombreFoto = subirFoto($_FILES['foto'] ?? null, $error);

        if ($error == "") {
            try {
                $sql = "INSERT INTO insumos_medicos (codigo, nombre, precio, stock, id_categoria, foto)
                        VALUES (:codigo, :nombre, :precio, :stock, :id_categoria, :foto)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ':codigo'       => $codigo,
                    ':nombre'       => $nombre,
                    ':precio'       => $precio,
                    ':stock'        => $stock,
                    ':id_categoria' => $id_categoria,
                    ':foto'         => $nombreFoto
                ]);
                header("Location: index.php?msg=agregado");
                exit;
            } catch (PDOException $e) {
                if ($nombreFoto != null) {
                    unlink("uploads/" . $nombreFoto);                              // No dejar fotos sueltas
                }
                if ($e->getCode() == 23000) {
                    $error = "Ya existe un producto con ese código.";
                } else {
                    error_log("agregar.php: " . $e->getMessage());
                    $error = "No se pudo guardar el producto. Intente nuevamente.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Inkafarma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<?php include("menu.php"); ?>

<div class="contenedor-pagina">
    <div class="panel mx-auto" style="max-width: 560px;">
        <h3 class="mb-3">Nuevo Producto</h3>

        <?php if ($error != "") { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form action="agregar.php" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label">Código:</label>
                <input type="text" name="codigo" class="form-control" maxlength="20" value="<?php echo htmlspecialchars($codigo); ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Nombre y presentación:</label>
                <input type="text" name="nombre" class="form-control" maxlength="150" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>
            <div class="row">
                <div class="col-6 mb-2">
                    <label class="form-label">Precio (S/):</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" value="<?php echo htmlspecialchars($precio); ?>" required>
                </div>
                <div class="col-6 mb-2">
                    <label class="form-label">Stock:</label>
                    <input type="number" min="0" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Categoría:</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>" <?php if ($cat['id_categoria'] == $id_categoria) { echo 'selected'; } ?>>
                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto del producto (opcional):</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary w-100">Guardar Producto</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
        </form>
    </div>
</div>
<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>