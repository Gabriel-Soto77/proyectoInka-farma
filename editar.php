<?php
include("conexion.php");
include("lib/fotos.php");                         // Función subirFoto()
$paginaActual = "productos";
$tituloPagina = "Editar producto";
verificarSesion();

$id = $_GET['id'] ?? null;                        // ID del producto a editar
if (!$id) {
    header("Location: index.php");
    exit;
}

$error = "";
$stmt = $conexion->prepare("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria");
$stmt->execute();
$categorias = $stmt->fetchAll();

// Cargar los datos actuales del producto
$stmt = $conexion->prepare("SELECT * FROM insumos_medicos WHERE id = :id");
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch();
if (!$producto) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo       = trim($_POST['codigo']);
    $nombre       = trim($_POST['nombre']);
    $precio       = $_POST['precio'];
    $stock        = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];
    $fotoAnterior = $producto['foto'];
    $fotoNueva    = null;

    // Mantener lo escrito en el formulario si hay un error
    $producto['codigo']       = $codigo;
    $producto['nombre']       = $nombre;
    $producto['precio']       = $precio;
    $producto['stock']        = $stock;
    $producto['id_categoria'] = $id_categoria;

    if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $id_categoria == "") {
        $error = "Complete todos los campos obligatorios.";
    } elseif ($precio < 0 || $stock < 0) {
        $error = "El precio y el stock no pueden ser negativos.";
    } else {
        // Si sube una foto nueva (validada por extensión y por contenido real)
        $fotoNueva = subirFoto($_FILES['foto'] ?? null, $error);

        if ($error == "") {
            $fotoFinal = $fotoNueva ?? $fotoAnterior;                  // Si no hay foto nueva, se queda la anterior
            try {
                $sql = "UPDATE insumos_medicos
                        SET codigo = :codigo, nombre = :nombre, precio = :precio, stock = :stock,
                            id_categoria = :id_categoria, foto = :foto
                        WHERE id = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ':codigo'       => $codigo,
                    ':nombre'       => $nombre,
                    ':precio'       => $precio,
                    ':stock'        => $stock,
                    ':id_categoria' => $id_categoria,
                    ':foto'         => $fotoFinal,
                    ':id'           => $id
                ]);

                // Borrar la foto anterior del servidor si se reemplazó
                if ($fotoNueva != null && $fotoAnterior != null && file_exists("uploads/" . $fotoAnterior)) {
                    unlink("uploads/" . $fotoAnterior);
                }
                header("Location: index.php?msg=editado");
                exit;
            } catch (PDOException $e) {
                if ($fotoNueva != null) {
                    unlink("uploads/" . $fotoNueva);
                }
                if ($e->getCode() == 23000) {
                    $error = "Ya existe un producto con ese código.";
                } else {
                    error_log("editar.php: " . $e->getMessage());
                    $error = "No se pudo actualizar el producto. Intente nuevamente.";
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
    <title>Editar Producto - Inkafarma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
<?php include("menu.php"); ?>

<div class="contenedor-pagina">
    <div class="panel mx-auto" style="max-width: 560px;">
        <h3 class="mb-3">Editar Producto #<?php echo $id; ?></h3>

        <?php if ($error != "") { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form action="editar.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label">Código:</label>
                <input type="text" name="codigo" class="form-control" maxlength="20" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Nombre y presentación:</label>
                <input type="text" name="nombre" class="form-control" maxlength="150" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div class="row">
                <div class="col-6 mb-2">
                    <label class="form-label">Precio (S/):</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                </div>
                <div class="col-6 mb-2">
                    <label class="form-label">Stock:</label>
                    <input type="number" min="0" name="stock" class="form-control" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Categoría:</label>
                <select name="id_categoria" class="form-select" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>" <?php if ($cat['id_categoria'] == $producto['id_categoria']) { echo 'selected'; } ?>>
                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto actual:</label><br>
                <?php if ($producto['foto'] != null) { ?>
                    <img src="uploads/<?php echo $producto['foto']; ?>" class="miniatura mb-2" alt="foto">
                <?php } else { ?>
                    <span class="sin-foto mb-2">Sin foto</span>
                <?php } ?>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <div class="form-text">Deje vacío para mantener la foto actual.</div>
            </div>
            <button type="submit" class="btn btn-warning w-100">Actualizar Producto</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
        </form>
    </div>
</div>
<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>